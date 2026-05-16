@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
body {
    background: radial-gradient(circle at top left, rgba(96, 165, 250, 0.10), transparent 20%),
                radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.12), transparent 18%),
                #08101d;
    color: #e2e8f0;
}
.dashboard-card {
    transition: transform .25s ease, box-shadow .25s ease;
}
.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 1rem 1.5rem rgba(15, 23, 42, .35);
}
.card-dark-bg {
    background: rgba(15, 23, 42, .88);
    border: 1px solid rgba(148, 163, 184, .14);
}
.table-dark-custom { background: rgba(15, 23, 42, .88); }
.card-header-dark {
    background: rgba(15, 23, 42, .92);
    border-color: rgba(148, 163, 184, .12);
}
.text-soft { color: rgba(226, 232, 240, .72) !important; }
.kpi-card {
    position: relative;
    overflow: hidden;
    border: 0;
    border-radius: .75rem;
}
.kpi-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.08) 0%, transparent 60%);
    pointer-events: none;
}
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1.1; letter-spacing: -.02em; }
.kpi-label { font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; opacity: .8; }
.kpi-description { font-size: .78rem; opacity: .75; line-height: 1.4; }
.kpi-trend {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .75rem; font-weight: 600; padding: .15rem .55rem;
    border-radius: 999px; margin-top: .35rem;
}
.kpi-trend.up      { background: rgba(255,255,255,.18); color: #fff; }
.kpi-trend.down    { background: rgba(0,0,0,.18); color: rgba(255,255,255,.8); }
.kpi-trend.neutral { background: rgba(255,255,255,.12); color: rgba(255,255,255,.7); }
.metric-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .65rem .85rem; border-radius: .5rem;
    background: rgba(255,255,255,.04); border: 1px solid rgba(148, 163, 184, .08);
    transition: background .2s ease; gap: .75rem;
}
.metric-row:hover { background: rgba(255,255,255,.07); }
.metric-content { display: flex; align-items: center; min-height: 2.4rem; flex: 1; }
.metric-icon {
    width: 2.4rem; height: 2.4rem; border-radius: .5rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.metric-label { font-size: .72rem; color: rgba(226, 232, 240, .6); margin-bottom: .1rem; }
.metric-value { font-size: .98rem; font-weight: 600; color: #f1f5f9; line-height: 1.2; }
.badge-status {
    display: inline-flex; align-items: center; gap: .35rem; font-size: .72rem;
    font-weight: 600; padding: .32rem .72rem; border-radius: 999px;
    letter-spacing: .02em; text-transform: capitalize; line-height: 1;
}
.badge-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.badge-concluida { background: rgba(25,135,84,.20); color: #4ade80; border: 1px solid rgba(25,135,84,.25); }
.badge-concluida::before { background: #4ade80; }
.badge-pendente  { background: rgba(255,193,7,.16); color: #facc15; border: 1px solid rgba(255,193,7,.24); }
.badge-pendente::before  { background: #facc15; }
.badge-cancelada { background: rgba(220,53,69,.14); color: #f87171; border: 1px solid rgba(220,53,69,.22); }
.badge-cancelada::before { background: #f87171; }
.badge-default   { background: rgba(148,163,184,.10); color: #94a3b8; border: 1px solid rgba(148,163,184,.18); }
.badge-default::before   { background: #94a3b8; }
.table-dark-custom thead th {
    font-size: .70rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: rgba(148,163,184,.88) !important; border-bottom: 1px solid rgba(148,163,184,.28) !important;
    padding-top: .9rem; padding-bottom: .9rem; white-space: nowrap;
}
.table-dark-custom tbody td {
    font-size: .875rem; color: #cbd5e1; border-color: rgba(148,163,184,.07);
    vertical-align: middle; padding-top: .7rem; padding-bottom: .7rem;
}
.table-dark-custom tbody tr:last-child td { border-bottom: 0; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Dashboard</h1>
        <p class="text-soft mb-0">Visão geral profissional de estoque, vendas e desempenho comercial.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-outline-light">Gerenciar Produtos</a>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-light">Gerenciar Categorias</a>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-light">Gerenciar Vendas</a>
    </div>
</div>

{{-- ── KPI Cards ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100"
             style="background: linear-gradient(135deg, #1d4ed8, #2563eb);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Produtos</div>
                <div>
                    <div class="kpi-value">{{ $totalProducts ?? 0 }}</div>
                    <div style="font-size:.8rem; opacity:.8;">Ativos no estoque</div>
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                </div>
                <p class="kpi-description mb-0">Quantidade total de itens cadastrados no estoque.</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100"
             style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Categorias</div>
                <div>
                    <div class="kpi-value">{{ $totalCategories ?? 0 }}</div>
                    <div style="font-size:.8rem; opacity:.8;">Cadastradas</div>
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                </div>
                <p class="kpi-description mb-0">Divisões de produtos para um controle organizado.</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100"
             style="background: linear-gradient(135deg, #16a34a, #22c55e);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Vendas</div>
                <div>
                    <div class="kpi-value">{{ $totalSales ?? 0 }}</div>
                    <div style="font-size:.8rem; opacity:.8;">No período</div>
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                </div>
                <p class="kpi-description mb-0">Número total de vendas filtradas por período.</p>
            </div>
        </div>
    </div>

    {{-- Card Faturamento Líquido --}}
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100"
             style="background: linear-gradient(135deg, #d97706, #f59e0b);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Faturamento Líquido</div>
                <div>
                    <div class="kpi-value" style="font-size: 1.55rem;">
                        R$ {{ number_format($periodNetRevenue ?? 0, 2, ',', '.') }}
                    </div>
                    {{-- Bruto e devoluções como subtexto --}}
                    <div style="font-size:.75rem; opacity:.85; margin-top:.25rem;">
                        Bruto: R$ {{ number_format($periodRevenue ?? 0, 2, ',', '.') }}
                        @if(($periodReturnsTotal ?? 0) > 0)
                            &nbsp;&mdash;&nbsp;
                            <span style="color:#fca5a5;">
                                Dev: &minus; R$ {{ number_format($periodReturnsTotal, 2, ',', '.') }}
                            </span>
                        @endif
                    </div>
                    @if (!is_null($revenueChangePercent ?? null))
                        <span class="kpi-trend {{ $revenueChangePercent >= 0 ? 'up' : 'down' }}">
                            <i class="bi bi-arrow-{{ $revenueChangePercent >= 0 ? 'up' : 'down' }}-short"></i>
                            {{ $revenueChangePercent >= 0 ? '+' : '' }}{{ number_format($revenueChangePercent, 1, ',', '.') }}% vs anterior
                        </span>
                    @else
                        <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                    @endif
                </div>
                <p class="kpi-description mb-0">Receita do período descontadas as devoluções.</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Gráfico + Resumo rápido ──────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="mb-1 text-white">Faturamento líquido por dia</h5>
                    <p class="text-soft mb-0">Receita bruta menos devoluções no período.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard', array_merge(request()->all(), ['interval' => 'today'])) }}" class="btn btn-sm {{ $interval === 'today' ? 'btn-primary' : 'btn-outline-light' }}">Hoje</a>
                    <a href="{{ route('dashboard', array_merge(request()->all(), ['interval' => '7d'])) }}"   class="btn btn-sm {{ $interval === '7d'    ? 'btn-primary' : 'btn-outline-light' }}">7 dias</a>
                    <a href="{{ route('dashboard', array_merge(request()->all(), ['interval' => 'month'])) }}" class="btn btn-sm {{ $interval === 'month' ? 'btn-primary' : 'btn-outline-light' }}">Mês</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesTrendChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom">
                <h5 class="mb-0 text-white">Resumo rápido</h5>
            </div>
            <div class="card-body d-flex flex-column gap-2 pt-3">

                <div class="metric-row">
                    <div class="metric-content">
                        <div>
                            <div class="metric-label">Faturamento bruto</div>
                            <div class="metric-value">R$ {{ number_format($periodRevenue ?? 0, 2, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="metric-icon" style="background: rgba(34,197,94,.12); color: #4ade80;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>

                <div class="metric-row">
                    <div class="metric-content">
                        <div>
                            <div class="metric-label">Devoluções ({{ $periodReturnsCount ?? 0 }})</div>
                            <div class="metric-value" style="color:#f87171;">
                                @if(($periodReturnsTotal ?? 0) > 0)
                                    &minus; R$ {{ number_format($periodReturnsTotal, 2, ',', '.') }}
                                @else
                                    R$ 0,00
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon" style="background: rgba(248,113,113,.12); color: #f87171;">
                        <i class="bi bi-arrow-return-left"></i>
                    </div>
                </div>

                <div class="metric-row" style="border-color:rgba(251,191,36,.2);">
                    <div class="metric-content">
                        <div>
                            <div class="metric-label">Faturamento líquido</div>
                            <div class="metric-value" style="color:#fbbf24; font-size:1.1rem;">
                                R$ {{ number_format($periodNetRevenue ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon" style="background: rgba(251,191,36,.15); color: #fbbf24;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>

                <div class="metric-row">
                    <div class="metric-content">
                        <div>
                            <div class="metric-label">Venda líquida hoje</div>
                            <div class="metric-value">R$ {{ number_format($salesTodayNet ?? 0, 2, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="metric-icon" style="background: rgba(251,191,36,.12); color: #fbbf24;">
                        <i class="bi bi-sun"></i>
                    </div>
                </div>

                <div class="metric-row">
                    <div class="metric-content">
                        <div>
                            <div class="metric-label">Ticket médio</div>
                            <div class="metric-value">R$ {{ number_format($averageTicket ?? 0, 2, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="metric-icon" style="background: rgba(99,102,241,.15); color: #818cf8;">
                        <i class="bi bi-calculator"></i>
                    </div>
                </div>

                <div class="metric-row">
                    <div class="metric-content">
                        <div>
                            <div class="metric-label">Variação receita líquida</div>
                            <div class="metric-value">
                                @if (!is_null($revenueChangePercent))
                                    <span style="color: {{ $revenueChangePercent >= 0 ? '#4ade80' : '#f87171' }}">
                                        {{ $revenueChangePercent >= 0 ? '+' : '' }}{{ number_format($revenueChangePercent, 2, ',', '.') }}%
                                    </span>
                                @else
                                    <span class="text-soft">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon" style="background: rgba(96,165,250,.12); color: #60a5fa;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Últimas vendas + Estoque baixo ───────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-6">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-white">Últimas vendas</h5>
                    <small class="text-soft">Últimos 5 registros</small>
                </div>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-light flex-shrink-0">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th><th>Data</th><th>Cliente</th><th>Total</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSales as $sale)
                                <tr>
                                    <td class="ps-3 text-soft">{{ $sale->id }}</td>
                                    <td>{{ optional($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->customer_name ?: '—' }}</td>
                                    <td class="fw-semibold text-white">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'concluida' => ['class'=>'badge-concluida','label'=>'Concluída'],
                                                'pendente'  => ['class'=>'badge-pendente', 'label'=>'Pendente'],
                                                'cancelada' => ['class'=>'badge-cancelada','label'=>'Cancelada'],
                                            ];
                                            $s = $statusMap[$sale->status] ?? ['class'=>'badge-default','label'=>ucfirst($sale->status)];
                                        @endphp
                                        <span class="badge-status {{ $s['class'] }}">{{ $s['label'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-soft py-4">Nenhuma venda encontrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-white">Produtos em estoque baixo</h5>
                    <small class="text-soft">Itens próximos ao estoque mínimo</small>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-light flex-shrink-0">Ver produtos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                        <thead><tr><th class="ps-3">Produto</th><th>Categoria</th><th>Qtd.</th></tr></thead>
                        <tbody>
                            @forelse($lowStockProducts as $product)
                                <tr>
                                    <td class="ps-3 fw-semibold text-white">{{ $product->name }}</td>
                                    <td class="text-soft">{{ optional($product->category)->name ?: 'Sem categoria' }}</td>
                                    <td><span class="badge-status badge-cancelada">{{ $product->quantity }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-soft py-4">Nenhum produto com estoque baixo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Últimas devoluções ──────────────────────────────────────────────── --}}
@if($latestReturns->isNotEmpty())
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card dashboard-card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center"
                 style="border-color:rgba(248,113,113,.2) !important;">
                <div>
                    <h5 class="mb-0" style="color:#f87171;">
                        <i class="bi bi-arrow-return-left me-1"></i>Devoluções recentes
                    </h5>
                    <small class="text-soft">Últimos 5 registros</small>
                </div>
                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-outline-danger flex-shrink-0">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Venda</th>
                                <th>Cliente</th>
                                <th>Motivo</th>
                                <th>Valor Estornado</th>
                                <th>Data</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestReturns as $ret)
                            <tr>
                                <td class="ps-3 text-soft">#{{ $ret->id }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $ret->sale_id) }}"
                                       class="text-white text-decoration-none">
                                        Venda #{{ $ret->sale_id }}
                                    </a>
                                </td>
                                <td class="text-soft">{{ $ret->sale->customer_name ?? 'Não informado' }}</td>
                                <td class="text-soft">{{ $ret->reason_label }}</td>
                                <td class="fw-bold" style="color:#f87171;">
                                    &minus; R$ {{ number_format($ret->total, 2, ',', '.') }}
                                </td>
                                <td class="text-soft" style="font-size:.82rem;">
                                    {{ $ret->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('returns.show', $ret) }}" class="btn btn-outline-danger btn-sm">Ver</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Ações rápidas ──────────────────────────────────────────────────────── --}}
<div class="card dashboard-card card-dark-bg shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
            <div>
                <h5 class="mb-1 text-white">Ações rápidas</h5>
                <p class="text-soft mb-0">Exportações de relatórios e navegação direta.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('dashboard.export.csv', request()->all()) }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
                </a>
                <a href="{{ route('dashboard.export.pdf', request()->all()) }}" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
                </a>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-basket3 me-1"></i>Nova Venda
                </a>
                <a href="{{ route('returns.create') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-arrow-return-left me-1"></i>Nova Devolução
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 text-white">
                    <div class="text-soft small">Média de faturamento</div>
                    <div class="h4 mb-0">R$ {{ number_format($periodAverageTicket ?? 0, 2, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 text-white">
                    <div class="text-soft small">Menor venda</div>
                    <div class="h4 mb-0">R$ {{ number_format($periodMinSale ?? 0, 2, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 text-white">
                    <div class="text-soft small">Vendas período anterior</div>
                    <div class="h4 mb-0">R$ {{ number_format($previousRevenue ?? 0, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesTrendChart');
    if (!ctx) return;

    const labels      = {!! json_encode($chartLabels ?? []) !!};
    const netData     = {!! json_encode($chartData ?? []) !!};
    const returnData  = {!! json_encode($chartReturnsData ?? []) !!};

    const canvasCtx = ctx.getContext('2d');
    const gradient  = canvasCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(96, 165, 250, 0.90)');
    gradient.addColorStop(1, 'rgba(96, 165, 250, 0.25)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Líquido',
                    data: netData,
                    backgroundColor: gradient,
                    borderColor: 'transparent',
                    borderWidth: 0,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                },
                {
                    label: 'Devoluções',
                    data: returnData,
                    backgroundColor: 'rgba(248, 113, 113, 0.70)',
                    borderColor: 'transparent',
                    borderWidth: 0,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600, easing: 'easeOutQuart' },
            scales: {
                x: {
                    stacked: false,
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.10)', drawTicks: false },
                    ticks: {
                        color: '#94a3b8', font: { size: 11 }, padding: 8,
                        callback: function (value) {
                            return value >= 1000
                                ? 'R$ ' + (value/1000).toLocaleString('pt-BR', {minimumFractionDigits:0}) + 'k'
                                : 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits:0});
                        },
                    },
                    border: { display: false },
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 12, padding: 16 }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    borderColor: 'rgba(148, 163, 184, 0.2)',
                    borderWidth: 1,
                    titleColor: '#94a3b8',
                    bodyColor: '#e2e8f0',
                    titleFont: { size: 11 },
                    bodyFont: { size: 13, weight: '600' },
                    padding: { x: 14, y: 10 },
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function (items) { return items[0].label; },
                        label: function (context) {
                            const value = context.raw || 0;
                            return context.dataset.label + ': R$ ' + Number(value).toLocaleString('pt-BR', {
                                minimumFractionDigits: 2, maximumFractionDigits: 2
                            });
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
