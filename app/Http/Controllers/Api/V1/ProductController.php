<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function companyId(): int
    {
        return auth()->user()->company_id;
    }

    public function index(Request $request)
    {
        $products = Product::where('company_id', $this->companyId())
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->active !== null, fn($q) => $q->where('active', (bool) $request->active))
            ->orderBy('name')
            ->paginate(20);

        return response()->json($products);
    }

    public function show(Product $product)
    {
        abort_if($product->company_id !== $this->companyId(), 403);

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => ['nullable', 'string', 'max:100'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost_price'   => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['nullable', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'active'       => ['boolean'],
        ]);

        // Verifica limite de plano
        $company = auth()->user()->company;
        if (! $company->canAddProduct()) {
            return response()->json([
                'message' => 'Limite de produtos do plano atingido. Faça upgrade para continuar.',
            ], 403);
        }

        $product = Product::create(array_merge($data, [
            'company_id' => $this->companyId(),
        ]));

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->company_id !== $this->companyId(), 403);

        $data = $request->validate([
            'name'         => ['sometimes', 'string', 'max:255'],
            'sku'          => ['nullable', 'string', 'max:100'],
            'price'        => ['sometimes', 'numeric', 'min:0'],
            'cost_price'   => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['nullable', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'active'       => ['boolean'],
        ]);

        $product->update($data);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        abort_if($product->company_id !== $this->companyId(), 403);

        $product->delete();

        return response()->json(['message' => 'Produto removido.'], 200);
    }
}
