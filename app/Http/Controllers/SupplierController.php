<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::where('company_id', Auth::user()->company_id)->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->paginate(15)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('suppliers')) {
            return redirect()->route('suppliers.index')
                ->with('error', $this->limitMessage('fornecedores', $company->limit('suppliers')));
        }
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('suppliers')) {
            return redirect()->route('suppliers.index')
                ->with('error', $this->limitMessage('fornecedores', $company->limit('suppliers')));
        }

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'document'=> ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string'],
        ]);

        Supplier::create(array_merge($request->all(), [
            'company_id' => Auth::user()->company_id,
        ]));

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor criado com sucesso.');
    }

    public function show(Supplier $supplier)
    {
        $this->authorize($supplier);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize($supplier);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize($supplier);

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'document'=> ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string'],
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize($supplier);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor excluído com sucesso.');
    }

    private function authorize(Supplier $supplier): void
    {
        if ($supplier->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}).  ✨ Faça upgrade para continuar.";
    }
}
