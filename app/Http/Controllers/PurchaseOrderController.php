<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        $companyId = Auth::user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $company   = Auth::user()->company;
        $companyId = Auth::user()->company_id;

        if ($company && !$company->canAdd('purchase_orders')) {
            return redirect()->route('purchase-orders.index')
                ->with('error', $this->limitMessage('ordens de compra', $company->limit('purchase_orders')));
        }

        $request->validate(
            [
                'supplier_id'        => ['required', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
                'order_date'         => ['required', 'date'],
                'notes'              => ['nullable', 'string'],
                'items'              => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
                'items.*.quantity'   => ['required', 'integer', 'min:1'],
                'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            ],
            [
                'supplier_id.required'        => 'Selecione um fornecedor.',
                'supplier_id.exists'          => 'Fornecedor inválido.',
                'order_date.required'         => 'A data do pedido é obrigatória.',
                'order_date.date'             => 'Data inválida.',
                'items.required'              => 'Adicione ao menos um produto ao pedido.',
                'items.min'                   => 'Adicione ao menos um produto ao pedido.',
                'items.*.product_id.required' => 'Selecione o produto do item.',
                'items.*.product_id.exists'   => 'Produto inválido.',
                'items.*.quantity.required'   => 'Informe a quantidade do item.',
                'items.*.quantity.integer'    => 'A quantidade deve ser um número inteiro.',
                'items.*.quantity.min'        => 'A quantidade mínima é 1.',
                'items.*.unit_price.required' => 'Informe o preço unitário do item.',
                'items.*.unit_price.numeric'  => 'O preço deve ser numérico.',
                'items.*.unit_price.min'      => 'O preço não pode ser negativo.',
            ]
        );

        DB::transaction(function () use ($request, $companyId) {
            $order = PurchaseOrder::create([
                'company_id'  => $companyId,
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
        $this->authorizeOrder($purchaseOrder);
        $purchaseOrder->load('supplier', 'items.product');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status !== 'pendente') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Apenas ordens pendentes podem ser editadas.');
        }
        $companyId = Auth::user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        $products  = Product::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $purchaseOrder->load('items.product');
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizeOrder($purchaseOrder);
        $companyId = Auth::user()->company_id;

        $request->validate(
            [
                'supplier_id'        => ['required', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
                'order_date'         => ['required', 'date'],
                'notes'              => ['nullable', 'string'],
                'items'              => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
                'items.*.quantity'   => ['required', 'integer', 'min:1'],
                'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            ],
            [
                'supplier_id.required'        => 'Selecione um fornecedor.',
                'supplier_id.exists'          => 'Fornecedor inválido.',
                'order_date.required'         => 'A data do pedido é obrigatória.',
                'order_date.date'             => 'Data inválida.',
                'items.required'              => 'Adicione ao menos um produto ao pedido.',
                'items.min'                   => 'Adicione ao menos um produto ao pedido.',
                'items.*.product_id.required' => 'Selecione o produto do item.',
                'items.*.product_id.exists'   => 'Produto inválido.',
                'items.*.quantity.required'   => 'Informe a quantidade do item.',
                'items.*.quantity.integer'    => 'A quantidade deve ser um número inteiro.',
                'items.*.quantity.min'        => 'A quantidade mínima é 1.',
                'items.*.unit_price.required' => 'Informe o preço unitário do item.',
                'items.*.unit_price.numeric'  => 'O preço deve ser numérico.',
                'items.*.unit_price.min'      => 'O preço não pode ser negativo.',
            ]
        );

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
        $this->authorizeOrder($purchaseOrder);

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
        $this->authorizeOrder($purchaseOrder);
        if ($purchaseOrder->status !== 'pendente') {
            return back()->with('error', 'Apenas ordens pendentes podem ser excluídas.');
        }
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Ordem excluída com sucesso.');
    }

    private function authorizeOrder(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}). ✨ Faça upgrade para continuar.";
    }
}
