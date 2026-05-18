<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Receivable;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function financial(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Período
        $period = $request->get('period', 'month');
        [$from, $to] = $this->resolvePeriod($period, $request);

        // Receitas: recebíveis pagos + vendas concluídas sem recebível
        $receivablesPaid = Receivable::where('company_id', $companyId)
            ->where('status', 'recebido')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        // Receitas pendentes
        $receivablesPending = Receivable::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$from, $to])
            ->sum('amount');

        // Receitas vencidas
        $receivablesOverdue = Receivable::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->where('due_date', '<', now())
            ->sum('amount');

        // Despesas pagas
        $billsPaid = Bill::where('company_id', $companyId)
            ->where('status', 'pago')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        // Despesas pendentes
        $billsPending = Bill::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->whereBetween('due_date', [$from, $to])
            ->sum('amount');

        // Despesas vencidas
        $billsOverdue = Bill::where('company_id', $companyId)
            ->where('status', 'pendente')
            ->where('due_date', '<', now())
            ->sum('amount');

        // Saldo líquido (realizado)
        $netBalance = $receivablesPaid - $billsPaid;

        // Projetado
        $projectedBalance = ($receivablesPaid + $receivablesPending) - ($billsPaid + $billsPending);

        // Receitas por mês (para gráfico — últimos 6 meses)
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

        // Lista de lançamentos do período
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
