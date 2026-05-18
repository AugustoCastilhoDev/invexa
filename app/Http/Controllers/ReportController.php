<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PurchaseOrder;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ---------------------------------------------------------------
    // Relatório de Compras
    // ---------------------------------------------------------------
    public function purchases(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // -- Per\u00edodo ---------------------------------------------------
        $period = $request->get('period', '30');
        [$from, $to] = $this->resolvePurchasePeriod($period, $request);

        // -- Filtros opcionais ------------------------------------------
        $supplierId = $request->get('supplier_id');
        $status     = $request->get('status');

        // -- Query base --------------------------------------------------
        $query = PurchaseOrder::with(['supplier', 'items.product.category'])
            ->where('company_id', $companyId)
            ->whereBetween('order_date', [$from, $to]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('order_date')->get();

        // -- KPIs --------------------------------------------------------
        $totalOrders   = $orders->count();
        $totalValue    = $orders->whereNotIn('status', ['cancelada'])->sum('total');
        $receivedValue = $orders->where('status', 'recebida')->sum('total');
        $pendingValue  = $orders->where('status', 'pendente')->sum('total');

        // -- Agrupamento por fornecedor ----------------------------------
        $bySupplier = $orders->whereNotIn('status', ['cancelada'])
            ->groupBy('supplier_id')
            ->map(function ($group) {
                return [
                    'name'  => optional($group->first()->supplier)->name ?? '(sem fornecedor)',
                    'count' => $group->count(),
                    'total' => $group->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        // -- Produtos mais comprados -------------------------------------
        $topItems = DB::table('purchase_order_items as poi')
            ->join('purchase_orders as po', 'po.id', '=', 'poi.purchase_order_id')
            ->join('products as p', 'p.id', '=', 'poi.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('po.company_id', $companyId)
            ->whereNotIn('po.status', ['cancelada'])
            ->whereBetween('po.order_date', [$from, $to])
            ->when($supplierId, fn($q) => $q->where('po.supplier_id', $supplierId))
            ->selectRaw('
                p.name  as product_name,
                c.name  as category_name,
                SUM(poi.quantity)         as total_qty,
                COUNT(DISTINCT po.id)     as total_orders,
                SUM(poi.subtotal)         as total_cost
            ')
            ->groupBy('poi.product_id', 'p.name', 'c.name')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();

        // -- Fornecedores para o filtro ----------------------------------
        $suppliers = Supplier::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('reports.purchases', compact(
            'period', 'from', 'to',
            'supplierId', 'status',
            'orders',
            'totalOrders', 'totalValue', 'receivedValue', 'pendingValue',
            'bySupplier', 'topItems',
            'suppliers'
        ));
    }

    // ---------------------------------------------------------------
    // Relat\u00f3rio Financeiro
    // ---------------------------------------------------------------
    public function financial(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $period = $request->get('period', 'month');
        [$from, $to] = $this->resolvePeriod($period, $request);

        $receivablesPaid = Receivable::where('company_id', $companyId)
            ->where('status', 'recebido')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $receivablesPending = Receivable::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$from, $to])
            ->sum('amount');

        $receivablesOverdue = Receivable::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->where('due_date', '<', now())
            ->sum('amount');

        $billsPaid = Bill::where('company_id', $companyId)
            ->where('status', 'pago')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $billsPending = Bill::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$from, $to])
            ->sum('amount');

        $billsOverdue = Bill::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->where('due_date', '<', now())
            ->sum('amount');

        $netBalance       = $receivablesPaid - $billsPaid;
        $projectedBalance = ($receivablesPaid + $receivablesPending) - ($billsPaid + $billsPending);

        $monthlyRevenue = Receivable::where('company_id', $companyId)
            ->where('status', 'recebido')
            ->where('paid_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyExpenses = Bill::where('company_id', $companyId)
            ->where('status', 'pago')
            ->where('paid_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $receivables = Receivable::with('customer')
            ->where('company_id', $companyId)
            ->whereBetween('due_date', [$from, $to])
            ->orderBy('due_date')
            ->get();

        $bills = Bill::with('supplier')
            ->where('company_id', $companyId)
            ->whereBetween('due_date', [$from, $to])
            ->orderBy('due_date')
            ->get();

        return view('reports.financial', compact(
            'period', 'from', 'to',
            'receivablesPaid', 'receivablesPending', 'receivablesOverdue',
            'billsPaid', 'billsPending', 'billsOverdue',
            'netBalance', 'projectedBalance',
            'monthlyRevenue', 'monthlyExpenses',
            'receivables', 'bills'
        ));
    }

    // ---------------------------------------------------------------
    // Helpers de per\u00edodo
    // ---------------------------------------------------------------

    /** Per\u00edodos em dias (usado pelo relat\u00f3rio de compras) */
    private function resolvePurchasePeriod(string $period, Request $request): array
    {
        if ($period === 'custom') {
            return [
                Carbon::parse($request->get('from', now()->subDays(30)))->startOfDay(),
                Carbon::parse($request->get('to', now()))->endOfDay(),
            ];
        }

        $days = is_numeric($period) ? (int) $period : 30;
        return [
            now()->subDays($days - 1)->startOfDay(),
            now()->endOfDay(),
        ];
    }

    /** Per\u00edodos nomeados (usado pelo relat\u00f3rio financeiro) */
    private function resolvePeriod(string $period, Request $request): array
    {
        return match ($period) {
            'week'    => [now()->startOfWeek(), now()->endOfWeek()],
            'month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'year'    => [now()->startOfYear(), now()->endOfYear()],
            'custom'  => [
                Carbon::parse($request->get('from', now()->startOfMonth())),
                Carbon::parse($request->get('to', now()->endOfMonth()))->endOfDay(),
            ],
            default   => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
