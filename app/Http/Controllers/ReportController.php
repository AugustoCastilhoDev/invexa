<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PurchaseOrder;
use App\Models\Receivable;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function financial(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $period    = $request->get('period', 'month');
        $from      = match($period) {
            'week'    => now()->startOfWeek(),
            'month'   => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year'    => now()->startOfYear(),
            'custom'  => Carbon::parse($request->get('from', now()->startOfMonth())),
            default   => now()->startOfMonth(),
        };
        $to = $period === 'custom'
            ? Carbon::parse($request->get('to', now()))
            : now();

        // Receitas (contas recebidas)
        $revenues = Receivable::where('company_id', $companyId)
            ->where('status', 'recebida')
            ->whereBetween('payment_date', [$from, $to])
            ->selectRaw('DATE(payment_date) as day, SUM(amount) as total')
            ->groupBy('day')->orderBy('day')->get();

        // Despesas (contas pagas)
        $expenses = Bill::where('company_id', $companyId)
            ->where('status', 'paga')
            ->whereBetween('payment_date', [$from, $to])
            ->selectRaw('DATE(payment_date) as day, SUM(amount) as total')
            ->groupBy('day')->orderBy('day')->get();

        $totalRevenue = $revenues->sum('total');
        $totalExpense = $expenses->sum('total');
        $netBalance   = $totalRevenue - $totalExpense;

        // Pendentes
        $pendingReceivables = Receivable::where('company_id', $companyId)->where('status','pendente')->sum('amount');
        $pendingBills       = Bill::where('company_id', $companyId)->where('status','pendente')->sum('amount');

        // Vencidos
        $overdueReceivables = Receivable::where('company_id', $companyId)->where('status','pendente')->whereDate('due_date','<', now())->sum('amount');
        $overdueBills       = Bill::where('company_id', $companyId)->where('status','pendente')->whereDate('due_date','<', now())->sum('amount');

        // Vendas do período
        $salesTotal = Sale::where('company_id', $companyId)
            ->where('status','concluida')
            ->whereBetween('sale_date', [$from, $to])
            ->sum('total');

        // Compras do período
        $purchasesTotal = PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['recebida'])
            ->whereBetween('order_date', [$from, $to])
            ->sum('total');

        return view('reports.financial', compact(
            'revenues','expenses','totalRevenue','totalExpense','netBalance',
            'pendingReceivables','pendingBills','overdueReceivables','overdueBills',
            'salesTotal','purchasesTotal','period','from','to'
        ));
    }

    public function purchases(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = PurchaseOrder::with(['supplier','items.product'])->where('company_id', $companyId);
        if ($request->filled('from')) { $query->whereDate('order_date', '>=', $request->from); }
        if ($request->filled('to'))   { $query->whereDate('order_date', '<=', $request->to); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $orders      = $query->orderByDesc('order_date')->paginate(15);
        $totalOrders = (clone $query)->count();
        $totalValue  = (clone $query)->sum('total');
        return view('reports.purchases', compact('orders','totalOrders','totalValue'));
    }
}
