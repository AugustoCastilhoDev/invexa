<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = PurchaseOrder::with('supplier')->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', $search)
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', $search));
            });
        }
        if ($request->filled('supplier')) { $query->where('supplier_id', $request->supplier); }
        if ($request->filled('status'))   { $query->where('status', $request->status); }
        if ($request->filled('from'))     { $query->whereDate('order_date', '>=', $request->from); }
        if ($request->filled('to'))       { $query->whereDate('order_date', '<=', $request->to); }

        $baseQuery     = PurchaseOrder::where('company_id', $companyId);
        $totalOrders   = (clone $baseQuery)->count();
        $pendingOrders = (clone $baseQuery)->where('status', 'pendente')->count();
        $totalValue    = (clone $baseQuery)->whereIn('status', ['pendente', 'enviada'])->sum('total');

        $orders    = $query->orderByDesc('order_date')->paginate(15);
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);

        return view('purchase_orders.index', compact(
            'orders', 'totalOrders', 'pendingOrders', 'totalValue', 'suppliers'
        ));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'price']);
        return view('purchase_orders.form', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'supplier_id'        => ['nullable', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
            'order_date'         => 'required|date',
            'expected_date'      => 'nullable|date',
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.cost'       => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $companyId, &$order) {
            $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['cost']);

            $order = PurchaseOrder::create([
                'company_id'    => $companyId,
                'supplier_id'   => $data['supplier_id'] ?? null,
                'order_date'    => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'status'        => 'pendente',
                'total'         => $total,
            ]);

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'quantity_received'  => 0,
                    'unit_cost'         => $item['cost'],
                    'subtotal'          => $item['quantity'] * $item['cost'],
                ]);
            }
        });

        AuditLogger::action('purchase_order.created', $purchaseOrder);
        return redirect()->route('purchase-orders.index')->with('success', 'Ordem de compra criada com sucesso.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        $purchaseOrder->load(['items.product', 'supplier', 'user']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebida') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Não é possível editar uma ordem já recebida.');
        }
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'price']);
        $purchaseOrder->load('items.product');
        return view('purchase_orders.form', compact('purchase_order', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebida') { abort(403); }

        $companyId = auth()->user()->company_id;
        $data = $request->validate([
            'supplier_id'        => ['nullable', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
            'order_date'         => 'required|date',
            'expected_date'      => 'nullable|date',
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.cost'       => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['cost']);
            $purchaseOrder->update([
                'supplier_id'   => $data['supplier_id'] ?? null,
                'order_date'    => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'total'         => $total,
            ]);
            $purchaseOrder->items()->delete();
            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id'  => $purchaseOrder->id,
                    'product_id'         => $item['product_id'],
                    'quantity'           => $item['quantity'],
                    'quantity_received'  => 0,
                    'unit_cost'          => $item['cost'],
                    'subtotal'           => $item['quantity'] * $item['cost'],
                ]);
            }
        });

        AuditLogger::action('purchase_order.updated', $purchase_order);
        return redirect()->route('purchase-orders.index')->with('success', 'Ordem de compra atualizada com sucesso.');
    }

    public function receive(Request $request, PurchaseOrder $purchase_order)
    {
       
	\Log::info('RECEIVE CHAMADO', ['id' => $purchase_order->id]);
	 $this->authorizeOrder($purchase_order);
        if ($purchase_order->status === 'recebida') {
            return back()->with('error', 'Esta ordem já foi recebida.');
        }

        $companyId = auth()->user()->company_id;
        $company   = auth()->user()->company;

        DB::transaction(function () use ($purchase_order, $companyId) {
            $purchase_order->load('items.product');

            foreach ($purchase_order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (!$product) continue;

                $before = $product->quantity;
                $after  = $before + $item->quantity;
                $product->update(['quantity' => $after]);

                // Atualiza quantity_received no item da OC
                $item->update(['quantity_received' => $item->quantity]);

                StockMovement::create([
                    'product_id'      => $product->id,
                    'company_id'      => $companyId,
                    'user_id'         => auth()->id(),
                    'type'            => 'entrada',
                    'quantity'        => $item->quantity,
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'reason'          => 'compra',
                    'notes'           => "Recebimento da Ordem de Compra #{$purchase_order->id}",
                    'source_type'     => PurchaseOrder::class,
                    'source_id'       => $purchase_order->id,
                ]);
            }

            $purchase_order->update([
                'status'      => 'recebida',
                'received_at' => now(),
            ]);
        });

        WebhookDispatcher::dispatch($company, 'purchase_order.received', [
            'id'          => $purchase_order->id,
            'supplier'    => $purchase_order->supplier?->name,
            'total'       => (float) $purchase_order->total,
            'received_at' => now()->toIso8601String(),
            'items_count' => $purchase_order->items->count(),
        ]);

        return redirect()->route('purchase-orders.show', $purchase_order)
        AuditLogger::action('purchase_order.received', $purchase_order);
            ->with('success', 'Ordem de compra recebida e estoque atualizado com sucesso.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebida') {
            return back()->with('error', 'Não é possível excluir uma ordem já recebida.');
        }
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();
        AuditLogger::action('purchase_order.deleted', $purchase_order);
        return redirect()->route('purchase-orders.index')->with('success', 'Ordem de compra excluída.');
    }

    private function authorizeOrder(PurchaseOrder $order): void
    {
        if ($order->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
