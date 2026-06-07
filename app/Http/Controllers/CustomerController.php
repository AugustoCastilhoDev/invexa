<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Customer::where('company_id', $companyId)
            ->withCount(['sales'])
            ->withSum(['sales' => fn ($q) => $q->where('status', 'concluida')], 'total');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search)
                  ->orWhere('document', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $customers = $query->orderBy('name')->paginate(15)->withQueryString();

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

        WebhookDispatcher::dispatch($company, 'customer.created', [
            'id'       => $customer->id,
            'name'     => $customer->name,
            'email'    => $customer->email,
            'phone'    => $customer->phone,
            'document' => $customer->document,
        ]);

        AuditLogger::action('customer.created', $customer);
        return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($customer);

        $from   = $request->input('from');
        $to     = $request->input('to');
        $status = $request->input('status');

        $salesQuery = Sale::with(['items.product'])
            ->where('company_id', $customer->company_id)
            ->where('customer_id', $customer->id);

        if ($from)   { $salesQuery->whereDate('sale_date', '>=', $from); }
        if ($to)     { $salesQuery->whereDate('sale_date', '<=', $to); }
        if ($status) { $salesQuery->where('status', $status); }

        $sales = $salesQuery->orderByDesc('sale_date')->paginate(10)->withQueryString();

        $allSales       = Sale::where('company_id', $customer->company_id)->where('customer_id', $customer->id);
        $completedSales = (clone $allSales)->where('status', 'concluida');

        $totalSales  = $allSales->count();
        $totalSpent  = $completedSales->sum('total');

        $cancelledSales = (clone $allSales)->where('status', 'cancelada');
        $returnsCount   = $cancelledSales->count();
        $returnsTotal   = $cancelledSales->sum('total');

        $netSpent  = $totalSpent - $returnsTotal;
        $avgTicket = $totalSales > 0 ? $totalSpent / max($completedSales->count(), 1) : 0;
        $lastSale  = Sale::where('company_id', $customer->company_id)
                         ->where('customer_id', $customer->id)
                         ->where('status', 'concluida')
                         ->orderByDesc('sale_date')
                         ->first();

        return view('customers.show', compact(
            'customer', 'sales', 'totalSales', 'totalSpent',
            'returnsCount', 'returnsTotal', 'netSpent', 'avgTicket',
            'lastSale', 'from', 'to', 'status'
        ));
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

        AuditLogger::action('customer.updated', $customer);
        return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function search(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $term = $request->get('q', '');
        $customers = Customer::where('company_id', $companyId)
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone']);
        return response()->json($customers);
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeCustomer($customer);
        $customer->delete();
        AuditLogger::action('customer.deleted', $customer);
        return redirect()->route('customers.index')->with('success', 'Cliente removido com sucesso.');
    }

    private function authorizeCustomer(Customer $customer): void
    {
        if ($customer->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
