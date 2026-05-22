<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SaleController extends Controller
{
    /** Prazo (dias) para vencimento apenas em vendas pendentes. */
    private int $receivableDueDays = 30;

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Sale::with(['items.product', 'customer'])->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', $search)
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', $search));
            });
        }

        if ($request->filled('status'))  { $query->where('status', $request->status); }
        if ($request->filled('from'))    { $query->whereDate('sale_date', '>=', Carbon::parse($request->from)->startOfDay()); }
        if ($request->filled('to'))      { $query->whereDate('sale_date', '<=', Carbon::parse($request->to)->endOfDay()); }

        $showTrashed = $request->boolean('trashed') && auth()->user()->hasRole(['admin', 'gerente']);
        if ($showTrashed) { $query->onlyTrashed(); }

        $salesCount     = (clone $query)->count();
        $salesRevenue   = (clone $query)->sum('total');
        $completedSales = (clone $query)->where('status', 'concluida')->count();
        $pendingSales   = (clone $query)->where('status', 'pendente')->count();

        $sales = $query->orderByDesc('sale_date')->orderByDesc('id')->paginate(10);

        return view('sales.index', compact('sales', 'salesCount', 'salesRevenue', 'completedSales', 'pendingSales', 'showTrashed'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'quantity', 'price']);
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'customer_id'        => ['nullable', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'customer_name'      => ['nullable', 'string', 'max:255'],
            'sale_date'          => ['required', 'date'],
            'status'             => ['required', 'in:concluida,pendente,cancelada'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.price'      => ['required', 'numeric', 'min:0'],
        ], [
            'customer_id.exists' => 'Cliente inválido.',
            'sale_date.required' => 'A data da venda é obrigatória.',
            'items.required'     => 'Adicione ao menos um produto.',
            'items.min'          => 'Adicione ao menos um produto.',
        ]);

        if (!empty($validated['customer_id'])) {
            $customer     = Customer::find($validated['customer_id']);
            $customerName = $customer->name;
        } else {
            $customer     = null;
            $customerName = !empty($request->customer_name)
                ? trim($request->customer_name)
                : 'Consumidor Final';
        }

        $saleDateTime = Carbon::parse($validated['sale_date'])->setTimeFrom(now());

        try {
            DB::transaction(function () use ($validated, $companyId, $customer, $customerName, $saleDateTime) {
                $saleNumber = Sale::withoutGlobalScope('company')
                    ->where('company_id', $companyId)
                    ->max('sale_number') + 1;

                $total = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['price']);

                $sale = Sale::create([
                    'company_id'    => $companyId,
                    'sale_number'   => $saleNumber,
                    'customer_id'   => $customer?->id ?? null,
                    'customer_name' => $customerName,
                    'sale_date'     => $saleDateTime,
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para \"{$product->name}\". Disponível: {$product->quantity} un.");
                    }

                    $before = $product->quantity;
                    $after  = $before - $item['quantity'];

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'subtotal'   => $item['quantity'] * $item['price'],
                    ]);

                    $product->update(['quantity' => $after]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'company_id'      => $companyId,
                        'user_id'         => auth()->id(),
                        'type'            => 'saida',
                        'quantity'        => -$item['quantity'],
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'reason'          => 'venda',
                        'notes'           => "Venda #{$sale->sale_number} — {$customerName}",
                        'source_type'     => Sale::class,
                        'source_id'       => $sale->id,
                    ]);
                }

                // Cria Receivable apenas para vendas com cliente cadastrado
                // (sem cliente = Consumidor Final, sem conta a receber)
                if ($sale->customer_id && $sale->status !== 'cancelada') {
                    $isConcluida = $sale->status === 'concluida';

                    Receivable::create([
                        'company_id'      => $companyId,
                        'customer_id'     => $sale->customer_id,
                        'sale_id'         => $sale->id,
                        'category'        => 'venda',
                        'description'     => "Venda #{$sale->sale_number} — {$customerName}",
                        'amount'          => $total,
                        // Se concluída no ato: já recebida integralmente
                        'amount_received' => $isConcluida ? $total : null,
                        'due_date'        => $isConcluida
                            ? $saleDateTime->toDateString()          // vencimento = data da venda
                            : Carbon::parse($saleDateTime)->addDays($this->receivableDueDays)->toDateString(),
                        'received_at'     => $isConcluida ? $saleDateTime : null,
                        'status'          => $isConcluida ? 'recebida' : 'pendente',
                    ]);
                }
            });

            return redirect()->route('sales.index')->with('success', 'Venda registrada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'saleReturns.items', 'receivable']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $companyId = auth()->user()->company_id;
        $sale->load(['items.product', 'customer']);
        $products = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'quantity', 'price']);
        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'customer_id'        => ['nullable', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'customer_name'      => ['nullable', 'string', 'max:255'],
            'sale_date'          => ['required', 'date'],
            'status'             => ['required', 'in:concluida,pendente,cancelada'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.price'      => ['required', 'numeric', 'min:0'],
        ], [
            'sale_date.required' => 'A data da venda é obrigatória.',
            'items.required'     => 'Adicione ao menos um produto.',
        ]);

        if (!empty($validated['customer_id'])) {
            $customer     = Customer::find($validated['customer_id']);
            $customerName = $customer->name;
        } else {
            $customer     = null;
            $customerName = !empty($request->customer_name)
                ? trim($request->customer_name)
                : ($sale->customer_name ?: 'Consumidor Final');
        }

        $existingTime = $sale->sale_date ? $sale->sale_date : now();
        $saleDateTime = Carbon::parse($validated['sale_date'])
            ->setHour($existingTime->hour)
            ->setMinute($existingTime->minute)
            ->setSecond($existingTime->second);

        try {
            DB::transaction(function () use ($sale, $validated, $companyId, $customer, $customerName, $saleDateTime) {
                $sale->load('items.product');

                $alreadyReturned = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->where('sale_id', $sale->id))
                    ->selectRaw('product_id, SUM(quantity) as total_returned')
                    ->groupBy('product_id')
                    ->pluck('total_returned', 'product_id')
                    ->map(fn($v) => (int) $v);

                foreach ($sale->items as $oldItem) {
                    $oldProduct  = Product::lockForUpdate()->find($oldItem->product_id);
                    if (!$oldProduct) continue;
                    $returnedQty = $alreadyReturned->get($oldItem->product_id, 0);
                    $netQty      = max(0, $oldItem->quantity - $returnedQty);
                    if ($netQty === 0) continue;
                    $before = $oldProduct->fresh()->quantity;
                    $after  = $before + $netQty;
                    $oldProduct->update(['quantity' => $after]);
                    StockMovement::create([
                        'product_id' => $oldProduct->id, 'company_id' => $companyId,
                        'user_id' => auth()->id(), 'type' => 'entrada',
                        'quantity' => $netQty, 'quantity_before' => $before, 'quantity_after' => $after,
                        'reason' => 'devolucao', 'notes' => "Estorno da edição da Venda #{$sale->sale_number}",
                        'source_type' => Sale::class, 'source_id' => $sale->id,
                    ]);
                }

                $sale->items()->delete();

                $total = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['price']);

                $sale->update([
                    'customer_id'   => $customer?->id ?? null,
                    'customer_name' => $customerName,
                    'sale_date'     => $saleDateTime,
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para \"{$product->name}\".");
                    }
                    $before = $product->fresh()->quantity;
                    $after  = $before - $item['quantity'];
                    SaleItem::create([
                        'sale_id' => $sale->id, 'product_id' => $product->id,
                        'quantity' => $item['quantity'], 'price' => $item['price'],
                        'subtotal' => $item['quantity'] * $item['price'],
                    ]);
                    $product->update(['quantity' => $after]);
                    StockMovement::create([
                        'product_id' => $product->id, 'company_id' => $companyId,
                        'user_id' => auth()->id(), 'type' => 'saida',
                        'quantity' => -$item['quantity'], 'quantity_before' => $before, 'quantity_after' => $after,
                        'reason' => 'venda', 'notes' => "Venda #{$sale->sale_number} (editada) — {$customerName}",
                        'source_type' => Sale::class, 'source_id' => $sale->id,
                    ]);
                }

                // Sincroniza Receivable conforme o novo status da venda
                $sale->load('receivable');
                $newCustomerId = $customer?->id ?? null;
                $isConcluida   = $validated['status'] === 'concluida';
                $isPendente    = $validated['status'] === 'pendente';
                $isCancelada   = $validated['status'] === 'cancelada';

                if ($newCustomerId && !$isCancelada) {
                    if ($sale->receivable) {
                        // Atualiza receivable existente
                        $sale->receivable->update(array_filter([
                            'amount'          => $total,
                            'customer_id'     => $newCustomerId,
                            'description'     => "Venda #{$sale->sale_number} — {$customerName}",
                            'status'          => $isConcluida ? 'recebida' : 'pendente',
                            'amount_received' => $isConcluida ? $total : null,
                            'received_at'     => $isConcluida ? $saleDateTime : null,
                            'due_date'        => $isPendente
                                ? Carbon::parse($saleDateTime)->addDays($this->receivableDueDays)->toDateString()
                                : $sale->receivable->due_date,
                        ], fn($v) => !is_null($v)));

                        // Se voltou para pendente, limpa campos de recebimento
                        if ($isPendente) {
                            $sale->receivable->update(['amount_received' => null, 'received_at' => null]);
                        }
                    } else {
                        // Cria novo receivable (ex: venda que antes era sem cliente)
                        Receivable::create([
                            'company_id'      => $companyId,
                            'customer_id'     => $newCustomerId,
                            'sale_id'         => $sale->id,
                            'category'        => 'venda',
                            'description'     => "Venda #{$sale->sale_number} — {$customerName}",
                            'amount'          => $total,
                            'amount_received' => $isConcluida ? $total : null,
                            'due_date'        => $isConcluida
                                ? $saleDateTime->toDateString()
                                : Carbon::parse($saleDateTime)->addDays($this->receivableDueDays)->toDateString(),
                            'received_at'     => $isConcluida ? $saleDateTime : null,
                            'status'          => $isConcluida ? 'recebida' : 'pendente',
                        ]);
                    }
                } elseif ($isCancelada && $sale->receivable) {
                    $sale->receivable->update(['status' => 'cancelado']);
                }
            });

            return redirect()->route('sales.index')->with('success', 'Venda atualizada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelada') {
            return back()->with('error', 'Esta venda já está cancelada.');
        }
        $companyId = auth()->user()->company_id;
        try {
            DB::transaction(function () use ($sale, $companyId) {
                $sale->load('items.product', 'receivable');
                $alreadyReturned = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->where('sale_id', $sale->id))
                    ->selectRaw('product_id, SUM(quantity) as total_returned')
                    ->groupBy('product_id')->pluck('total_returned', 'product_id')->map(fn($v) => (int) $v);
                foreach ($sale->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if (!$product) continue;
                    $returnedQty = $alreadyReturned->get($item->product_id, 0);
                    $netQty = max(0, $item->quantity - $returnedQty);
                    if ($netQty === 0) continue;
                    $before = $product->fresh()->quantity; $after = $before + $netQty;
                    $product->update(['quantity' => $after]);
                    StockMovement::create([
                        'product_id' => $product->id, 'company_id' => $companyId, 'user_id' => auth()->id(),
                        'type' => 'entrada', 'quantity' => $netQty, 'quantity_before' => $before, 'quantity_after' => $after,
                        'reason' => 'cancelamento', 'notes' => "Estorno por cancelamento da Venda #{$sale->sale_number}",
                        'source_type' => Sale::class, 'source_id' => $sale->id,
                    ]);
                }
                $sale->update(['status' => 'cancelada']);
                if ($sale->receivable) {
                    $sale->receivable->update(['status' => 'cancelado']);
                }
            });
            return back()->with('success', 'Venda cancelada e estoque estornado com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        $companyId = auth()->user()->company_id;
        if ($sale->status !== 'cancelada') {
            DB::transaction(function () use ($sale, $companyId) {
                $sale->load('items.product');
                $alreadyReturned = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->where('sale_id', $sale->id))
                    ->selectRaw('product_id, SUM(quantity) as total_returned')
                    ->groupBy('product_id')->pluck('total_returned', 'product_id')->map(fn($v) => (int) $v);
                foreach ($sale->items as $item) {
                    $product = Product::find($item->product_id);
                    if (!$product) continue;
                    $returnedQty = $alreadyReturned->get($item->product_id, 0);
                    $netQty = max(0, $item->quantity - $returnedQty);
                    if ($netQty === 0) continue;
                    $before = $product->fresh()->quantity; $after = $before + $netQty;
                    $product->update(['quantity' => $after]);
                    StockMovement::create([
                        'product_id' => $product->id, 'company_id' => $companyId, 'user_id' => auth()->id(),
                        'type' => 'entrada', 'quantity' => $netQty, 'quantity_before' => $before, 'quantity_after' => $after,
                        'reason' => 'devolucao', 'notes' => "Estorno por exclusão da Venda #{$sale->sale_number}",
                        'source_type' => Sale::class, 'source_id' => $sale->id,
                    ]);
                }
            });
        }
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Venda movida para a lixeira com sucesso.');
    }

    public function restore(int $id)
    {
        $sale = Sale::onlyTrashed()->where('company_id', auth()->user()->company_id)->findOrFail($id);
        $sale->restore();
        return redirect()->route('sales.index', ['trashed' => 1])->with('success', 'Venda restaurada com sucesso.');
    }

    public function forceDestroy(int $id)
    {
        $sale = Sale::onlyTrashed()->where('company_id', auth()->user()->company_id)->findOrFail($id);
        $sale->forceDelete();
        return redirect()->route('sales.index', ['trashed' => 1])->with('success', 'Venda excluída permanentemente.');
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['items.product', 'customer']);
        return view('sales.invoice', compact('sale'));
    }

    public function pdf(Sale $sale)
    {
        $sale->load(['items.product', 'customer']);
        $company = auth()->user()->company;

        $pdf = Pdf::loadView('sales.pdf', compact('sale', 'company'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
                'margin_top'           => 20,
                'margin_right'         => 20,
                'margin_bottom'        => 20,
                'margin_left'          => 20,
            ]);

        return $pdf->download('nf-venda-' . $sale->sale_number . '-' . now()->format('Ymd') . '.pdf');
    }
}
