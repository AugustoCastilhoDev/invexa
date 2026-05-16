<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function topProducts(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $period = $request->get('period', '30');

        $from = match ($period) {
            '7'    => Carbon::now()->subDays(7)->startOfDay(),
            '30'   => Carbon::now()->subDays(30)->startOfDay(),
            '90'   => Carbon::now()->subDays(90)->startOfDay(),
            '365'  => Carbon::now()->subDays(365)->startOfDay(),
            'custom' => Carbon::parse($request->get('from'))->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        $to = $period === 'custom'
            ? Carbon::parse($request->get('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $products = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', 'concluida')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->select(
                'products.id',
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as total_sales')
            )
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_qty')
            ->limit(20)
            ->get();

        // Dados para o gráfico (top 8)
        $chartLabels  = $products->take(8)->pluck('product_name');
        $chartQty     = $products->take(8)->pluck('total_qty');
        $chartRevenue = $products->take(8)->pluck('total_revenue');

        return view('reports.top-products', compact(
            'products', 'period', 'from', 'to',
            'chartLabels', 'chartQty', 'chartRevenue'
        ));
    }

    public function topProductsCsv(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $period    = $request->get('period', '30');

        $from = match ($period) {
            '7'    => Carbon::now()->subDays(7)->startOfDay(),
            '30'   => Carbon::now()->subDays(30)->startOfDay(),
            '90'   => Carbon::now()->subDays(90)->startOfDay(),
            '365'  => Carbon::now()->subDays(365)->startOfDay(),
            'custom' => Carbon::parse($request->get('from'))->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        $to = $period === 'custom'
            ? Carbon::parse($request->get('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $products = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', 'concluida')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->select(
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as total_sales')
            )
            ->groupBy('products.name', 'categories.name')
            ->orderByDesc('total_qty')
            ->get();

        $filename = 'produtos_mais_vendidos_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            // BOM para Excel PT-BR
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Produto', 'Categoria', 'Qtd. Vendida', 'Receita (R$)', 'Nº de Vendas'], ';');

            foreach ($products as $row) {
                fputcsv($file, [
                    $row->product_name,
                    $row->category_name ?? 'Sem categoria',
                    $row->total_qty,
                    number_format($row->total_revenue, 2, ',', '.'),
                    $row->total_sales,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
