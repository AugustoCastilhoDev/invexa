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
        $user      = auth()->user();
        $companyId = $user->company_id;

        // SuperAdmin sem empresa vinculada vai direto para o painel admin
        if (! $companyId) {
            if ($user->role === 'superadmin') {
                return redirect()->route('admin.index');
            }
            return redirect()->route('login')
                ->withErrors(['email' => 'Seu usuário não está vinculado a nenhuma empresa. Contate o administrador.']);
        }

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
            $toDate        = Carbon::parse($to,   $this->tz)->endOfDay();
            $periodDays    = $fromDate->diffInDays($toDate) + 1;
            $previousStart = $fromDate->copy()->subDays($periodDays);
            $previousEnd   = $fromDate->copy()->subDay()->endOfDay();

            $prevStartStr = $previousStart->toDateString();
            $prevEndStr   = $previousEnd->toDateString();

            // Receita anterior: vendas concluídas (módulo)
            $previousRevenueSales = (float) Sale::where('company_id', $companyId)
                ->where('status', 'concluida')
                ->whereBetween(DB::raw('COALESCE(sale_date, created_at)'), [$previousStart, $previousEnd])
                ->sum('total');

            // Receita anterior: movimentações manuais de estoque (venda)
            $previousRevenueStock = $this->stockManualQuery($companyId, 'venda', $prevStartStr, $prevEndStr)
                ->get()
                ->sum(fn($m) => abs($m->quantity) * (float) optional($m->product)->price);

            $previousRevenue = $previousRevenueSales + $previousRevenueStock;

            $revenueChange        = $periodNetRevenue - $previousRevenue;
            $revenueChangePercent = $previousRevenue > 0
                ? ($revenueChange / $previousRevenue) * 100
                : ($periodNetRevenue > 0 ? 100.0 : null);
        }

        $totalProducts   = Product::where('company_id', $companyId)->count();
        $totalCategories = Category::where('company_id', $companyId)->count();
        $totalSales      = $periodSalesCount;
        $totalRevenue    = $periodRevenue;
        $averageTicket   = $periodAverageTicket;

        $todayStr = now($this->tz)->toDateString();

        // Fluxo de caixa do dia — apenas vendas CONCLUÍDAS
        $salesToday = (float) Sale::where('company_id', $companyId)
            ->where('status', 'concluida')
            ->whereBetween(DB::raw('COALESCE(sale_date, created_at)'), [
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

        $topQuery = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', '!=', 'cancelada')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_sold'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5);

        if ($from && $to) {
            $topQuery->whereBetween(DB::raw('COALESCE(sales.sale_date, sales.created_at)'), [
                Carbon::parse($from, $this->tz)->startOfDay(),
                Carbon::parse($to,   $this->tz)->endOfDay(),
            ]);
        }

        $topSellingProducts = $topQuery->get();

        $topChartLabels  = $topSellingProducts->pluck('name');
        $topChartData    = $topSellingProducts->pluck('total_sold')->map(fn($v) => (int) $v);
        $topChartRevenue = $topSellingProducts->pluck('total_revenue')->map(fn($v) => round((float) $v, 2));

        $latestReturns = SaleReturn::with('sale')
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $chartFrom = $from;
        $chartTo   = $to;
        if (!$chartFrom && !$chartTo && !$interval) {
            $chartFrom = now($this->tz)->subDays(30)->toDateString();
            $chartTo   = now($this->tz)->toDateString();
        }

        // Gráfico de vendas — apenas concluídas
        $chartQuery       = $this->filteredSalesQuery($request, $chartFrom, $chartTo);
        $salesByDayModule = $chartQuery
            ->selectRaw('DATE(COALESCE(sale_date, created_at)) as day, SUM(total) as total')
            ->groupBy(DB::raw('DATE(COALESCE(sale_date, created_at))'))
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

        // Se não há dados reais no período, garante ao menos o dia de hoje no gráfico
        if ($allDays->isEmpty()) {
            $todayLabel       = now($this->tz)->format('d/m');
            $chartLabels      = collect([$todayLabel]);
            $chartData        = collect([0]);
            $chartReturnsData = collect([0]);
            $chartNetData     = collect([0]);
        }

        $todayDate  = now($this->tz)->toDateString();
        $in7Date    = now($this->tz)->addDays(7)->toDateString();

        $finReceivablePending = (float) Receivable::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->sum('amount');
        $finReceivableOverdue = (float) Receivable::where('company_id', $companyId)
            ->where('status', 'vencida')
            ->sum('amount');

        $finPayablePending = (float) Bill::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->sum('amount');
        $finPayableOverdue = (float) Bill::where('company_id', $companyId)
            ->where('status', 'vencida')
            ->sum('amount');

        $finCashBalance = $finReceivablePending - $finPayablePending;

        $upcomingPayables = Bill::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereDate('due_date', '>=', $todayDate)
            ->whereDate('due_date', '<=', $in7Date)
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $upcomingReceivables = Receivable::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereDate('due_date', '>=', $todayDate)
            ->whereDate('due_date', '<=', $in7Date)
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $cfFrom = $chartFrom ?? now($this->tz)->startOfMonth()->toDateString();
        $cfTo   = $chartTo   ?? now($this->tz)->endOfMonth()->toDateString();

        $cfFromDt = Carbon::parse($cfFrom, $this->tz)->startOfDay();
        $cfToDt   = Carbon::parse($cfTo,   $this->tz)->endOfDay();

        $cfRecPendente = Receivable::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereBetween('due_date', [$cfFrom, $cfTo])
            ->selectRaw('DATE(due_date) as day, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(due_date)'))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $cfRecRecebida = Receivable::where('company_id', $companyId)
            ->where('status', 'recebida')
            ->where(function ($q) use ($cfFromDt, $cfToDt) {
                $q->whereBetween('received_at', [$cfFromDt, $cfToDt])
                  ->orWhere(function ($q2) use ($cfFromDt, $cfToDt) {
                      $q2->whereNull('received_at')
                         ->whereBetween('due_date', [$cfFromDt->toDateString(), $cfToDt->toDateString()]);
                  });
            })
            ->selectRaw('DATE(COALESCE(received_at, due_date)) as day, SUM(amount_received) as total')
            ->groupBy(DB::raw('DATE(COALESCE(received_at, due_date))'))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $cfPayPendente = Bill::where('company_id', $companyId)
            ->whereIn('status', ['pendente', 'vencida'])
            ->whereBetween('due_date', [$cfFrom, $cfTo])
            ->selectRaw('DATE(due_date) as day, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(due_date)'))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $cfPayPaga = Bill::where('company_id', $companyId)
            ->where('status', 'paga')
            ->where(function ($q) use ($cfFromDt, $cfToDt) {
                $q->whereBetween('paid_at', [$cfFromDt, $cfToDt])
                  ->orWhere(function ($q2) use ($cfFromDt, $cfToDt) {
                      $q2->whereNull('paid_at')
                         ->whereBetween('due_date', [$cfFromDt->toDateString(), $cfToDt->toDateString()]);
                  });
            })
            ->selectRaw('DATE(COALESCE(paid_at, due_date)) as day, SUM(amount_paid) as total')
            ->groupBy(DB::raw('DATE(COALESCE(paid_at, due_date))'))
            ->pluck('total', 'day')
            ->map(fn($v) => (float) $v);

        $cfAllDays = collect($cfRecPendente->keys())
            ->merge($cfRecRecebida->keys())
            ->merge($cfPayPendente->keys())
            ->merge($cfPayPaga->keys())
            ->unique()->sort()->values();

        $cfLabels       = $cfAllDays->map(fn($d) => date('d/m', strtotime($d)));
        $cfDataRecPend  = $cfAllDays->map(fn($d) => (float)($cfRecPendente[$d] ?? 0));
        $cfDataRecReceb = $cfAllDays->map(fn($d) => (float)($cfRecRecebida[$d] ?? 0));
        $cfDataPay      = $cfAllDays->map(
            fn($d) => round((float)($cfPayPendente[$d] ?? 0) + (float)($cfPayPaga[$d] ?? 0), 2)
        );
        $cfDataPayPend  = $cfAllDays->map(fn($d) => (float)($cfPayPendente[$d] ?? 0));
        $cfDataPayPaga  = $cfAllDays->map(fn($d) => (float)($cfPayPaga[$d] ?? 0));

        $cfDataBalance  = collect();
        $runningBalance = 0.0;
        foreach ($cfAllDays as $d) {
            $runningBalance += (float)($cfRecPendente[$d]  ?? 0)
                             + (float)($cfRecRecebida[$d]  ?? 0)
                             - (float)($cfPayPendente[$d]  ?? 0)
                             - (float)($cfPayPaga[$d]      ?? 0);
            $cfDataBalance->push(round($runningBalance, 2));
        }

        // Fallback: garante ao menos um ponto no fluxo de caixa
        if ($cfAllDays->isEmpty()) {
            $todayLabel    = now($this->tz)->format('d/m');
            $cfLabels      = collect([$todayLabel]);
            $cfDataRecPend  = collect([0]);
            $cfDataRecReceb = collect([0]);
            $cfDataPay      = collect([0]);
            $cfDataPayPend  = collect([0]);
            $cfDataPayPaga  = collect([0]);
            $cfDataBalance  = collect([0]);
        }

        $mesesPt = ['janeiro','fevereiro','março','abril','maio','junho',
                    'julho','agosto','setembro','outubro','novembro','dezembro'];
        if ($interval === 'today') {
            $cfPeriodLabel = 'Hoje';
        } elseif ($interval === '7d') {
            $cfPeriodLabel = 'Últimos 7 dias';
        } elseif ($interval === 'month') {
            $cfPeriodLabel = 'Mês de ' . ucfirst($mesesPt[now($this->tz)->month - 1]);
        } elseif ($cfFrom && $cfTo && $cfFrom !== $cfTo) {
            $cfPeriodLabel = Carbon::parse($cfFrom)->format('d/m') . ' – ' . Carbon::parse($cfTo)->format('d/m/Y');
        } else {
            $cfPeriodLabel = ucfirst($mesesPt[now($this->tz)->month - 1]) . '/' . now($this->tz)->year;
        }

        return view('dashboard', compact(
            'totalProducts', 'totalCategories', 'totalSales', 'totalRevenue',
            'averageTicket', 'salesToday', 'salesTodayNet',
            'lowStockProducts', 'criticalStockProducts',
            'latestSales', 'topSellingProducts',
            'topChartLabels', 'topChartData', 'topChartRevenue',
            'chartLabels', 'chartData', 'chartReturnsData', 'chartNetData',
            'from', 'to', 'interval',
            'periodSalesCount', 'periodRevenue', 'periodAverageTicket',
            'periodMaxSale', 'periodMinSale',
            'previousRevenue', 'revenueChange', 'revenueChangePercent',
            'periodReturnsTotal', 'periodReturnsCount', 'periodNetRevenue',
            'latestReturns',
            'finReceivablePending', 'finReceivableOverdue',
            'finPayablePending', 'finPayableOverdue', 'finCashBalance',
            'upcomingPayables', 'upcomingReceivables',
            'cfLabels', 'cfDataRecPend', 'cfDataRecReceb',
            'cfDataPay', 'cfDataPayPend', 'cfDataPayPaga',
            'cfDataBalance', 'cfPeriodLabel'
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
        // Apenas vendas concluídas contribuem para faturamento e gráficos
        $query = Sale::where('company_id', $companyId)
                     ->where('status', 'concluida');
        if ($from && $to) {
            $query->whereBetween(
                DB::raw('COALESCE(sale_date, created_at)'),
                [
                    Carbon::parse($from, $this->tz)->startOfDay(),
                    Carbon::parse($to,   $this->tz)->endOfDay(),
                ]
            );
        } elseif ($from) {
            $query->where(DB::raw('COALESCE(sale_date, created_at)'), '>=', Carbon::parse($from, $this->tz)->startOfDay());
        } elseif ($to) {
            $query->where(DB::raw('COALESCE(sale_date, created_at)'), '<=', Carbon::parse($to, $this->tz)->endOfDay());
        }
        return $query;
    }
}
