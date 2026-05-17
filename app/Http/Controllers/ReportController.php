<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ── index = página principal (produtos mais vendidos) ───────────────
    public function index(Request $request)
    {
        return $this->topProducts($request);
    }

    public function export(Request $request)
    {
        return $this->topProductsCsv($request);
    }

    // ── Helpers compartilhados ────────────────────────────────────

    private function resolvePeriod(Request $request): array
    {
        $period = $request->get('period', '30');

        $from = match ($period) {
            '7'      => Carbon::now()->subDays(7)->startOfDay(),
            '30'     => Carbon::now()->subDays(30)->startOfDay(),
            '90'     => Carbon::now()->subDays(90)->startOfDay(),
            '365'    => Carbon::now()->subDays(365)->startOfDay(),
            'custom' => Carbon::parse($request->get('from'))->startOfDay(),
            default  => Carbon::now()->subDays(30)->startOfDay(),
        };

        $to = $period === 'custom'
            ? Carbon::parse($request->get('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        return [$period, $from, $to];
    }

    // ── Produtos mais vendidos ──────────────────────────────────

    private function getProductsQuery(string $companyId, Carbon $from, Carbon $to)
    {
        $returnedSub = DB::table('sale_return_items')
            ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
            ->join('sales as s2', 'sale_returns.sale_id', '=', 's2.id')
            ->where('s2.company_id', $companyId)
            ->whereBetween('s2.sale_date', [$from, $to])
            ->select(
                'sale_return_items.product_id',
                DB::raw('SUM(sale_return_items.quantity) as returned_qty'),
                DB::raw('SUM(sale_return_items.subtotal) as returned_revenue')
            )
            ->groupBy('sale_return_items.product_id');

        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoinSub($returnedSub, 'ret', 'ret.product_id', '=', 'sale_items.product_id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', 'concluida')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->select(
                'products.id',
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) - COALESCE(MAX(ret.returned_qty), 0) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) - COALESCE(MAX(ret.returned_revenue), 0) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as total_sales')
            )
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_qty')
            ->limit(20)
            ->get();
    }

    public function topProducts(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$period, $from, $to] = $this->resolvePeriod($request);

        $products = $this->getProductsQuery($companyId, $from, $to);

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
        [, $from, $to] = $this->resolvePeriod($request);

        $products = $this->getProductsQuery($companyId, $from, $to);

        $filename = 'produtos_mais_vendidos_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Produto', 'Categoria', 'Qtd. Líquida Vendida', 'Receita Líquida (R$)', 'Nº de Vendas'], ';');

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

    public function topProductsPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$period, $from, $to] = $this->resolvePeriod($request);

        $products = $this->getProductsQuery($companyId, $from, $to);

        $pdf = Pdf::loadView('exports.top-products-pdf', compact('products', 'from', 'to', 'period'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('produtos_mais_vendidos_' . now()->format('Ymd_His') . '.pdf');
    }

    // ── Relatório de Compras ────────────────────────────────────

    public function purchases(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$period, $from, $to] = $this->resolvePeriod($request);

        // Filtros opcionais
        $supplierId = $request->get('supplier_id');
        $status     = $request->get('status');

        // ─ Query base ────────────────────────────────────────────────────────
        $query = PurchaseOrder::with('supplier')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('created_at')->get();

        // ─ KPIs ────────────────────────────────────────────────────────────
        $totalOrders    = $orders->count();
        $totalValue     = $orders->whereIn('status', ['enviada', 'recebida_parcial', 'recebida'])->sum('total');
        $receivedValue  = $orders->where('status', 'recebida')->sum('total');
        $pendingValue   = $orders->whereIn('status', ['enviada', 'recebida_parcial'])->sum('total');

        // ─ Agrupamento por fornecedor ─────────────────────────────────
        $bySupplier = $orders
            ->whereIn('status', ['enviada', 'recebida_parcial', 'recebida'])
            ->groupBy('supplier_id')
            ->map(fn($group) => [
                'name'   => optional($group->first()->supplier)->name ?? 'Desconhecido',
                'total'  => $group->sum('total'),
                'count'  => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        // ─ Itens mais comprados no período ────────────────────────────
        $topItemsQuery = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('purchase_orders.company_id', $companyId)
            ->whereIn('purchase_orders.status', ['enviada', 'recebida_parcial', 'recebida'])
            ->whereBetween('purchase_orders.created_at', [$from, $to]);

        if ($supplierId) {
            $topItemsQuery->where('purchase_orders.supplier_id', $supplierId);
        }

        $topItems = $topItemsQuery
            ->select(
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(purchase_order_items.quantity) as total_qty'),
                DB::raw('SUM(purchase_order_items.subtotal) as total_cost'),
                DB::raw('COUNT(DISTINCT purchase_orders.id) as total_orders')
            )
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_cost')
            ->limit(15)
            ->get();

        // ─ Lista de fornecedores para filtro ───────────────────────────
        $suppliers = \App\Models\Supplier::where('company_id', $companyId)
            ->orderBy('name')->get();

        return view('reports.purchases', compact(
            'orders', 'period', 'from', 'to',
            'supplierId', 'status',
            'totalOrders', 'totalValue', 'receivedValue', 'pendingValue',
            'bySupplier', 'topItems', 'suppliers'
        ));
    }

    public function purchasesCsv(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$period, $from, $to] = $this->resolvePeriod($request);

        $supplierId = $request->get('supplier_id');
        $status     = $request->get('status');

        $query = PurchaseOrder::with('supplier', 'items.product')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to]);

        if ($supplierId) $query->where('supplier_id', $supplierId);
        if ($status)     $query->where('status', $status);

        $orders = $query->orderByDesc('created_at')->get();

        $filename = 'relatorio_compras_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'Número OC', 'Fornecedor', 'Status', 'Data Criação',
                'Previsão Entrega', 'Data Recebimento', 'Total (R$)', 'Observações',
            ], ';');

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->number,
                    optional($order->supplier)->name ?? '',
                    $order->status_label,
                    $order->created_at->format('d/m/Y'),
                    $order->expected_date?->format('d/m/Y') ?? '',
                    $order->received_at?->format('d/m/Y') ?? '',
                    number_format($order->total, 2, ',', '.'),
                    $order->notes ?? '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
