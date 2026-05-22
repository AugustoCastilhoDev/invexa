<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Product::where('company_id', $companyId)
            ->with('category')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'ativo');
        }
        if ($request->boolean('low_stock')) {
            $query->where('active', true)
                  ->where('min_quantity', '>', 0)
                  ->whereColumn('quantity', '<=', 'min_quantity');
        }

        $products        = $query->paginate(15)->withQueryString();
        $categories      = Category::where('company_id', $companyId)->orderBy('name')->get();
        $totalProducts   = Product::where('company_id', $companyId)->count();
        $categoriesCount = $categories->count();
        $lowStockCount   = Product::where('company_id', $companyId)
                               ->where('active', true)
                               ->where('min_quantity', '>', 0)
                               ->whereColumn('quantity', '<=', 'min_quantity')
                               ->count();
        $lowStockAlert   = $lowStockCount;

        return view('products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'categoriesCount',
            'lowStockCount',
            'lowStockAlert'
        ));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('products')) {
            return redirect()->route('products.index')
                ->with('error', $this->limitMessage('produtos', $company->limit('products')));
        }

        $companyId  = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $suppliers  = Supplier::where('company_id', $companyId)->orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('products')) {
            return redirect()->route('products.index')
                ->with('error', $this->limitMessage('produtos', $company->limit('products')));
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => ['nullable', 'string', 'max:100'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'supplier_id'  => ['nullable', 'exists:suppliers,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost'         => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['nullable', 'string', 'max:20'],
        ]);

        Product::create(array_merge($validated, [
            'company_id' => Auth::user()->company_id,
            'active'     => $request->boolean('active', true),
        ]));

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product)
    {
        $this->authorizeProduct($product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        $companyId  = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $suppliers  = Supplier::where('company_id', $companyId)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => ['nullable', 'string', 'max:100'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'supplier_id'  => ['nullable', 'exists:suppliers,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost'         => ['nullable', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['nullable', 'string', 'max:20'],
        ]);

        $product->update(array_merge($validated, [
            'active' => $request->boolean('active'),
        ]));

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }

    private function authorizeProduct(Product $product): void
    {
        if ($product->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}). ✨ Faça upgrade para continuar.";
    }
}
