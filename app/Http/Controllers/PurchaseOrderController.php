<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = PurchaseOrder::with(['supplier', 'items.product'])
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->whereHas('supplier', fn($q) => $q->where('name', 'like', $s));
        }
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('order_date')->paginate(10)->withQueryString();

        $totalOrders   = PurchaseOrder::where('company_id', $companyId)->count();
        $pendingOrders = PurchaseOrder::where('company_id', $companyId)->where('status', 'pendente')->count();
        $totalValue    = PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'recebida'])
            ->sum('total');

        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);

        return view('purchase_orders.index', compact(
            'orders', 'totalOrders', 'pendingOrders', 'totalValue', 'suppliers'
        ));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'price']);
        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $validated = $request->validate([
            'supplier_id'        => ['required', 'exists:suppliers,id'],
            'order_date'         => ['required', 'date'],
            'expected_date'      => ['nullable', 'date', 'after_or_equal:order_date'],
            'status'             => ['required', 'in:pendente,enviada,recebida,cancelada'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'  => ['required', 'numeric', 'min:0'],
        ]);
        try {
            DB::transaction(function () use ($validated, $companyId) {
                $total = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['unit_cost']);
                $order = PurchaseOrder::create([
                    'company_id'    => $companyId,
                    'supplier_id'   => $validated['supplier_id'],
                    'order_date'    => Carbon::parse($validated['order_date']),
                    'expected_date' => isset($validated['expected_date']) ? Carbon::parse($validated['expected_date']) : null,
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);
                foreach ($validated['items'] as $item) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'product_id'        => $item['product_id'],
                        'quantity'          => $item['quantity'],
                        'unit_cost'         => $item['unit_cost'],
                        'subtotal'          => $item['quantity'] * $item['unit_cost'],
                    ]);
                }
                if (in_array($validated['status'], ['pendente', 'enviada', 'recebida'])) {
                    $supplier = Supplier::find($validated['supplier_id']);
                    Bill::create([
                        'company_id'        => $companyId,
                        'supplier_id'       => $validated['supplier_id'],
                        'purchase_order_id' => $order->id,
                        'description'       => "OC #{$order->number} — " . ($supplier?->name ?? 'Fornecedor'),
                        'amount'            => $total,
                        'due_date'          => $order->expected_date ?? $order->order_date->addDays(30),
                        'status'            => 'pendente',
                    ]);
                }
                if ($validated['status'] === 'recebida') {
                    $order->load('items');
                    $this->processStockReceipt($order, $companyId);
                }
            });
            return redirect()->route('purchase-orders.index')->with('success', 'Ordem de compra criada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'bill']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get(['id', 'name', 'price']);
        $purchaseOrder->load('items.product');
        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $companyId      = auth()->user()->company_id;
        $previousStatus = $purchaseOrder->status;

        $validated = $request->validate([
            'supplier_id'        => ['required', 'exists:suppliers,id'],
            'order_date'         => ['required', 'date'],
            'expected_date'      => ['nullable', 'date'],
            'status'             => ['required', 'in:pendente,enviada,recebida,cancelada'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'  => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($purchaseOrder, $validated, $companyId, $previousStatus) {
                $total = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['unit_cost']);

                $purchaseOrder->items()->delete();
                $purchaseOrder->update([
                    'supplier_id'   => $validated['supplier_id'],
                    'order_date'    => Carbon::parse($validated['order_date']),
                    'expected_date' => isset($validated['expected_date']) ? Carbon::parse($validated['expected_date']) : null,
                    'status'        => $validated['status'],
                    'notes'         => $validated['notes'] ?? null,
                    'total'         => $total,
                ]);

                foreach ($validated['items'] as $item) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_id'        => $item['product_id'],
                        'quantity'          => $item['quantity'],
                        'unit_cost'         => $item['unit_cost'],
                        'subtotal'          => $item['quantity'] * $item['unit_cost'],
                    ]);
                }

                if ($validated['status'] === 'recebida' && $previousStatus !== 'recebida') {
                    $purchaseOrder->load('items');
                    $this->processStockReceipt($purchaseOrder, $companyId);
                }
            });

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Ordem de compra atualizada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')
            ->with('success', 'Ordem de compra excluída.');
    }

    /**
     * Botão "Receber" na view show.
     * Processa apenas itens ainda pendentes (idempotente).
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        // Verifica se ainda há itens pendentes de recebimento
        $purchaseOrder->load('items');
        $pendingItems = $purchaseOrder->items->filter(fn($i) => $i->quantity_received < $i->quantity);

        if ($pendingItems->isEmpty() && $purchaseOrder->status === 'recebida') {
            return back()->with('info', 'Todos os itens desta OC já foram recebidos.');
        }

        $companyId = auth()->user()->company_id;

        DB::transaction(function () use ($purchaseOrder, $companyId) {
            $this->processStockReceipt($purchaseOrder, $companyId);
            $purchaseOrder->update(['status' => 'recebida']);
        });

        return back()->with('success', 'OC marcada como recebida e estoque atualizado.');
    }

    // ── Método privado central ─────────────────────────────────────────────

    /**
     * Processa entrada de estoque para itens ainda pendentes da OC.
     * Idempotente: pula itens já totalmente recebidos.
     */
    public function processStockReceipt(PurchaseOrder $purchaseOrder, int $companyId): void
    {
        foreach ($purchaseOrder->items as $item) {
            $pending = $item->quantity - $item->quantity_received;
            if ($pending <= 0) continue; // já recebido, pula

            $product = Product::lockForUpdate()->find($item->product_id);
            if (! $product) continue;

            $before = $product->quantity;
            $after  = $before + $pending;

            // Atualiza quantidade e custo do produto
            $product->update([
                'quantity' => $after,
                'cost'     => $item->unit_cost,
            ]);

            // Marca o item como totalmente recebido
            $item->update(['quantity_received' => $item->quantity]);

            StockMovement::create([
                'product_id'      => $product->id,
                'company_id'      => $companyId,
                'user_id'         => auth()->id(),
                'type'            => 'entrada',
                'quantity'        => $pending,
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'reason'          => 'compra',
                'notes'           => "Recebimento OC #{$purchaseOrder->number}",
                'source_type'     => PurchaseOrder::class,
                'source_id'       => $purchaseOrder->id,
            ]);
        }
    }
}
