<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('company_id', Auth::user()->company_id)->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
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
        $company = Auth::user()->company;
        if ($company && !$company->canAdd('customers')) {
            return redirect()->route('customers.index')
                ->with('error', $this->limitMessage('clientes', $company->limit('customers')));
        }

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'document'=> ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string'],
        ]);

        Customer::create(array_merge($request->all(), [
            'company_id' => Auth::user()->company_id,
        ]));

        return redirect()->route('customers.index')->with('success', 'Cliente criado com sucesso.');
    }

    public function show(Customer $customer)
    {
        $this->authorize($customer);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorize($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize($customer);

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'document'=> ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes'   => ['nullable', 'string'],
        ]);

        $customer->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize($customer);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente excluído com sucesso.');
    }

    public function search(Request $request)
    {
        $customers = Customer::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($customers);
    }

    private function authorize(Customer $customer): void
    {
        if ($customer->company_id !== Auth::user()->company_id) abort(403);
    }

    private function limitMessage(string $nome, int $limite): string
    {
        $plano = strtoupper(Auth::user()->company->plan);
        return "Limite de {$nome} do plano {$plano} atingido ({$limite}).  ✨ Faça upgrade para continuar.";
    }
}
