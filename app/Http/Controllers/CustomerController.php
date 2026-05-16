<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Customer::forCompany($companyId)->orderBy('name');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('document', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('phone', 'like', $s);
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $customers = $query->withCount('sales')
                           ->withSum('sales', 'total')
                           ->paginate(15)
                           ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string', 'max:1000'],
            'active'   => ['boolean'],
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['active']     = $request->boolean('active', true);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Customer $customer)
    {
        $this->authorizeCustomer($customer);

        $customer->load(['sales' => fn($q) => $q->latest()->limit(10)]);

        $totalSales  = $customer->sales()->count();
        $totalSpent  = (float) $customer->sales()->sum('total');
        $lastSale    = $customer->sales()->latest('sale_date')->first();

        return view('customers.show', compact('customer', 'totalSales', 'totalSpent', 'lastSale'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($customer);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string', 'max:1000'],
            'active'   => ['boolean'],
        ]);

        $validated['active'] = $request->boolean('active', true);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeCustomer($customer);

        // Desvincula vendas antes de excluir (nullOnDelete já faz no banco, mas garante em memória)
        $customer->sales()->update(['customer_id' => null]);
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente excluído com sucesso.');
    }

    // ── API rápida para selects (usada no formulário de venda) ────────
    public function search(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $term      = $request->input('q', '');

        $customers = Customer::forCompany($companyId)
            ->active()
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'document', 'phone']);

        return response()->json($customers);
    }

    private function authorizeCustomer(Customer $customer): void
    {
        abort_if($customer->company_id !== auth()->user()->company_id, 403);
    }
}
