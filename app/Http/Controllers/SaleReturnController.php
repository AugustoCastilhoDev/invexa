<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    /** Lista todas as devoluções da empresa **/
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = SaleReturn::with(['sale', 'user', 'items.product'])
            ->where('company_id', $companyId);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $returns       = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $totalReturned = (clone $query)->sum('total');
        $countReturns  = (clone $query)->count();

        return view('returns.index', compact('returns', 'totalReturned', 'countReturns'));
    }

    /** Formulário de nova devolução vinculada a uma venda **/
    public function create(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $saleId    = $request->query('sale_id');

        $sale = $saleId
            ? Sale::with('items.product')
                ->where('company_id', $companyId)
                ->findOrFail($saleId)
            : null;

        $sales = Sale::where('company_id', $companyId)
            ->where('status', 'concluida')
            ->orderByDesc('sale_date')
            ->get(['id', 'customer_name', 'sale_date', 'total']);

        return view('returns.create', compact('sale', 'sales'));
    }

    /** Salva a devolução, atualiza estoque e registra StockMovement **/
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'sale_id'              => ['required', 'exists:sales,id'],
            'reason'               => ['required', 'string'],
            'notes'                => ['nullable', 'string', 'max:500'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
            'items.*.price'        => ['required', 'numeric', 'min:0'],
            'items.*.selected'     => ['nullable'],
        ]);

        // Filtra apenas itens marcados
        $selectedItems = collect($validated['items'])
            ->filter(fn($i) => !empty($i['selected']));

        if ($selectedItems->isEmpty()) {
            return back()->withInput()->with('error', 'Selecione ao menos um item para devolver.');
        }

        try {
            DB::transaction(function () use ($validated, $selectedItems, $companyId) {
                $total = $selectedItems->sum(fn($i) => $i['quantity'] * $i['price']);

                $return = SaleReturn::create([
                    'sale_id'    => $validated['sale_id'],
                    'company_id' => $companyId,
                    'user_id'    => auth()->id(),
                    'total'      => $total,
                    'reason'     => $validated['reason'],
                    'notes'      => $validated['notes'] ?? null,
                ]);

                foreach ($selectedItems as $item) {
                    SaleReturnItem::create([
                        'sale_return_id' => $return->id,
                        'product_id'     => $item['product_id'],
                        'quantity'       => $item['quantity'],
                        'price'          => $item['price'],
                        'subtotal'       => $item['quantity'] * $item['price'],
                    ]);

                    // Recarrega o produto com fresh() para garantir quantity_before correto
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $product = $product->fresh();

                    $before = $product->quantity;
                    $after  = $before + (int) $item['quantity'];

                    $product->update(['quantity' => $after]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'company_id'      => $companyId,
                        'user_id'         => auth()->id(),
                        'type'            => 'entrada',
                        'quantity'        => (int) $item['quantity'],
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'reason'          => 'devolucao',
                        'notes'           => "Devolução #{$return->id} — Venda #{$validated['sale_id']}",
                        'source_type'     => SaleReturn::class,
                        'source_id'       => $return->id,
                    ]);
                }
            });

            return redirect()->route('returns.index')
                ->with('success', 'Devolução registrada com sucesso. Estoque atualizado.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Detalhe de uma devolução **/
    public function show(SaleReturn $return)
    {
        abort_if($return->company_id !== auth()->user()->company_id, 403);
        $return->load(['sale.items.product', 'items.product', 'user']);
        return view('returns.show', compact('return'));
    }

    /** Busca os itens de uma venda via AJAX (rota: returns.items) **/
    public function getItems(Sale $saleReturn)
    {
        abort_if($saleReturn->company_id !== auth()->user()->company_id, 403);
        $saleReturn->load('items.product');
        return response()->json($saleReturn->items->map(fn($i) => [
            'product_id'   => $i->product_id,
            'product_name' => $i->product->name ?? 'Produto removido',
            'quantity'     => $i->quantity,
            'price'        => $i->price,
            'subtotal'     => $i->subtotal,
        ]));
    }
}
