<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /** Lista todas as movimentações da empresa com filtros **/
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = StockMovement::with(['product', 'user'])
            ->where('company_id', $companyId);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $movements = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $products = Product::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('stock.index', compact('movements', 'products'));
    }

    /** Formulário de nova entrada manual **/
    public function create()
    {
        $companyId = auth()->user()->company_id;

        $products = Product::where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'quantity', 'unit', 'sku']);

        return view('stock.create', compact('products'));
    }

    /** Salva a movimentação e atualiza o estoque **/
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type'       => ['required', 'in:entrada,saida,ajuste'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'reason'     => ['required', 'string'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($validated, $companyId) {
                $product = Product::where('company_id', $companyId)
                    ->lockForUpdate()
                    ->findOrFail($validated['product_id']);

                $before = $product->quantity;

                $delta = match ($validated['type']) {
                    'entrada' => +$validated['quantity'],
                    'saida'   => -$validated['quantity'],
                    'ajuste'  => $validated['quantity'] - $before,
                };

                $after = $validated['type'] === 'ajuste'
                    ? $validated['quantity']
                    : $before + $delta;

                if ($after < 0) {
                    throw new \Exception('Estoque insuficiente. Estoque atual: ' . $before . ' un.');
                }

                $product->update(['quantity' => $after]);

                StockMovement::create([
                    'product_id'      => $product->id,
                    'company_id'      => $companyId,
                    'user_id'         => auth()->id(),
                    'type'            => $validated['type'],
                    'quantity'        => $validated['type'] === 'ajuste' ? ($after - $before) : $delta,
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'reason'          => $validated['reason'],
                    'notes'           => $validated['notes'] ?? null,
                ]);
            });

            return redirect()->route('stock.index')
                ->with('success', 'Movimentação registrada e estoque atualizado com sucesso.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Exclui uma movimentação MANUAL e estorna o estoque **/
    public function destroy(StockMovement $stock)
    {
        $companyId = auth()->user()->company_id;

        abort_if($stock->company_id !== $companyId, 403);

        // Só permite excluir movimentos manuais (não vinculados a vendas)
        if (!is_null($stock->source_type)) {
            return redirect()->route('stock.index')
                ->withErrors(['error' => 'Não é possível excluir movimentações vinculadas a vendas. Exclua a venda correspondente.']);
        }

        DB::transaction(function () use ($stock, $companyId) {
            $product = Product::where('company_id', $companyId)
                ->lockForUpdate()
                ->find($stock->product_id);

            if ($product) {
                $estorno = -$stock->quantity;
                $newQty  = $product->quantity + $estorno;
                $product->update(['quantity' => max(0, $newQty)]);
            }

            $stock->delete();
        });

        return redirect()->route('stock.index')
            ->with('success', 'Movimentação excuída e estoque estornado com sucesso.');
    }

    /**
     * Zera TODAS as movimentações da empresa e o estoque dos produtos.
     * Restrito a usuários com role admin.
     */
    public function resetAll()
    {
        $companyId = auth()->user()->company_id;

        abort_if(auth()->user()->role !== 'admin', 403);

        DB::transaction(function () use ($companyId) {
            StockMovement::where('company_id', $companyId)->delete();
            Product::where('company_id', $companyId)->update(['quantity' => 0]);
        });

        return redirect()->route('stock.index')
            ->with('success', 'Todas as movimentações foram removidas e o estoque foi zerado.');
    }

    /** Histórico de um produto específico **/
    public function product(Product $product)
    {
        $companyId = auth()->user()->company_id;

        abort_if($product->company_id !== $companyId, 403);

        $movements = StockMovement::with('user')
            ->where('product_id', $product->id)
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('stock.product', compact('product', 'movements'));
    }
}
