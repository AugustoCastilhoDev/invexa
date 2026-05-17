<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Product::with('category')->where('company_id', $companyId);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $totalProducts   = (clone $query)->count();
        $products        = $query->orderBy('name')->paginate(10);
        $categories      = Category::where('company_id', $companyId)->orderBy('name')->get();
        $categoriesCount = Category::where('company_id', $companyId)->count();

        // Alinhado com Product::isLowStock() que usa `<=`
        $lowStockCount = Product::where('company_id', $companyId)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->count();

        return view('products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'categoriesCount',
            'lowStockCount'
        ));
    }

    public function create()
    {
        $companyId  = auth()->user()->company_id;
        $company    = auth()->user()->company;

        if ($company && ! $company->canAddProduct()) {
            return redirect()->route('products.index')
                ->with('error', 'Limite de produtos do seu plano atingido. Faça upgrade para continuar.');
        }

        $categories = Category::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $suppliers  = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if ($company && ! $company->canAddProduct()) {
            return redirect()->route('products.index')
                ->with('error', 'Limite de produtos do seu plano atingido. Faça upgrade para continuar.');
        }

        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'sku'          => [
                'required', 'string', 'max:50',
                Rule::unique('products', 'sku')->where('company_id', $companyId),
            ],
            'barcode'      => [
                'nullable', 'string', 'max:50',
                Rule::unique('products', 'barcode')->where('company_id', $companyId),
            ],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost'         => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'unit'         => ['nullable', 'string', 'max:10'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'supplier_id'  => ['nullable', 'exists:suppliers,id'],
        ], [
            'name.required'     => 'O nome do produto é obrigatório.',
            'sku.required'      => 'O SKU é obrigatório.',
            'sku.unique'        => 'Este SKU já está em uso nesta empresa.',
            'price.required'    => 'O preço é obrigatório.',
            'quantity.required' => 'A quantidade é obrigatória.',
        ]);

        $validated['active']     = $request->has('active');
        $validated['company_id'] = $companyId;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function show(Product $product)
    {
        $product->load('category', 'supplier');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companyId     = auth()->user()->company_id;
        $categories    = Category::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $suppliers     = Supplier::where('company_id', $companyId)->where('active', true)->orderBy('name')->get();
        $totalProducts = Product::where('company_id', $companyId)->count();

        return view('products.edit', compact('product', 'categories', 'suppliers', 'totalProducts'));
    }

    public function update(Request $request, Product $product)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'sku'          => [
                'required', 'string', 'max:50',
                Rule::unique('products', 'sku')->where('company_id', $companyId)->ignore($product->id),
            ],
            'barcode'      => [
                'nullable', 'string', 'max:50',
                Rule::unique('products', 'barcode')->where('company_id', $companyId)->ignore($product->id),
            ],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost'         => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['required', 'integer', 'min:0'],
            'unit'         => ['nullable', 'string', 'max:10'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'supplier_id'  => ['nullable', 'exists:suppliers,id'],
        ], [
            'name.required'     => 'O nome do produto é obrigatório.',
            'sku.required'      => 'O SKU é obrigatório.',
            'sku.unique'        => 'Este SKU já está em uso nesta empresa.',
            'price.required'    => 'O preço é obrigatório.',
            'quantity.required' => 'A quantidade é obrigatória.',
        ]);

        $validated['active'] = $request->has('active');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso.');
    }
}
