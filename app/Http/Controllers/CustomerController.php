<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;

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

        $customers = $query->orderBy('name')->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.form');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $company   = auth()->user()->company;

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'phone'    => 'nullable|string|max:30',
            'document' => 'nullable|string|max:30',
            'address'  => 'nullable|string|max:500',
            'notes'    => 'nullable|string',
        ]);

        $customer = Customer::create(array_merge($data, ['company_id' => $companyId]));

        // Webhook customer.created
        WebhookDispatcher::dispatch($company, 'customer.created', [
            'id'       => $customer->id,
            'name'     => $customer->name,
            'email'    => $customer->email,
            'phone'    => $customer->phone,
            'document' => $customer->document,
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        $customer->load(['sales' => fn($q) => $q->latest()->take(10)]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($customer);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'phone'    => 'nullable|string|max:30',
            'document' => 'nullable|string|max:30',
            'address'  => 'nullable|string|max:500',
            'notes'    => 'nullable|string',
        ]);

        $customer->update($data);

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
        if ($customer->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
