<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::where('company_id', Auth::user()->company_id)
            ->with('supplier')
            ->latest();

        if ($request->filled('search')) {
            $query->whereHas('supplier', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('purchase_orders')) {
            return redirect()->route('purchase-orders.index')
                ->with('error', $this->limitMessage('ordens de compra', $company->limit('purchase_orders')));
        }

        $suppliers = Supplier::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        $products  = Product::where('company_id', Auth::user()->company_id)->where('active', true)->orderBy('name')->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('purchase_orders')) {
            return redirect()->route('purchase-orders.index')
                ->with('error', $this->limitMessage('ordens de compra', $company->limit('purchase_orders')));
        }

        $request->validate([
            'supplier_id'        => ['required', 'exists:suppliers,id'],
            'order_date'         => ['required', 'date'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request) {
            $order = PurchaseOrder::create([
                'company_id'  => Auth::user()->company_id,
                'supplier_id' => $request->supplier_id,
                'order_date'  => $request->order_date,
                'notes'       => $request->notes,
                'status'      => 'pendente',
                'total'       => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_cost'  => $item['unit_price'],
                    'subtotal'   => $subtotal,
                ]);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Ordem de compra criada com sucesso.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize($purchaseOrder);
        $purchaseOrder->load('supplier', 'items.product');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorize($purchaseOrder);
        if ($purchaseOrder->status !== 'pendente') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Apenas ordens pendentes podem ser editadas.');
        }
        $suppliers = Supplier::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        $products  = Product::where('company_id', Auth::user()->company_id)->where('active', true)->orderBy('name')->get();
        $purchaseOrder->load('items.product');
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize($purchaseOrder);

        $request->validate([
            'supplier_id'        => ['required', 'exists:suppliers,id'],
            'order_date'         => ['required', 'date'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date'  => $request->order_date,
                'notes'       => $request->notes,
            ]);

            $purchaseOrder->items()->delete();
            $total = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_cost'  => $item['unit_price'],
                    'subtotal'   => $subtotal,
                ]);
                $total += $subtotal;
            }
            $purchaseOrder->update(['total' => $total]);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Ordem atualizada com sucesso.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        $this->authorize($purchaseOrder);

        if (!$purchaseOrder->canReceive()) {
            return back()->with('error', 'Esta ordem não pode ser recebida.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->load('items.product');
            foreach ($purchaseOrder->items as $item) {
                $item->product->increment('quantity', $item->quantity);
            }
            $purchaseOrder->update([
                'status'      => 'recebida',
                'received_at' => now(),
            ]);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Ordem recebida e estoque atualizado.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorize($purchaseOrder);
        if ($purchaseOrder->status !== 'pendente') {
            return back()->with('error', 'Apenas ordens pendentes podem ser excluídas.');
        }
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Ordem excluída com sucesso.');
    }

    private function authorize(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}).  ✨ Faça upgrade para continuar.";
    }
}
