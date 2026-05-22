<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleReturnItem;
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

    public function show(Customer $customer, Request $request)
    {
        $this->authorizeCustomer($customer);
        $companyId = Auth::user()->company_id;

        // ── Filtros do histórico ──────────────────────────────────────
        $from   = $request->input('from');
        $to     = $request->input('to');
        $status = $request->input('status');

        // ── Query de vendas deste cliente ─────────────────────────────
        $salesQuery = Sale::with('items.product')
            ->where('company_id', $companyId)
            ->where('customer_id', $customer->id);

        if ($from)   { $salesQuery->whereDate('sale_date', '>=', $from); }
        if ($to)     { $salesQuery->whereDate('sale_date', '<=', $to); }
        if ($status) { $salesQuery->where('status', $status); }

        $sales = $salesQuery->orderByDesc('sale_date')->paginate(10)->withQueryString();

        // ── KPIs (sem filtro de data/status para mostrar o total real) ─
        $allSales = Sale::where('company_id', $companyId)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['concluida', 'pendente'])
            ->get(['id', 'total', 'sale_date']);

        $totalSales = $allSales->count();
        $totalSpent = $allSales->sum('total');
        $avgTicket  = $totalSales > 0 ? $totalSpent / $totalSales : 0;
        $lastSale   = Sale::where('company_id', $companyId)
            ->where('customer_id', $customer->id)
            ->orderByDesc('sale_date')
            ->first(['id', 'sale_date']);

        // Devoluções deste cliente
        $saleIds = $allSales->pluck('id');
        $returnsData = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->whereIn('sale_id', $saleIds))
            ->selectRaw('SUM(quantity * price) as total_returned, COUNT(DISTINCT sale_return_id) as count_returned')
            ->first();

        $returnsTotal = (float) ($returnsData->total_returned ?? 0);
        $returnsCount = (int)   ($returnsData->count_returned ?? 0);
        $netSpent     = $totalSpent - $returnsTotal;

        return view('customers.show', compact(
            'customer',
            'sales',
            'from', 'to', 'status',
            'totalSales', 'totalSpent', 'avgTicket', 'lastSale',
            'returnsTotal', 'returnsCount', 'netSpent'
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
