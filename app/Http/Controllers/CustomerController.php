<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Customer::where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search)
                  ->orWhere('document', 'like', $search);
            });
        }

        $customers = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $company = auth()->user()->company;
        if (! $company->canAdd('customers')) {
            return redirect()->route('customers.index')
                ->with('error', 'Limite de clientes atingido para o seu plano.');
        }
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $company   = auth()->user()->company;

        if (! $company->canAdd('customers')) {
            return redirect()->route('customers.index')
                ->with('error', 'Limite de clientes atingido para o seu plano.');
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255', Rule::unique('customers')->where('company_id', $companyId)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
            'notes'    => ['nullable', 'string', 'max:1000'],
            'type'     => ['nullable', 'in:pessoa_fisica,pessoa_juridica'],
            'birth_date' => ['nullable', 'date'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $customer = Customer::create(array_merge($validated, ['company_id' => $companyId]));

        // ── Webhook: customer.created
        WebhookDispatcher::dispatch($company, 'customer.created', [
            'id'    => $customer->id,
            'name'  => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'type'  => $customer->type,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Customer $customer)
    {
        abort_if($customer->company_id !== auth()->user()->company_id, 403);
        $customer->load(['sales' => fn($q) => $q->latest()->limit(10)]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        abort_if($customer->company_id !== auth()->user()->company_id, 403);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if($customer->company_id !== auth()->user()->company_id, 403);
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255', Rule::unique('customers')->where('company_id', $companyId)->ignore($customer->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
            'notes'    => ['nullable', 'string', 'max:1000'],
            'type'     => ['nullable', 'in:pessoa_fisica,pessoa_juridica'],
            'birth_date' => ['nullable', 'date'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $customer)
    {
        abort_if($customer->company_id !== auth()->user()->company_id, 403);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente removido com sucesso.');
    }

    public function search(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $term      = $request->get('q', '');
        $customers = Customer::where('company_id', $companyId)
            ->where('name', 'like', '%' . $term . '%')
            ->limit(10)
            ->get(['id', 'name', 'email']);
        return response()->json($customers);
    }
}
