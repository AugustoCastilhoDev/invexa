<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('company_id', Auth::user()->company_id)
            ->with('category')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'ativo');
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::where('company_id', Auth::user()->company_id)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('products')) {
            return redirect()->route('products.index')
                ->with('error', $this->limitMessage('produtos', $company->limit('products'), 'products'));
        }

        $categories = Category::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('products')) {
            return redirect()->route('products.index')
                ->with('error', $this->limitMessage('produtos', $company->limit('products'), 'products'));
        }

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost_price'  => ['nullable', 'numeric', 'min:0'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'min_quantity'=> ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'unit'        => ['nullable', 'string', 'max:20'],
        ]);

        Product::create(array_merge($request->all(), [
            'company_id' => Auth::user()->company_id,
            'active'     => $request->boolean('active', true),
        ]));

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product)
    {
        $this->authorize($product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize($product);
        $categories = Category::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize($product);

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost_price'  => ['nullable', 'numeric', 'min:0'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'min_quantity'=> ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'unit'        => ['nullable', 'string', 'max:20'],
        ]);

        $product->update(array_merge($request->all(), [
            'active' => $request->boolean('active'),
        ]));

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $this->authorize($product);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }

    private function authorize(Product $product): void
    {
        if ($product->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite, string $resource): string
    {
        $company = Auth::user()->company;
        $plano   = strtoupper($company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}).  ✨ Faça upgrade para continuar.";
    }
}
