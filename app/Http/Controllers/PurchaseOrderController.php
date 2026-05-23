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
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('from'))   { $query->whereDate('order_date', '>=', $request->from); }
        if ($request->filled('to'))     { $query->whereDate('order_date', '<=', $request->to); }

        $totalPending  = (clone $query)->where('status', 'pendente')->count();
        $totalReceived = (clone $query)->where('status', 'recebido')->count();

        $orders = $query->orderByDesc('order_date')->paginate(15);

        return view('purchase_orders.index', compact('orders', 'totalPending', 'totalReceived'));
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
                    'cost'              => $item['cost'],
                    'subtotal'          => $item['quantity'] * $item['cost'],
                ]);
            }
        });

        return redirect()->route('purchase_orders.index')->with('success', 'Ordem de compra criada com sucesso.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        $purchaseOrder->load(['items.product', 'supplier']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebido') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)
                ->with('error', 'Não é possível editar uma ordem já recebida.');
        }
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'price']);
        $purchaseOrder->load('items.product');
        return view('purchase_orders.form', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebido') { abort(403); }

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

        DB::transaction(function () use ($purchaseOrder, $data, &$updatedOrder) {
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
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'cost'              => $item['cost'],
                    'subtotal'          => $item['quantity'] * $item['cost'],
                ]);
            }
            $updatedOrder = $purchaseOrder;
        });

        return redirect()->route('purchase_orders.index')->with('success', 'Ordem de compra atualizada com sucesso.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebido') {
            return back()->with('error', 'Esta ordem já foi recebida.');
        }

        $companyId = auth()->user()->company_id;
        $company   = auth()->user()->company;

        DB::transaction(function () use ($purchaseOrder, $companyId) {
            $purchaseOrder->load('items.product');

            foreach ($purchaseOrder->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (!$product) continue;

                $before = $product->quantity;
                $after  = $before + $item->quantity;
                $product->update(['quantity' => $after]);

                StockMovement::create([
                    'product_id'      => $product->id,
                    'company_id'      => $companyId,
                    'user_id'         => auth()->id(),
                    'type'            => 'entrada',
                    'quantity'        => $item->quantity,
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'reason'          => 'compra',
                    'notes'           => "Recebimento da Ordem de Compra #{$purchaseOrder->id}",
                    'source_type'     => PurchaseOrder::class,
                    'source_id'       => $purchaseOrder->id,
                ]);
            }

            $purchaseOrder->update([
                'status'      => 'recebido',
                'received_at' => now(),
            ]);
        });

        // Webhook purchase_order.received
        WebhookDispatcher::dispatch($company, 'purchase_order.received', [
            'id'          => $purchaseOrder->id,
            'supplier'    => $purchaseOrder->supplier?->name,
            'total'       => (float) $purchaseOrder->total,
            'received_at' => now()->toIso8601String(),
            'items_count' => $purchaseOrder->items->count(),
        ]);

        return redirect()->route('purchase_orders.index')->with('success', 'Ordem de compra recebida e estoque atualizado.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status === 'recebido') {
            return back()->with('error', 'Não é possível excluir uma ordem já recebida.');
        }
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();
        return redirect()->route('purchase_orders.index')->with('success', 'Ordem de compra excluída.');
    }

    private function authorizeOrder(PurchaseOrder $order): void
    {
        if ($order->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
