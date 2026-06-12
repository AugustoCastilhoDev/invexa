<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = SaleReturn::with(['sale', 'user', 'items.product'])
            ->where('company_id', $companyId);

        if ($request->filled('from')) { $query->whereDate('created_at', '>=', $request->from); }
        if ($request->filled('to'))   { $query->whereDate('created_at', '<=', $request->to); }

        $returns       = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $totalReturned = (clone $query)->sum('total');
        $countReturns  = (clone $query)->count();

        return view('returns.index', compact('returns', 'totalReturned', 'countReturns'));
    }

    public function create(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $saleId    = $request->query('sale_id', old('sale_id'));

        $sale = $saleId
            ? Sale::with('items.product')->where('company_id', $companyId)->find($saleId)
            : null;

        $sales = Sale::where('company_id', $companyId)
            ->where('status', 'concluida')
            ->orderByDesc('sale_date')
            ->get(['id', 'customer_name', 'sale_date', 'total']);

        return view('returns.create', compact('sale', 'sales'));
    }

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

        $selectedItems = collect($validated['items'])
            ->filter(fn($i) => !empty($i['selected']));

        if ($selectedItems->isEmpty()) {
            return back()->withInput()->with('error', 'Selecione ao menos um item para devolver.');
        }

        $company = auth()->user()->company;

        try {
            DB::transaction(function () use ($validated, $selectedItems, $companyId, &$return) {
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

                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $product = $product->fresh();
                    $before  = $product->quantity;
                    $after   = $before + (int) $item['quantity'];

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

            // ── Webhook: sale.returned
            $return->load('sale');
            WebhookDispatcher::dispatch($company, 'sale.returned', [
                'id'       => $return->id,
                'sale_id'  => $return->sale_id,
                'total'    => (float) $return->total,
                'reason'   => $return->reason,
                'items'    => $selectedItems->values()->toArray(),
            ]);

            return redirect()->route('returns.index')
                ->with('success', 'Devolução registrada com sucesso. Estoque atualizado.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(SaleReturn $saleReturn)
    {
        abort_if($saleReturn->company_id !== auth()->user()->company_id, 403);
        $saleReturn->load(['sale.items.product', 'items.product', 'user']);
        $return = $saleReturn;
        return view('returns.show', compact('return'));
    }

    public function getSaleItems(Sale $sale)
    {
        abort_if($sale->company_id !== auth()->user()->company_id, 403);
        $sale->load('items.product');

        $alreadyReturned = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->where('sale_id', $sale->id))
            ->selectRaw('product_id, SUM(quantity) as total_returned')
            ->groupBy('product_id')
            ->pluck('total_returned', 'product_id')
            ->map(fn($v) => (int) $v);

        $items = $sale->items
            ->filter(fn($i) => $i->product !== null)
            ->map(fn($i) => [
                'product_id'       => $i->product_id,
                'product_name'     => $i->product->name,
                'quantity'         => $i->quantity,
                'already_returned' => $alreadyReturned->get($i->product_id, 0),
                'available'        => max(0, $i->quantity - $alreadyReturned->get($i->product_id, 0)),
                'price'            => (float) $i->price,
                'subtotal'         => (float) $i->subtotal,
            ])
            ->values();

        return response()->json($items);
    }

    public function items(Sale $sale)
    {
        // fix: valida que a venda pertence à empresa do usuário autenticado
        abort_if($sale->company_id !== auth()->user()->company_id, 403);

        $alreadyReturned = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->where('sale_id', $sale->id))
            ->selectRaw('product_id, SUM(quantity) as total_returned')
            ->groupBy('product_id')
            ->pluck('total_returned', 'product_id')
            ->map(fn($v) => (int) $v);

        $items = $sale->items
            ->filter(fn($i) => $i->product !== null)
            ->map(fn($i) => [
                'product_id'       => $i->product_id,
                'product_name'     => $i->product->name,
                'quantity'         => $i->quantity,
                'already_returned' => $alreadyReturned->get($i->product_id, 0),
                'available'        => max(0, $i->quantity - $alreadyReturned->get($i->product_id, 0)),
                'price'            => (float) $i->price,
                'subtotal'         => (float) $i->subtotal,
            ])
            ->values();

        return response()->json($items);
    }
}
