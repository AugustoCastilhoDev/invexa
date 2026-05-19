<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ---------------------------------------------------------------
    // Relatorio de Compras
    // ---------------------------------------------------------------
    public function purchases(Request $request)
    {
        [$companyId, $period, $from, $to, $supplierId, $status, $orders] =
            $this->purchasesData($request);

        $totalOrders   = $orders->count();
        $totalValue    = $orders->whereNotIn('status', ['cancelada'])->sum('total');
        $receivedValue = $orders->where('status', 'recebida')->sum('total');
        $pendingValue  = $orders->where('status', 'pendente')->sum('total');

        $bySupplier = $orders->whereNotIn('status', ['cancelada'])
            ->groupBy('supplier_id')
            ->map(fn($g) => [
                'name'  => optional($g->first()->supplier)->name ?? '(sem fornecedor)',
                'count' => $g->count(),
                'total' => $g->sum('total'),
            ])
            ->sortByDesc('total')
            ->values();

        $topItems  = $this->topItemsQuery($companyId, $from, $to, $supplierId);
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get(['id', 'name']);

        return view('reports.purchases', compact(
            'period', 'from', 'to',
            'supplierId', 'status',
            'orders',
            'totalOrders', 'totalValue', 'receivedValue', 'pendingValue',
            'bySupplier', 'topItems',
            'suppliers'
        ));
    }

    public function purchasesCsv(Request $request): Response
    {
        [, , $from, $to, , , $orders] = $this->purchasesData($request);
        $filename = 'compras_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        $lines[] = implode(';', ['Numero', 'Fornecedor', 'Status', 'Emissao', 'Recebimento', 'Total']);
        foreach ($orders as $o) {
            $lines[] = implode(';', [
                $o->number,
                optional($o->supplier)->name ?? '',
                $o->status_label ?? $o->status,
                $o->created_at->format('d/m/Y'),
                $o->received_at ? $o->received_at->format('d/m/Y') : '',
                number_format($o->total, 2, ',', '.'),
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function purchasesPdf(Request $request): Response
    {
        [, $period, $from, $to, $supplierId, $status, $orders] = $this->purchasesData($request);

        $totalValue    = $orders->whereNotIn('status', ['cancelada'])->sum('total');
        $receivedValue = $orders->where('status', 'recebida')->sum('total');
        $pendingValue  = $orders->where('status', 'pendente')->sum('total');

        $html = view('reports.purchases-pdf', compact(
            'from', 'to', 'orders', 'totalValue', 'receivedValue', 'pendingValue'
        ))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Relatorio Financeiro
    // ---------------------------------------------------------------
    public function financial(Request $request)
    {
        [$companyId, $period, $from, $to, $receivables, $bills,
         $receivablesPaid, $receivablesPending, $receivablesOverdue,
         $billsPaid, $billsPending, $billsOverdue,
         $netBalance, $projectedBalance] = $this->financialData($request);

        return view('reports.financial', compact(
            'period', 'from', 'to',
            'receivablesPaid', 'receivablesPending', 'receivablesOverdue',
            'billsPaid', 'billsPending', 'billsOverdue',
            'netBalance', 'projectedBalance',
            'receivables', 'bills'
        ));
    }

    public function financialCsv(Request $request): Response
    {
        [, , $from, $to, $receivables, $bills] = $this->financialData($request);
        $filename = 'financeiro_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        $lines[] = implode(';', ['Tipo', 'Descricao', 'Vencimento', 'Valor', 'Status']);
        foreach ($receivables as $r) {
            $lines[] = implode(';', [
                'Receita',
                $r->description,
                Carbon::parse($r->due_date)->format('d/m/Y'),
                number_format($r->amount, 2, ',', '.'),
                ucfirst($r->status),
            ]);
        }
        foreach ($bills as $b) {
            $lines[] = implode(';', [
                'Despesa',
                $b->description,
                Carbon::parse($b->due_date)->format('d/m/Y'),
                number_format($b->amount, 2, ',', '.'),
                ucfirst($b->status),
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function financialPdf(Request $request): Response
    {
        [, , $from, $to, $receivables, $bills,
         $receivablesPaid, $receivablesPending, $receivablesOverdue,
         $billsPaid, $billsPending, $billsOverdue,
         $netBalance, $projectedBalance] = $this->financialData($request);

        $html = view('reports.financial-pdf', compact(
            'from', 'to', 'receivables', 'bills',
            'receivablesPaid', 'receivablesPending', 'receivablesOverdue',
            'billsPaid', 'billsPending', 'billsOverdue',
            'netBalance', 'projectedBalance'
        ))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Relatorio de Vendas
    // ---------------------------------------------------------------
    public function sales(Request $request)
    {
        [$companyId, $period, $from, $to, $sales,
         $totalSales, $totalRevenue, $totalCanceled] = $this->salesData($request);

        return view('reports.sales', compact(
            'period', 'from', 'to',
            'sales', 'totalSales', 'totalRevenue', 'totalCanceled'
        ));
    }

    public function salesCsv(Request $request): Response
    {
        [, , $from, $to, $sales] = $this->salesData($request);
        $filename = 'vendas_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        $lines[] = implode(';', ['No Venda', 'Cliente', 'Data', 'Status', 'Total']);
        foreach ($sales as $s) {
            $lines[] = implode(';', [
                $s->id,
                optional($s->customer)->name ?? 'Consumidor',
                $s->created_at->format('d/m/Y'),
                ucfirst($s->status),
                number_format($s->total, 2, ',', '.'),
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function salesPdf(Request $request): Response
    {
        [, , $from, $to, $sales, $totalSales, $totalRevenue, $totalCanceled] = $this->salesData($request);

        $html = view('reports.sales-pdf', compact(
            'from', 'to', 'sales', 'totalSales', 'totalRevenue', 'totalCanceled'
        ))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Relatorio de Contas a Pagar
    // ---------------------------------------------------------------
    public function bills(Request $request)
    {
        [$companyId, $period, $from, $to, $bills,
         $totalPaid, $totalPending, $totalOverdue] = $this->billsData($request);

        return view('reports.bills', compact(
            'period', 'from', 'to',
            'bills', 'totalPaid', 'totalPending', 'totalOverdue'
        ));
    }

    public function billsCsv(Request $request): Response
    {
        [, , $from, $to, $bills] = $this->billsData($request);
        $filename = 'contas_pagar_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        $lines[] = implode(';', ['Descricao', 'Fornecedor', 'Vencimento', 'Valor', 'Status']);
        foreach ($bills as $b) {
            $lines[] = implode(';', [
                $b->description,
                optional($b->supplier)->name ?? '',
                Carbon::parse($b->due_date)->format('d/m/Y'),
                number_format($b->amount, 2, ',', '.'),
                ucfirst($b->status),
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function billsPdf(Request $request): Response
    {
        [, , $from, $to, $bills, $totalPaid, $totalPending, $totalOverdue] = $this->billsData($request);

        $html = view('reports.bills-pdf', compact(
            'from', 'to', 'bills', 'totalPaid', 'totalPending', 'totalOverdue'
        ))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Relatorio de Estoque
    // ---------------------------------------------------------------
    public function stock(Request $request)
    {
        [$companyId, $filter, $products, $totalActive, $totalLow, $totalValue] = $this->stockData($request);

        return view('reports.stock', compact(
            'filter', 'products', 'totalActive', 'totalLow', 'totalValue'
        ));
    }

    public function stockCsv(Request $request): Response
    {
        [, , $products] = $this->stockData($request);
        $filename = 'estoque_' . now()->format('Ymd') . '.csv';

        $lines[] = implode(';', ['Produto', 'Categoria', 'Qtd. em Estoque', 'Estoque Min.', 'Preco de Custo', 'Preco de Venda', 'Status']);
        foreach ($products as $p) {
            if (!$p->active) {
                $statusLabel = 'Inativo';
            } elseif ($p->min_quantity > 0 && $p->quantity <= $p->min_quantity) {
                $statusLabel = 'Estoque Baixo';
            } else {
                $statusLabel = 'OK';
            }
            $lines[] = implode(';', [
                $p->name,
                optional($p->category)->name ?? '',
                $p->quantity,
                $p->min_quantity ?? 0,
                number_format($p->cost_price ?? 0, 2, ',', '.'),
                number_format($p->price, 2, ',', '.'),
                $statusLabel,
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function stockPdf(Request $request): Response
    {
        [, , $products, $totalActive, $totalLow, $totalValue] = $this->stockData($request);

        $html = view('reports.stock-pdf', compact(
            'products', 'totalActive', 'totalLow', 'totalValue'
        ))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Relatorio de Fornecedores
    // ---------------------------------------------------------------
    public function suppliers(Request $request)
    {
        [$companyId, $suppliers, $total] = $this->suppliersData($request);

        return view('reports.suppliers', compact('suppliers', 'total'));
    }

    public function suppliersCsv(Request $request): Response
    {
        [, $suppliers] = $this->suppliersData($request);
        $filename = 'fornecedores_' . now()->format('Ymd') . '.csv';

        $lines[] = implode(';', ['Nome', 'CNPJ/CPF', 'E-mail', 'Telefone', 'Cidade', 'UF']);
        foreach ($suppliers as $s) {
            $lines[] = implode(';', [
                $s->name,
                $s->document ?? '',
                $s->email ?? '',
                $s->phone ?? '',
                $s->city ?? '',
                $s->state ?? '',
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function suppliersPdf(Request $request): Response
    {
        [, $suppliers, $total] = $this->suppliersData($request);

        $html = view('reports.suppliers-pdf', compact('suppliers', 'total'))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Relatorio de Produtos Mais Vendidos
    // ---------------------------------------------------------------
    public function topProducts(Request $request)
    {
        [$companyId, $period, $from, $to, $sortBy, $products, $chartLabels, $chartData, $chartLabel] =
            $this->topProductsData($request);

        return view('reports.top-products', compact(
            'period', 'from', 'to', 'sortBy', 'products', 'chartLabels', 'chartData', 'chartLabel'
        ));
    }

    public function topProductsCsv(Request $request): Response
    {
        [, , $from, $to, , $products] = $this->topProductsData($request);
        $filename = 'produtos_mais_vendidos_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        $lines[] = implode(';', ['#', 'Produto', 'Categoria', 'Qtd. Vendida', 'No de Vendas', 'Receita Total', 'Ticket Medio']);
        foreach ($products as $i => $p) {
            $ticketMedio = $p->total_sales > 0
                ? number_format($p->total_revenue / $p->total_sales, 2, ',', '.')
                : '0,00';
            $lines[] = implode(';', [
                $i + 1,
                $p->product_name,
                $p->category_name ?? 'Sem categoria',
                number_format($p->total_qty, 0, ',', '.'),
                $p->total_sales,
                number_format($p->total_revenue, 2, ',', '.'),
                $ticketMedio,
            ]);
        }

        return $this->csvResponse("\xEF\xBB\xBF" . implode("\n", $lines), $filename);
    }

    public function topProductsPdf(Request $request): Response
    {
        [, , $from, $to, , $products] = $this->topProductsData($request);

        $html = view('reports.top-products-pdf', compact('from', 'to', 'products'))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // ---------------------------------------------------------------
    // Helpers de dados
    // ---------------------------------------------------------------
    private function financialData(Request $request): array
    {
        $companyId = auth()->user()->company_id;
        $period    = $request->get('period', 'month');
        [$from, $to] = $this->resolvePeriod($period, $request);

        $receivablesPaid    = Receivable::where('company_id', $companyId)->where('status', 'recebido')->whereBetween('paid_at', [$from, $to])->sum('amount');
        $receivablesPending = Receivable::where('company_id', $companyId)->where('status', 'pendente')->whereBetween('due_date', [$from, $to])->sum('amount');
        $receivablesOverdue = Receivable::where('company_id', $companyId)->where('status', 'pendente')->where('due_date', '<', now())->sum('amount');
        $billsPaid          = Bill::where('company_id', $companyId)->where('status', 'pago')->whereBetween('paid_at', [$from, $to])->sum('amount');
        $billsPending       = Bill::where('company_id', $companyId)->where('status', 'pendente')->whereBetween('due_date', [$from, $to])->sum('amount');
        $billsOverdue       = Bill::where('company_id', $companyId)->where('status', 'pendente')->where('due_date', '<', now())->sum('amount');

        $netBalance       = $receivablesPaid - $billsPaid;
        $projectedBalance = ($receivablesPaid + $receivablesPending) - ($billsPaid + $billsPending);

        $receivables = Receivable::with('customer')->where('company_id', $companyId)->whereBetween('due_date', [$from, $to])->orderBy('due_date')->get();
        $bills       = Bill::with('supplier')->where('company_id', $companyId)->whereBetween('due_date', [$from, $to])->orderBy('due_date')->get();

        return [
            $companyId, $period, $from, $to, $receivables, $bills,
            $receivablesPaid, $receivablesPending, $receivablesOverdue,
            $billsPaid, $billsPending, $billsOverdue,
            $netBalance, $projectedBalance,
        ];
    }

    private function salesData(Request $request): array
    {
        $companyId = auth()->user()->company_id;
        $period    = $request->get('period', 'month');
        [$from, $to] = $this->resolvePeriod($period, $request);

        $sales = Sale::with('customer')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get();

        $totalSales    = $sales->count();
        $totalRevenue  = $sales->where('status', '!=', 'cancelada')->sum('total');
        $totalCanceled = $sales->where('status', 'cancelada')->count();

        return [$companyId, $period, $from, $to, $sales, $totalSales, $totalRevenue, $totalCanceled];
    }

    private function billsData(Request $request): array
    {
        $companyId = auth()->user()->company_id;
        $period    = $request->get('period', 'month');
        [$from, $to] = $this->resolvePeriod($period, $request);

        $bills = Bill::with('supplier')
            ->where('company_id', $companyId)
            ->whereBetween('due_date', [$from, $to])
            ->orderBy('due_date')
            ->get();

        $totalPaid    = $bills->where('status', 'pago')->sum('amount');
        $totalPending = $bills->where('status', 'pendente')->sum('amount');
        $totalOverdue = $bills->where('status', 'pendente')->where('due_date', '<', now()->format('Y-m-d'))->sum('amount');

        return [$companyId, $period, $from, $to, $bills, $totalPaid, $totalPending, $totalOverdue];
    }

    private function stockData(Request $request): array
    {
        $companyId = auth()->user()->company_id;
        $filter    = $request->get('filter', 'all');

        $query = Product::with('category')
            ->where('company_id', $companyId)
            ->orderBy('name');

        if ($filter === 'inactive') {
            $query->where('active', 0);
        } elseif ($filter === 'low') {
            $query->where('active', 1)
                  ->whereColumn('quantity', '<=', 'min_quantity')
                  ->where('min_quantity', '>', 0);
        } else {
            $query->where('active', 1);
        }

        $products    = $query->get();
        $totalActive = $products->count();
        $totalLow    = $products->filter(fn($p) => $p->active && $p->min_quantity > 0 && $p->quantity <= $p->min_quantity)->count();
        $totalValue  = $products->sum(fn($p) => ($p->cost_price ?? $p->price) * $p->quantity);

        return [$companyId, $filter, $products, $totalActive, $totalLow, $totalValue];
    }

    private function suppliersData(Request $request): array
    {
        $companyId = auth()->user()->company_id;

        $suppliers = Supplier::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $total = $suppliers->count();

        return [$companyId, $suppliers, $total];
    }

    private function topProductsData(Request $request): array
    {
        $companyId = auth()->user()->company_id;
        $period    = $request->get('period', '30');
        $sortBy    = $request->get('sort_by', 'total_qty');

        $allowedSorts = ['total_qty', 'total_revenue', 'total_sales'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'total_qty';
        }

        [$from, $to] = $this->resolvePurchasePeriod($period, $request);

        $products = SaleItem::query()
            ->join('sales as s', function ($join) {
                $join->on('s.id', '=', 'sale_items.sale_id')
                     ->whereNull('s.deleted_at');
            })
            ->join('products as p', 'p.id', '=', 'sale_items.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('s.company_id', $companyId)
            ->whereNotIn('s.status', ['cancelada'])
            ->whereBetween('s.created_at', [$from, $to])
            ->groupBy('sale_items.product_id', 'p.name', 'c.name')
            ->orderByDesc($sortBy)
            ->limit(20)
            ->selectRaw(
                'sale_items.product_id,' .
                'p.name AS product_name,' .
                'c.name AS category_name,' .
                'SUM(sale_items.quantity) AS total_qty,' .
                'COUNT(DISTINCT sale_items.sale_id) AS total_sales,' .
                'SUM(sale_items.subtotal) AS total_revenue'
            )
            ->get();

        $top8 = $products->take(8);
        $chartLabels = $top8->pluck('product_name')->toArray();

        if ($sortBy === 'total_revenue') {
            $chartData  = $top8->pluck('total_revenue')->toArray();
            $chartLabel = 'Receita (R$)';
        } elseif ($sortBy === 'total_sales') {
            $chartData  = $top8->pluck('total_sales')->toArray();
            $chartLabel = 'No de Vendas';
        } else {
            $chartData  = $top8->pluck('total_qty')->toArray();
            $chartLabel = 'Unidades Vendidas';
        }

        return [$companyId, $period, $from, $to, $sortBy, $products, $chartLabels, $chartData, $chartLabel];
    }

    // ---------------------------------------------------------------
    // Helpers compartilhados
    // ---------------------------------------------------------------
    private function purchasesData(Request $request): array
    {
        $companyId  = auth()->user()->company_id;
        $period     = $request->get('period', '30');
        $supplierId = $request->get('supplier_id');
        $status     = $request->get('status');

        [$from, $to] = $this->resolvePurchasePeriod($period, $request);

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

        return [$companyId, $period, $from, $to, $supplierId, $status, $orders];
    }

    private function topItemsQuery(int $companyId, Carbon $from, Carbon $to, ?string $supplierId)
    {
        return DB::table('purchase_order_items as poi')
            ->join('purchase_orders as po', 'po.id', '=', 'poi.purchase_order_id')
            ->join('products as p', 'p.id', '=', 'poi.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('po.company_id', $companyId)
            ->whereNotIn('po.status', ['cancelada'])
            ->whereBetween('po.order_date', [$from, $to])
            ->when($supplierId, fn($q) => $q->where('po.supplier_id', $supplierId))
            ->selectRaw(
                'p.name AS product_name,' .
                'c.name AS category_name,' .
                'SUM(poi.quantity) AS total_qty,' .
                'COUNT(DISTINCT po.id) AS total_orders,' .
                'SUM(poi.subtotal) AS total_cost'
            )
            ->groupBy('poi.product_id', 'p.name', 'c.name')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();
    }

    private function resolvePurchasePeriod(string $period, Request $request): array
    {
        if ($period === 'custom') {
            $from = Carbon::parse($request->get('from', now()->subDays(30)))->startOfDay();
            $to   = Carbon::parse($request->get('to', now()))->endOfDay();
            return [$from, $to];
        }

        $days = is_numeric($period) ? (int) $period : 30;
        return [now()->subDays($days - 1)->startOfDay(), now()->endOfDay()];
    }

    private function resolvePeriod(string $period, Request $request): array
    {
        if ($period === 'week') {
            return [now()->startOfWeek(), now()->endOfWeek()];
        }

        if ($period === 'month') {
            return [now()->startOfMonth(), now()->endOfMonth()];
        }

        if ($period === 'quarter') {
            return [now()->startOfQuarter(), now()->endOfQuarter()];
        }

        if ($period === 'year') {
            return [now()->startOfYear(), now()->endOfYear()];
        }

        if ($period === 'custom') {
            $from = Carbon::parse($request->get('from', now()->startOfMonth()));
            $to   = Carbon::parse($request->get('to', now()->endOfMonth()))->endOfDay();
            return [$from, $to];
        }

        return [now()->startOfMonth(), now()->endOfMonth()];
    }

    private function csvResponse(string $content, string $filename): Response
    {
        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
