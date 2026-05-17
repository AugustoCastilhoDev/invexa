<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private string $tz       = 'America/Sao_Paulo';
    private string $tzOffset = '-03:00';

    private function applyReturnDateFilter($query, ?string $from, ?string $to)
    {
        if ($from && $to) {
            $query->whereRaw(
                "CONVERT_TZ(created_at, '+00:00', ?) BETWEEN ? AND ?",
                [$this->tzOffset,
                 Carbon::parse($from, $this->tz)->startOfDay()->toDateTimeString(),
                 Carbon::parse($to,   $this->tz)->endOfDay()->toDateTimeString()]
            );
        }
        return $query;
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $interval  = $request->input('interval');
        $from      = $request->input('from');
        $to        = $request->input('to');

        if ($interval === 'today') {
            $from = now($this->tz)->toDateString();
            $to   = now($this->tz)->toDateString();
        } elseif ($interval === '7d') {
            $from = now($this->tz)->subDays(6)->toDateString();
            $to   = now($this->tz)->toDateString();
        } elseif ($interval === 'month') {
            $from = now($this->tz)->startOfMonth()->toDateString();
            $to   = now($this->tz)->toDateString();
        }

        $filteredQuery = $this->filteredSalesQuery($request, $from, $to);

        $periodSalesCount    = (clone $filteredQuery)->count();
        $periodRevenue       = (float)(clone $filteredQuery)->sum('total');
        $periodAverageTicket = $periodSalesCount > 0 ? $periodRevenue / $periodSalesCount : 0;
        $periodMaxSale       = (clone $filteredQuery)->max('total') ?? 0;
        $periodMinSale       = (clone $filteredQuery)->min('total') ?? 0;

        $stockSalesMovements = $this->stockManualQuery($companyId, 'venda', $from, $to)->get();
        $stockSalesRevenue   = $stockSalesMovements->sum(
            fn($m) => abs($m->quantity) * (float) optional($m->product)->price
        );
        $stockSalesCount = $stockSalesMovements->count();

        $periodRevenue      += $stockSalesRevenue;
        $periodSalesCount   += $stockSalesCount;
        $periodAverageTicket = $periodSalesCount > 0 ? $periodRevenue / $periodSalesCount : 0;

        $saleReturnsQuery = SaleReturn::where('company_id', $companyId);
        $this->applyReturnDateFilter($saleReturnsQuery, $from, $to);
        $returnsFromModule  = (float)(clone $saleReturnsQuery)->sum('total');
        $returnsCountModule = (clone $saleReturnsQuery)->count();

        $stockRetMovements = $this->stockManualQuery($companyId, 'devolucao', $from, $to)->get();
        $returnsFromStock  = $stockRetMovements->sum(
            fn($m) => abs($m->quantity) * (float) optional($m->product)->price
        );
        $returnsCountStock = $stockRetMovements->count();

        $periodReturnsTotal = $returnsFromModule + $returnsFromStock;
        $periodReturnsCount = $returnsCountModule + $returnsCountStock;
        $periodNetRevenue   = $periodRevenue - $periodReturnsTotal;

        $previousRevenue      = 0;
        $revenueChange        = 0;
        $revenueChangePercent = null;

        if ($from && $to) {
            $fromDate      = Carbon::parse($from, $this->tz)->startOfDay();
            $toDate        = Carbon::parse($to, $this->tz)->endOfDay();
            $periodDays    = $fromDate->diffInDays($toDate) + 1;
            $previousStart = $fromDate->copy()->subDays($periodDays);
            $previousEnd   = $fromDate->copy()->subDay();

            $previousRevenue = (float) Sale::where('company_id', $companyId)
                ->whereBetween('sale_date', [$previousStart, $previousEnd])
                ->sum('total');

            $revenueChange        = $periodNetRevenue - $previousRevenue;
            $revenueChangePercent = $previousRevenue > 0
                ? ($revenueChange / $previousRevenue) * 100
                : null;
        }

        $totalProducts   = Product::where('company_id', $companyId)->count();
        $totalCategories = Category::where('company_id', $companyId)->count();
        $totalSales      = $periodSalesCount;
        $totalRevenue    = $periodRevenue;
        $averageTicket   = $periodAverageTicket;

        $todayStr = now($this->tz)->toDateString();

        $salesToday = (float) Sale::where('company_id', $companyId)
            ->whereBetween('sale_date', [
                Carbon::parse($todayStr, $this->tz)->startOfDay(),
                Carbon::parse($todayStr, $this->tz)->endOfDay(),
            ])
            ->sum('total');
        $salesToday += $this->stockManualQuery($companyId, 'venda', $todayStr, $todayStr)
            ->get()->sum(fn($m) => abs($m->quantity) * (float) optional($m->product)->price);

        $returnsTodayQuery = SaleReturn::where('company_id', $companyId);
        $this->applyReturnDateFilter($returnsTodayQuery, $todayStr, $todayStr);
        $returnsTodayModule = (float)(clone $returnsTodayQuery)->sum('total');

        $returnsTodayStock = $this->stockManualQuery($companyId, 'devolucao', $todayStr, $todayStr)
            ->get()->sum(fn($m) => abs($m->quantity) * (float) optional($m->product)->price);

        $salesTodayNet = $salesToday - $returnsTodayModule - $returnsTodayStock;

        $lowStockProducts = Product::with('category')
            ->where('company_id', $companyId)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->orderBy('quantity')
            ->limit(8)
            ->get();

        $criticalStockProducts = Product::with('category')
            ->where('company_id', $companyId)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        $latestSales = Sale::where('company_id', $companyId)->latest()->limit(5)->get();

        $topSellingProducts = Product::select('products.id', 'products.name', 'products.quantity')
            ->selectRaw('COALESCE(SUM(sale_items.quantity), 0) as total_sold')
            ->leftJoin('sale_items', 'sale_items.product_id', '=', 'products.id')
            ->where('products.company_id', $companyId)
            ->groupBy('products.id', 'products.name', 'products.quantity')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $latestReturns = SaleReturn::with('sale')
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Gráfico de vendas ──
        $chartFrom = $from;
        $chartTo   = $to;
        if (!$chartFrom && !$chartTo && !$interval) {
            $chartFrom = now($this->tz)->subDays(30)->toDateString();
            $chartTo   = now($this->tz)->toDateString();
        }

        $chartQuery       = $this->filteredSalesQuery($request, $chartFrom, $chartTo);
        $salesByDayModule = $chartQuery
            ->selectRaw('DATE(sale_date) as day, SUM(total) as total')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('day')
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $salesByDayStock = $this->stockManualQuery($companyId, 'venda', $chartFrom, $chartTo)
            ->get()
            ->groupBy(fn($m) => $m->created_at->timezone($this->tz)->toDateString())
            ->map(fn($g) => $g->sum(fn($m) => abs($m->quantity) * (float) optional($m->product)->price));

        $allSaleDays = collect($salesByDayModule->keys())
            ->merge($salesByDayStock->keys())
            ->unique();
        $salesByDay = $allSaleDays->mapWithKeys(
            fn($d) => [$d => round((float)($salesByDayModule[$d] ?? 0) + (float)($salesByDayStock[$d] ?? 0), 2)]
        );

        $convertTzExpr      = "DATE(CONVERT_TZ(created_at, '+00:00', '{$this->tzOffset}'))";
        $returnsChartBase   = SaleReturn::where('company_id', $companyId);
        $this->applyReturnDateFilter($returnsChartBase, $chartFrom, $chartTo);
        $returnsByDayModule = $returnsChartBase
            ->selectRaw($convertTzExpr . ' as day, SUM(total) as total')
            ->groupBy(DB::raw($convertTzExpr))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $returnsByDayStock = $this->stockManualQuery($companyId, 'devolucao', $chartFrom, $chartTo)
            ->get()
            ->groupBy(fn($m) => $m->created_at->timezone($this->tz)->toDateString())
            ->map(fn($g) => $g->sum(fn($m) => abs($m->quantity) * (float) optional($m->product)->price));

        $allReturnDays = collect($returnsByDayModule->keys())
            ->merge($returnsByDayStock->keys())
            ->unique();
        $returnsByDay = $allReturnDays->mapWithKeys(
            fn($d) => [$d => round((float)($returnsByDayModule[$d] ?? 0) + (float)($returnsByDayStock[$d] ?? 0), 2)]
        );

        $allDays          = $salesByDay->keys()->merge($returnsByDay->keys())->unique()->sort()->values();
        $chartLabels      = $allDays->map(fn($d) => date('d/m', strtotime($d)));
        $chartData        = $allDays->map(fn($d) => (float)($salesByDay[$d] ?? 0));
        $chartReturnsData = $allDays->map(fn($d) => (float)($returnsByDay[$d] ?? 0));
        $chartNetData     = $allDays->map(
            fn($d) => round((float)($salesByDay[$d] ?? 0) - (float)($returnsByDay[$d] ?? 0), 2)
        );

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // MÓDULO 3.5 — Painel Financeiro (Bill = contas a pagar)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        $today   = now($this->tz)->startOfDay();
        $in7days = now($this->tz)->addDays(7)->endOfDay();

        // KPIs Contas a Receber
        $finReceivablePending = (float) Receivable::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->sum('amount');
        $finReceivableOverdue = (float) Receivable::where('company_id', $companyId)
            ->where('status', 'vencida')
            ->sum('amount');

        // KPIs Contas a Pagar (Bill)
        $finPayablePending = (float) Bill::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->sum('amount');
        $finPayableOverdue = (float) Bill::where('company_id', $companyId)
            ->where('status', 'vencida')
            ->sum('amount');

        // Saldo previsto = total a receber - total a pagar (pendentes/vencidas)
        $finCashBalance = $finReceivablePending - $finPayablePending;

        // Vencimentos próximos 7 dias
        $upcomingPayables = Bill::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereBetween('due_date', [$today, $in7days])
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $upcomingReceivables = Receivable::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereBetween('due_date', [$today, $in7days])
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        // Gráfico de fluxo de caixa (mês atual por vencimento)
        $cfStart = now($this->tz)->startOfMonth()->toDateString();
        $cfEnd   = now($this->tz)->endOfMonth()->toDateString();

        $cfReceivables = Receivable::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida', 'recebida'])
            ->whereBetween('due_date', [$cfStart, $cfEnd])
            ->selectRaw('DATE(due_date) as day, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(due_date)'))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $cfPayables = Bill::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida', 'paga'])
            ->whereBetween('due_date', [$cfStart, $cfEnd])
            ->selectRaw('DATE(due_date) as day, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(due_date)'))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $cfAllDays = collect($cfReceivables->keys())->merge($cfPayables->keys())->unique()->sort()->values();
        $cfLabels  = $cfAllDays->map(fn($d) => date('d/m', strtotime($d)));
        $cfDataRec = $cfAllDays->map(fn($d) => (float)($cfReceivables[$d] ?? 0));
        $cfDataPay = $cfAllDays->map(fn($d) => (float)($cfPayables[$d] ?? 0));

        return view('dashboard', compact(
            'totalProducts', 'totalCategories', 'totalSales', 'totalRevenue',
            'averageTicket', 'salesToday', 'salesTodayNet',
            'lowStockProducts', 'criticalStockProducts',
            'latestSales', 'topSellingProducts',
            'chartLabels', 'chartData', 'chartReturnsData', 'chartNetData',
            'from', 'to', 'interval',
            'periodSalesCount', 'periodRevenue', 'periodAverageTicket',
            'periodMaxSale', 'periodMinSale',
            'previousRevenue', 'revenueChange', 'revenueChangePercent',
            'periodReturnsTotal', 'periodReturnsCount', 'periodNetRevenue',
            'latestReturns',
            // 3.5 financeiro
            'finReceivablePending', 'finReceivableOverdue',
            'finPayablePending', 'finPayableOverdue', 'finCashBalance',
            'upcomingPayables', 'upcomingReceivables',
            'cfLabels', 'cfDataRec', 'cfDataPay'
        ));
    }

    private function stockManualQuery(int $companyId, string $reason, ?string $from, ?string $to)
    {
        $q = StockMovement::with('product')
            ->where('company_id', $companyId)
            ->where('reason', $reason)
            ->whereNull('source_type');

        if ($from && $to) {
            $q->whereBetween('created_at', [
                Carbon::parse($from, $this->tz)->startOfDay(),
                Carbon::parse($to,   $this->tz)->endOfDay(),
            ]);
        }

        return $q;
    }

    public function exportCsv(Request $request)
    {
        $sales    = $this->filteredSalesQuery($request)->with('items.product')->orderBy('sale_date')->get();
        $filename = 'vendas_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['ID', 'Data', 'Cliente', 'Status', 'Total', 'Notas']);
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->id,
                    optional($sale->sale_date)->format('d/m/Y H:i'),
                    $sale->customer_name,
                    $sale->status,
                    number_format($sale->total, 2, ',', '.'),
                    $sale->notes,
                ]);
            }
            fclose($handle);
        };
        return response()->streamDownload($callback, $filename, $headers);
    }

    public function exportPdf(Request $request)
    {
        $sales    = $this->filteredSalesQuery($request)->with('items.product')->orderBy('sale_date')->get();
        $from     = $request->input('from');
        $to       = $request->input('to');
        $interval = $request->input('interval');
        $pdf = Pdf::loadView('exports.sales-pdf', compact('sales', 'from', 'to', 'interval'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('vendas_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    private function filteredSalesQuery(Request $request, ?string $from = null, ?string $to = null)
    {
        $interval = $request->input('interval');
        if ($interval === 'today') {
            $from = now($this->tz)->toDateString();
            $to   = now($this->tz)->toDateString();
        } elseif ($interval === '7d') {
            $from = now($this->tz)->subDays(6)->toDateString();
            $to   = now($this->tz)->toDateString();
        } elseif ($interval === 'month') {
            $from = now($this->tz)->startOfMonth()->toDateString();
            $to   = now($this->tz)->toDateString();
        }
        $companyId = auth()->user()->company_id;
        $query     = Sale::where('company_id', $companyId);
        if ($from && $to) {
            $query->whereBetween('sale_date', [
                Carbon::parse($from, $this->tz)->startOfDay(),
                Carbon::parse($to, $this->tz)->endOfDay(),
            ]);
        } elseif ($from) {
            $query->where('sale_date', '>=', Carbon::parse($from, $this->tz)->startOfDay());
        } elseif ($to) {
            $query->where('sale_date', '<=', Carbon::parse($to, $this->tz)->endOfDay());
        }
        return $query;
    }
}
