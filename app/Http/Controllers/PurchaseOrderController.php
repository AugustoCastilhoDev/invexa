<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = PurchaseOrder::with('supplier')
            ->where('company_id', $companyId);

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders    = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $suppliers = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();

        $totalOrders   = PurchaseOrder::where('company_id', $companyId)->count();
        $pendingOrders = PurchaseOrder::where('company_id', $companyId)
                            ->whereIn('status', ['enviada', 'recebida_parcial'])->count();
        $totalValue    = PurchaseOrder::where('company_id', $companyId)
                            ->whereIn('status', ['enviada', 'recebida_parcial', 'recebida'])
                            ->sum('total');

        return view('purchase_orders.index', compact(
            'orders', 'suppliers', 'totalOrders', 'pendingOrders', 'totalValue'
        ));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();

        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'           => ['required', 'exists:suppliers,id'],
            'expected_date'         => ['nullable', 'date'],
            'notes'                 => ['nullable', 'string'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.product_id'    => ['required', 'exists:products,id'],
            'items.*.quantity'      => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'     => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required'        => 'Selecione um fornecedor.',
            'items.required'              => 'Adicione pelo menos um item.',
            'items.*.product_id.required' => 'Selecione o produto.',
            'items.*.quantity.required'   => 'Informe a quantidade.',
            'items.*.unit_cost.required'  => 'Informe o custo unitário.',
        ]);

        $companyId = auth()->user()->company_id;

        DB::transaction(function () use ($request, $companyId) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['unit_cost'];
            }

            $order = PurchaseOrder::create([
                'company_id'    => $companyId,
                'supplier_id'   => $request->supplier_id,
                'user_id'       => auth()->id(),
                'number'        => PurchaseOrder::nextNumber($companyId),
                'status'        => $request->has('send') ? 'enviada' : 'rascunho',
                'expected_date' => $request->expected_date,
                'notes'         => $request->notes,
                'total'         => $total,
            ]);

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_cost'         => $item['unit_cost'],
                    'subtotal'          => $item['quantity'] * $item['unit_cost'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Ordem de compra criada com sucesso.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== auth()->user()->company_id, 403);
        $purchaseOrder->load('supplier', 'user', 'items.product');
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /** Exibe formulário de recebimento */
    public function receiveForm(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== auth()->user()->company_id, 403);
        abort_if(! $purchaseOrder->canReceive(), 422);
        $purchaseOrder->load('supplier', 'items.product');
        return view('purchase_orders.receive', compact('purchaseOrder'));
    }

    /** Processa recebimento e dá entrada no estoque */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== auth()->user()->company_id, 403);
        abort_if(! $purchaseOrder->canReceive(), 422);

        $request->validate([
            'items'                     => ['required', 'array'],
            'items.*.quantity_received' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $allReceived = true;

            foreach ($purchaseOrder->items as $item) {
                $received = (int) ($request->items[$item->id]['quantity_received'] ?? 0);

                if ($received <= 0) {
                    // Se nenhuma quantidade recebida neste item, ainda pode estar pendente
                    if ($item->quantity_received < $item->quantity) {
                        $allReceived = false;
                    }
                    continue;
                }

                // Captura estoque ANTES de alterar
                $quantityBefore = (int) $item->product->quantity;
                $quantityAfter  = $quantityBefore + $received;

                // Atualiza quantidade recebida no item da OC
                $newTotal = $item->quantity_received + $received;
                $item->update(['quantity_received' => $newTotal]);

                // Incrementa estoque do produto
                $item->product->increment('quantity', $received);

                // Registra movimentação de estoque com snapshot completo
                StockMovement::create([
                    'company_id'      => $purchaseOrder->company_id,
                    'product_id'      => $item->product_id,
                    'user_id'         => auth()->id(),
                    'type'            => 'entrada',
                    'quantity'        => $received,
                    'quantity_before' => $quantityBefore,
                    'quantity_after'  => $quantityAfter,
                    'reason'          => 'Recebimento OC ' . $purchaseOrder->number,
                    'notes'           => 'Ordem de Compra #' . $purchaseOrder->number,
                ]);

                if ($newTotal < $item->quantity) {
                    $allReceived = false;
                }
            }

            $purchaseOrder->update([
                'status'      => $allReceived ? 'recebida' : 'recebida_parcial',
                'received_at' => now()->toDateString(),
            ]);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Recebimento registrado e estoque atualizado.');
    }

    /** Envia a OC (rascunho → enviada) */
    public function send(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== auth()->user()->company_id, 403);
        abort_if(! $purchaseOrder->canSend(), 422);
        $purchaseOrder->update(['status' => 'enviada']);
        return back()->with('success', 'Ordem de compra enviada ao fornecedor.');
    }

    /** Cancela a OC */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== auth()->user()->company_id, 403);
        abort_if(! $purchaseOrder->canCancel(), 422);
        $purchaseOrder->update(['status' => 'cancelada']);
        return back()->with('success', 'Ordem de compra cancelada.');
    }
}
