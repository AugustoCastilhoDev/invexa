<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $totalProducts = (clone $query)->count();
        $products = $query->orderBy('name')->paginate(10);
        $categories = Category::orderBy('name')->get();
        $categoriesCount = Category::count();
        $lowStockCount = Product::whereColumn('quantity', '<', 'min_quantity')->count();

        return view('products.index', compact('products', 'categories', 'totalProducts', 'categoriesCount', 'lowStockCount'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:10'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'O nome do produto é obrigatório',
            'sku.required' => 'O SKU é obrigatório',
            'sku.unique' => 'Este SKU já existe no sistema',
            'price.required' => 'O preço é obrigatório',
            'quantity.required' => 'A quantidade é obrigatória',
        ]);

        $validated['active'] = $request->boolean('active');
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produto cadastrado com sucesso.');
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,' . $product->id],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:10'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'O nome do produto é obrigatório',
            'sku.required' => 'O SKU é obrigatório',
            'sku.unique' => 'Este SKU já existe no sistema',
            'price.required' => 'O preço é obrigatório',
            'quantity.required' => 'A quantidade é obrigatória',
        ]);

        $validated['active'] = $request->boolean('active');

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }
}
