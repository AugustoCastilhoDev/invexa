<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private function companyId(): int
    {
        return auth()->user()->company_id;
    }

    // GET /api/v1/stock — Lista todos produtos com estoque
    public function index(Request $request)
    {
        $stock = Product::where('company_id', $this->companyId())
            ->select('id', 'name', 'sku', 'quantity', 'min_quantity', 'active')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(20);

        return response()->json($stock);
    }

    // GET /api/v1/stock/low — Produtos abaixo do estoque mínimo
    public function low()
    {
        $products = Product::where('company_id', $this->companyId())
            ->where('active', true)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->where('min_quantity', '>', 0)
            ->select('id', 'name', 'sku', 'quantity', 'min_quantity')
            ->orderBy('quantity')
            ->get();

        return response()->json(['data' => $products, 'total' => $products->count()]);
    }

    // POST /api/v1/stock/movement — Registra entrada/saída manual
    public function movement(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type'       => ['required', 'in:in,out,adjustment'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'reason'     => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::where('id', $data['product_id'])
            ->where('company_id', $this->companyId())
            ->firstOrFail();

        $qty = $data['type'] === 'out' ? -abs($data['quantity']) : abs($data['quantity']);
        $product->increment('quantity', $qty);

        StockMovement::create([
            'company_id' => $this->companyId(),
            'product_id' => $product->id,
            'user_id'    => auth()->id(),
            'type'       => $data['type'],
            'quantity'   => $data['quantity'],
            'reason'     => $data['reason'] ?? 'Movimentação via API',
        ]);

        return response()->json([
            'message'          => 'Movimentação registrada.',
            'product_id'       => $product->id,
            'new_quantity'     => $product->fresh()->quantity,
        ]);
    }
}
