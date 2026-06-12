<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    private function company()
    {
        return Auth::user()->company;
    }

    // ── Listagem ───────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Quote::with('customer')
            ->where('company_id', $this->company()->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }

        $quotes = $query->paginate(15)->withQueryString();
        return view('quotes.index', compact('quotes'));
    }

    // ── Formulário criar ──────────────────────────────────────────
    public function create()
    {
        $customers = Customer::where('company_id', $this->company()->id)->orderBy('name')->get();
        $products  = Product::where('company_id', $this->company()->id)->orderBy('name')->get();
        return view('quotes.create', compact('customers', 'products'));
    }

    // ── Salvar novo ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'          => 'nullable|exists:customers,id',
            'valid_until'          => 'nullable|date',
            'discount'             => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string|max:2000',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string',
            'items.*.quantity'     => 'required|numeric|min:0.001',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        $quote = null;

        DB::transaction(function () use ($request, &$quote) {
            $company = $this->company();
            $quote = Quote::create([
                'company_id'  => $company->id,
                'user_id'     => Auth::id(),
                'customer_id' => $request->customer_id,
                'number'      => Quote::nextNumber($company->id),
                'status'      => 'draft',
                'valid_until' => $request->valid_until,
                'discount'    => $request->discount ?? 0,
                'notes'       => $request->notes,
            ]);

            foreach ($request->items as $item) {
                QuoteItem::create([
                    'quote_id'    => $quote->id,
                    'product_id'  => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                ]);
            }

            $quote->recalcTotals();
        });

        AuditLogger::action('quote.created', $quote);
        return redirect()->route('quotes.index')
            ->with('success', 'Orçamento criado com sucesso!');
    }

    // ── Exibir ────────────────────────────────────────────────────
    public function show(Quote $quote)
    {
        $this->authorizeQuote($quote);
        $quote->load('customer', 'items.product');
        return view('quotes.show', compact('quote'));
    }

    // ── Alterar status ────────────────────────────────────────────
    public function updateStatus(Request $request, Quote $quote)
    {
        $this->authorizeQuote($quote);
        $request->validate(['status' => 'required|in:draft,sent,accepted,rejected,expired']);
        $quote->update(['status' => $request->status]);
        AuditLogger::action('quote.status_changed', $quote);
        return back()->with('success', 'Status atualizado!');
    }

    // ── Converter em venda ────────────────────────────────────────
    public function convert(Quote $quote)
    {
        $this->authorizeQuote($quote);

        if ($quote->status === 'converted') {
            return back()->with('error', 'Este orçamento já foi convertido.');
        }

        $companyId = $quote->company_id;

        DB::transaction(function () use ($quote, $companyId) {
            $quote->load('items.product', 'customer');

            // fix: usa withTrashed() para evitar sale_number duplicado com vendas soft-deleted
            $saleNumber   = (Sale::withoutGlobalScope('company')->withTrashed()->where('company_id', $companyId)->max('sale_number') ?? 0) + 1;
            $customerName = $quote->customer?->name ?? 'Consumidor Final';

            $sale = Sale::create([
                'company_id'    => $companyId,
                'sale_number'   => $saleNumber,
                'customer_id'   => $quote->customer_id,
                'customer_name' => $customerName,
                'sale_date'     => now(),
                'status'        => 'concluida',
                'notes'         => 'Convertido do orçamento ' . $quote->number,
                'total'         => $quote->total,
            ]);

            foreach ($quote->items as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->unit_price,
                    'subtotal'   => $item->total,
                ]);

                if ($item->product) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    $before  = $product->quantity;
                    $after   = max(0, $before - $item->quantity);

                    $product->update(['quantity' => $after]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'company_id'      => $companyId,
                        'user_id'         => Auth::id(),
                        'type'            => 'saida',
                        'quantity'        => -$item->quantity,
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'reason'          => 'venda',
                        'notes'           => "Venda #{$saleNumber} — convertida do orçamento {$quote->number}",
                        'source_type'     => Sale::class,
                        'source_id'       => $sale->id,
                    ]);
                }
            }

            $quote->update([
                'status'               => 'converted',
                'converted_to_sale_id' => $sale->id,
            ]);
        });

        AuditLogger::action('quote.converted', $quote);
        return redirect()->route('sales.index')
            ->with('success', 'Orçamento convertido em venda com sucesso!');
    }

    // ── Download PDF ──────────────────────────────────────────────
    public function pdf(Quote $quote)
    {
        $this->authorizeQuote($quote);
        $quote->load('customer', 'items.product', 'company');
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))->setPaper('a4');
        return $pdf->download('orcamento-' . $quote->number . '.pdf');
    }

    // ── Excluir ───────────────────────────────────────────────────
    public function destroy(Quote $quote)
    {
        $this->authorizeQuote($quote);
        if ($quote->status === 'converted') {
            return back()->with('error', 'Não é possível excluir um orçamento já convertido.');
        }
        $quote->delete();
        AuditLogger::action('quote.deleted', $quote);
        return redirect()->route('quotes.index')
            ->with('success', 'Orçamento excluído.');
    }

    // ── Aux ───────────────────────────────────────────────────────
    private function authorizeQuote(Quote $quote): void
    {
        abort_if($quote->company_id !== $this->company()->id, 403);
    }
}
