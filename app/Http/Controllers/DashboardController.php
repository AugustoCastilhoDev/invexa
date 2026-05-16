<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $interval  = $request->input('interval');
        $from      = $request->input('from');
        $to        = $request->input('to');

        if ($interval === 'today') {
            $from = now()->toDateString();
            $to   = now()->toDateString();
        } elseif ($interval === '7d') {
            $from = now()->subDays(6)->toDateString();
            $to   = now()->toDateString();
        } elseif ($interval === 'month') {
            $from = now()->startOfMonth()->toDateString();
            $to   = now()->toDateString();
        }

        $filteredQuery = $this->filteredSalesQuery($request, $from, $to);

        // ── Métricas brutas de vendas ────────────────────────────────────
        $periodSalesCount    = (clone $filteredQuery)->count();
        $periodRevenue       = (float)(clone $filteredQuery)->sum('total');
        $periodAverageTicket = $periodSalesCount > 0 ? $periodRevenue / $periodSalesCount : 0;
        $periodMaxSale       = (clone $filteredQuery)->max('total') ?? 0;
        $periodMinSale       = (clone $filteredQuery)->min('total') ?? 0;

        // ── Devoluções do período ────────────────────────────────────────
        $returnsQuery = SaleReturn::where('company_id', $companyId);
        if ($from && $to) {
            $returnsQuery->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }
        $periodReturnsTotal = (float)(clone $returnsQuery)->sum('total');
        $periodReturnsCount = (clone $returnsQuery)->count();
        $periodNetRevenue   = $periodRevenue - $periodReturnsTotal;

        // ── Variação vs período anterior ───────────────────────────────
        $previousRevenue      = 0;
        $revenueChange        = 0;
        $revenueChangePercent = null;

        if ($from && $to) {
            $fromDate      = Carbon::parse($from)->startOfDay();
            $toDate        = Carbon::parse($to)->endOfDay();
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

        // ── Totais gerais ────────────────────────────────────────────────
        $totalProducts   = Product::where('company_id', $companyId)->count();
        $totalCategories = Category::where('company_id', $companyId)->count();
        $totalSales      = (clone $filteredQuery)->count();
        $totalRevenue    = (float)(clone $filteredQuery)->sum('total');
        $averageTicket   = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        $salesToday = (float) Sale::where('company_id', $companyId)
            ->whereBetween('sale_date', [now()->startOfDay(), now()->endOfDay()])
            ->sum('total');

        // Devoluções de hoje (para "Venda de hoje" líquida)
        $returnsTodayTotal = (float) SaleReturn::where('company_id', $companyId)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->sum('total');
        $salesTodayNet = $salesToday - $returnsTodayTotal;

        // ── Estoque e listas ─────────────────────────────────────────────
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

        // ── Últimas devoluções ─────────────────────────────────────────
        $latestReturns = SaleReturn::with('sale')
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Gráfico: receita líquida por dia (bruto − devoluções) ───────
        $chartQuery = $this->filteredSalesQuery($request, $from, $to);
        if (!$from && !$to && !$interval) {
            $chartQuery->whereBetween('sale_date', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // Vendas brutas por dia
        $salesByDay = $chartQuery
            ->selectRaw('DATE(sale_date) as day, SUM(total) as total')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('day')
            ->pluck('total', 'day');

        // Devoluções por dia (mesmo intervalo)
        $returnsChartQuery = SaleReturn::where('company_id', $companyId);
        if ($from && $to) {
            $returnsChartQuery->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        } elseif (!$from && !$to && !$interval) {
            $returnsChartQuery->whereBetween('created_at', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay(),
            ]);
        }
        $returnsByDay = $returnsChartQuery
            ->selectRaw('DATE(created_at) as day, SUM(total) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'day');

        // Mescla: para cada dia com venda, subtrai devoluções do mesmo dia
        $allDays    = $salesByDay->keys()->merge($returnsByDay->keys())->unique()->sort()->values();
        $chartLabels = $allDays->map(fn($d) => date('d/m', strtotime($d)));
        $chartData   = $allDays->map(fn($d) =>
            max(0, (float)($salesByDay[$d] ?? 0) - (float)($returnsByDay[$d] ?? 0))
        );
        // Dataset secundário: devoluções (para exibir no tooltip)
        $chartReturnsData = $allDays->map(fn($d) => (float)($returnsByDay[$d] ?? 0));

        return view('dashboard', compact(
            'totalProducts', 'totalCategories', 'totalSales', 'totalRevenue',
            'averageTicket', 'salesToday', 'salesTodayNet',
            'lowStockProducts', 'criticalStockProducts',
            'latestSales', 'topSellingProducts',
            'chartLabels', 'chartData', 'chartReturnsData',
            'from', 'to', 'interval',
            'periodSalesCount', 'periodRevenue', 'periodAverageTicket',
            'periodMaxSale', 'periodMinSale',
            'previousRevenue', 'revenueChange', 'revenueChangePercent',
            'periodReturnsTotal', 'periodReturnsCount', 'periodNetRevenue',
            'latestReturns'
        ));
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
            $from = now()->toDateString();
            $to   = now()->toDateString();
        } elseif ($interval === '7d') {
            $from = now()->subDays(6)->toDateString();
            $to   = now()->toDateString();
        } elseif ($interval === 'month') {
            $from = now()->startOfMonth()->toDateString();
            $to   = now()->toDateString();
        }
        $companyId = auth()->user()->company_id;
        $query     = Sale::where('company_id', $companyId);
        if ($from && $to) {
            $query->whereBetween('sale_date', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        } elseif ($from) {
            $query->where('sale_date', '>=', Carbon::parse($from)->startOfDay());
        } elseif ($to) {
            $query->where('sale_date', '<=', Carbon::parse($to)->endOfDay());
        }
        return $query;
    }
}
