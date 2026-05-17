<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Supplier::where('company_id', $companyId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('trade_name', 'like', '%' . $request->search . '%')
                  ->orWhere('document', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $totalSuppliers  = Supplier::where('company_id', $companyId)->count();
        $activeSuppliers = Supplier::where('company_id', $companyId)->where('active', true)->count();

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:200'],
            'trade_name'     => ['nullable', 'string', 'max:200'],
            'document'       => [
                'nullable', 'string', 'max:20',
                Rule::unique('suppliers', 'document')->where('company_id', $companyId),
            ],
            'email'          => ['nullable', 'email', 'max:200'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'address'        => ['nullable', 'string', 'max:200'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:2'],
            'zip_code'       => ['nullable', 'string', 'max:10'],
            'notes'          => ['nullable', 'string'],
        ], [
            'name.required'    => 'O nome do fornecedor é obrigatório.',
            'email.email'      => 'Informe um e-mail válido.',
            'document.unique'  => 'Este CNPJ/CPF já está cadastrado.',
        ]);

        $validated['active']     = $request->has('active');
        $validated['company_id'] = $companyId;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso.');
    }

    public function show(Supplier $supplier)
    {
        abort_if($supplier->company_id !== auth()->user()->company_id, 403);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        abort_if($supplier->company_id !== auth()->user()->company_id, 403);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $companyId = auth()->user()->company_id;
        abort_if($supplier->company_id !== $companyId, 403);

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:200'],
            'trade_name'     => ['nullable', 'string', 'max:200'],
            'document'       => [
                'nullable', 'string', 'max:20',
                Rule::unique('suppliers', 'document')->where('company_id', $companyId)->ignore($supplier->id),
            ],
            'email'          => ['nullable', 'email', 'max:200'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'address'        => ['nullable', 'string', 'max:200'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:2'],
            'zip_code'       => ['nullable', 'string', 'max:10'],
            'notes'          => ['nullable', 'string'],
        ], [
            'name.required'   => 'O nome do fornecedor é obrigatório.',
            'email.email'     => 'Informe um e-mail válido.',
            'document.unique' => 'Este CNPJ/CPF já está cadastrado.',
        ]);

        $validated['active'] = $request->has('active');
        $supplier->update($validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier)
    {
        abort_if($supplier->company_id !== auth()->user()->company_id, 403);
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor excluído com sucesso.');
    }
}
