<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private string $tz = 'America/Sao_Paulo';

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Customer::forCompany($companyId)->orderByDesc('id');

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
        $company = auth()->user()->company;

        if ($company && ! $company->canAddCustomer()) {
            return redirect()->route('customers.index')
                ->with('error', 'Limite de clientes do seu plano atingido. Faça upgrade para continuar.');
        }

        return view('customers.create');
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if ($company && ! $company->canAddCustomer()) {
            return redirect()->route('customers.index')
                ->with('error', 'Limite de clientes do seu plano atingido. Faça upgrade para continuar.');
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'size:2'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['active']     = $request->has('active');

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($customer);

        $from   = $request->input('from');
        $to     = $request->input('to');
        $status = $request->input('status');

        $salesQuery = $customer->sales()->latest('sale_date');

        if ($from) {
            $salesQuery->where('sale_date', '>=', Carbon::parse($from, $this->tz)->startOfDay());
        }
        if ($to) {
            $salesQuery->where('sale_date', '<=', Carbon::parse($to, $this->tz)->endOfDay());
        }
        if ($status) {
            $salesQuery->where('status', $status);
        }

        $sales = $salesQuery->with('items.product')->paginate(15)->withQueryString();

        $totalSales  = $customer->sales()->count();
        $totalSpent  = (float) $customer->sales()->sum('total');
        $lastSale    = $customer->sales()->latest('sale_date')->first();
        $avgTicket   = $totalSales > 0 ? $totalSpent / $totalSales : 0;

        $returnsTotal = (float) SaleReturn::whereHas('sale', fn($q) =>
            $q->where('customer_id', $customer->id)
        )->sum('total');
        $returnsCount = SaleReturn::whereHas('sale', fn($q) =>
            $q->where('customer_id', $customer->id)
        )->count();

        $netSpent = $totalSpent - $returnsTotal;

        return view('customers.show', compact(
            'customer', 'sales',
            'totalSales', 'totalSpent', 'netSpent',
            'avgTicket', 'lastSale',
            'returnsTotal', 'returnsCount',
            'from', 'to', 'status'
        ));
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
        ]);

        $validated['active'] = $request->has('active');

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeCustomer($customer);

        $customer->sales()->update(['customer_id' => null]);
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente excluído com sucesso.');
    }

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
