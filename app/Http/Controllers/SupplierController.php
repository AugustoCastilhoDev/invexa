<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Supplier::where('company_id', $companyId)->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'ativo');
        }

        $suppliers       = $query->paginate(15)->withQueryString();
        $totalSuppliers  = Supplier::where('company_id', $companyId)->count();
        $activeSuppliers = Supplier::where('company_id', $companyId)->where('active', true)->count();

        return view('suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers'));
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
        $company   = Auth::user()->company;
        $companyId = Auth::user()->company_id;

        if ($company && !$company->canAdd('suppliers')) {
            return redirect()->route('suppliers.index')
                ->with('error', $this->limitMessage('fornecedores', $company->limit('suppliers')));
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => [
                'nullable', 'string', 'max:20',
                Rule::unique('suppliers', 'document')->where('company_id', $companyId),
            ],
            'email'    => [
                'nullable', 'email', 'max:255',
                Rule::unique('suppliers', 'email')->where('company_id', $companyId),
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string'],
        ], [
            'name.required'    => 'O nome do fornecedor é obrigatório.',
            'document.unique'  => 'Já existe um fornecedor com este CNPJ/CPF.',
            'email.unique'     => 'Já existe um fornecedor com este e-mail.',
            'email.email'      => 'Informe um e-mail válido.',
        ]);

        $supplier = Supplier::create(array_merge($validated, [
            'company_id' => $companyId,
            'active'     => $request->boolean('active', true),
        ]));

        AuditLogger::action('supplier.created', $supplier);
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor cadastrado com sucesso.');
    }

    public function show(Supplier $supplier)
    {
        $this->authorizeSupplier($supplier);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorizeSupplier($supplier);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeSupplier($supplier);
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => [
                'nullable', 'string', 'max:20',
                Rule::unique('suppliers', 'document')
                    ->where('company_id', $companyId)
                    ->ignore($supplier->id),
            ],
            'email'    => [
                'nullable', 'email', 'max:255',
                Rule::unique('suppliers', 'email')
                    ->where('company_id', $companyId)
                    ->ignore($supplier->id),
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string'],
        ], [
            'name.required'    => 'O nome do fornecedor é obrigatório.',
            'document.unique'  => 'Já existe um fornecedor com este CNPJ/CPF.',
            'email.unique'     => 'Já existe um fornecedor com este e-mail.',
            'email.email'      => 'Informe um e-mail válido.',
        ]);

        $supplier->update(array_merge($validated, [
            'active' => $request->boolean('active'),
        ]));

        AuditLogger::action('supplier.updated', $supplier);
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorizeSupplier($supplier);
        $supplier->delete();
        AuditLogger::action('supplier.deleted', $supplier);
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor removido com sucesso.');
    }

    private function authorizeSupplier(Supplier $supplier): void
    {
        if ($supplier->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}). ✨ Faça upgrade para continuar.";
    }
}
