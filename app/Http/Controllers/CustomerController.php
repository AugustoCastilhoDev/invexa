<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Customer::where('company_id', $companyId)->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'ativo');
        }

        $customers       = $query->paginate(15)->withQueryString();
        $totalCustomers  = Customer::where('company_id', $companyId)->count();
        $activeCustomers = Customer::where('company_id', $companyId)->where('active', true)->count();

        return view('customers.index', compact('customers', 'totalCustomers', 'activeCustomers'));
    }

    /**
     * Autocomplete de clientes para o formulário de venda.
     * Retorna JSON: [{id, name, document, phone}]
     */
    public function search(Request $request)
    {
        $q         = trim($request->get('q', ''));
        $companyId = Auth::user()->company_id;

        $customers = Customer::where('company_id', $companyId)
            ->where('active', true)
            ->where(function ($query) use ($q) {
                $query->where('name',     'like', "%{$q}%")
                      ->orWhere('document', 'like', "%{$q}%")
                      ->orWhere('phone',    'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'document', 'phone']);

        return response()->json($customers);
    }

    public function create()
    {
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('customers')) {
            return redirect()->route('customers.index')
                ->with('error', $this->limitMessage('clientes', $company->limit('customers')));
        }
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $company   = Auth::user()->company;
        $companyId = Auth::user()->company_id;

        if ($company && !$company->canAdd('customers')) {
            return redirect()->route('customers.index')
                ->with('error', $this->limitMessage('clientes', $company->limit('customers')));
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => [
                'nullable', 'string', 'max:20',
                Rule::unique('customers', 'document')->where('company_id', $companyId),
            ],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string'],
        ], [
            'name.required'   => 'O nome do cliente é obrigatório.',
            'document.unique' => 'Já existe um cliente com este CPF/CNPJ.',
            'email.email'     => 'Informe um e-mail válido.',
        ]);

        Customer::create(array_merge($validated, [
            'company_id' => $companyId,
            'active'     => $request->boolean('active', true),
        ]));

        return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($customer);
        $companyId = Auth::user()->company_id;

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => [
                'nullable', 'string', 'max:20',
                Rule::unique('customers', 'document')
                    ->where('company_id', $companyId)
                    ->ignore($customer->id),
            ],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string'],
        ], [
            'name.required'   => 'O nome do cliente é obrigatório.',
            'document.unique' => 'Já existe um cliente com este CPF/CNPJ.',
            'email.email'     => 'Informe um e-mail válido.',
        ]);

        $customer->update(array_merge($validated, [
            'active' => $request->boolean('active'),
        ]));

        return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente removido com sucesso.');
    }

    private function authorizeCustomer(Customer $customer): void
    {
        if ($customer->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}). ✨ Faça upgrade para continuar.";
    }
}
