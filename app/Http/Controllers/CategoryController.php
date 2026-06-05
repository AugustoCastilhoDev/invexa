<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $categories         = Category::where('company_id', $companyId)->latest()->paginate(10);
        $activeCategories   = Category::where('company_id', $companyId)->where('active', true)->count();
        $inactiveCategories = Category::where('company_id', $companyId)->where('active', false)->count();

        return view('categories.index', compact(
            'categories',
            'activeCategories',
            'inactiveCategories'
        ));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'name'        => [
                'required', 'string', 'max:120',
                Rule::unique('categories', 'name')->where('company_id', $companyId),
            ],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique'   => 'Já existe uma categoria com este nome.',
        ]);

        $data['active']     = $request->has('active');
        $data['company_id'] = $companyId;

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria cadastrada com sucesso!');
    }

    public function show(Category $category)
    {
        $this->authorize($category);
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $this->authorize($category);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize($category);
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'name'        => [
                'required', 'string', 'max:120',
                Rule::unique('categories', 'name')
                    ->where('company_id', $companyId)
                    ->ignore($category->id),
            ],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique'   => 'Já existe uma categoria com este nome.',
        ]);

        $data['active'] = $request->has('active');

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $this->authorize($category);
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }

    private function authorize(Category $category): void
    {
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
