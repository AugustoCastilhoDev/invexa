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
.dashboard-card { transition: transform .25s ease, box-shadow .25s ease; }
.dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 1rem 1.5rem rgba(15,23,42,.35); }
.card-dark-bg { background: rgba(15,23,42,.88); border: 1px solid rgba(148,163,184,.14); }
.table-dark-custom { background: rgba(15,23,42,.88); }
.card-header-dark { background: rgba(15,23,42,.92); border-color: rgba(148,163,184,.12); }
.text-soft { color: rgba(226,232,240,.72) !important; }
.kpi-card { position:relative; overflow:hidden; border:0; border-radius:.75rem; }
.kpi-card::after { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,.08) 0%,transparent 60%); pointer-events:none; }
.kpi-value { font-size:2rem; font-weight:700; line-height:1.1; letter-spacing:-.02em; }
.kpi-label { font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; opacity:.8; }
.kpi-description { font-size:.78rem; opacity:.75; line-height:1.4; }
.kpi-trend { display:inline-flex; align-items:center; gap:.25rem; font-size:.75rem; font-weight:600; padding:.15rem .55rem; border-radius:999px; margin-top:.35rem; }
.kpi-trend.up      { background:rgba(255,255,255,.18); color:#fff; }
.kpi-trend.down    { background:rgba(0,0,0,.18); color:rgba(255,255,255,.8); }
.kpi-trend.neutral { background:rgba(255,255,255,.12); color:rgba(255,255,255,.7); }
.metric-row { display:flex; align-items:center; justify-content:space-between; padding:.65rem .85rem; border-radius:.5rem; background:rgba(255,255,255,.04); border:1px solid rgba(148,163,184,.08); transition:background .2s ease; gap:.75rem; }
.metric-row:hover { background:rgba(255,255,255,.07); }
.metric-content { display:flex; align-items:center; min-height:2.4rem; flex:1; }
.metric-icon { width:2.4rem; height:2.4rem; border-radius:.5rem; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.metric-label { font-size:.72rem; color:rgba(226,232,240,.6); margin-bottom:.1rem; }
.metric-value { font-size:.98rem; font-weight:600; color:#f1f5f9; line-height:1.2; }
.badge-status { display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; font-weight:600; padding:.32rem .72rem; border-radius:999px; letter-spacing:.02em; text-transform:capitalize; line-height:1; }
.badge-status::before { content:''; width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.badge-concluida { background:rgba(25,135,84,.20); color:#4ade80; border:1px solid rgba(25,135,84,.25); }
.badge-concluida::before { background:#4ade80; }
.badge-pendente  { background:rgba(255,193,7,.16); color:#facc15; border:1px solid rgba(255,193,7,.24); }
.badge-pendente::before  { background:#facc15; }
.badge-cancelada { background:rgba(220,53,69,.14); color:#f87171; border:1px solid rgba(220,53,69,.22); }
.badge-cancelada::before { background:#f87171; }
.badge-default   { background:rgba(148,163,184,.10); color:#94a3b8; border:1px solid rgba(148,163,184,.18); }
.badge-default::before   { background:#94a3b8; }
.table-dark-custom thead th { font-size:.70rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(148,163,184,.88) !important; border-bottom:1px solid rgba(148,163,184,.28) !important; padding-top:.9rem; padding-bottom:.9rem; white-space:nowrap; }
.table-dark-custom tbody td { font-size:.875rem; color:#cbd5e1; border-color:rgba(148,163,184,.07); vertical-align:middle; padding-top:.7rem; padding-bottom:.7rem; }
.table-dark-custom tbody tr:last-child td { border-bottom:0; }
/* Top produtos */
.top-rank { width:1.6rem;height:1.6rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0; }
.top-rank-1 { background:rgba(251,191,36,.2);color:#fbbf24;border:1px solid rgba(251,191,36,.35); }
.top-rank-2 { background:rgba(148,163,184,.15);color:#94a3b8;border:1px solid rgba(148,163,184,.25); }
.top-rank-3 { background:rgba(180,120,60,.18);color:#c97a3a;border:1px solid rgba(180,120,60,.3); }
.top-rank-n { background:rgba(100,116,139,.1);color:#64748b;border:1px solid rgba(100,116,139,.2); }
.top-bar-track { height:5px;border-radius:999px;background:rgba(255,255,255,.06);overflow:hidden; }
.top-bar-fill  { height:100%;border-radius:999px;transition:width .8s cubic-bezier(.4,0,.2,1); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Dashboard</h1>
        <p class="text-soft mb-0">Visão geral profissional de estoque, vendas e desempenho comercial.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if(Auth::user()->isGerente())
            <a href="{{ route('products.index') }}" class="btn btn-outline-light">Gerenciar Produtos</a>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-light">Gerenciar Categorias</a>
        @endif
        <a href="{{ route('sales.index') }}" class="btn btn-outline-light">Gerenciar Vendas</a>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Produtos</div>
                <div>
                    <div class="kpi-value">{{ $totalProducts ?? 0 }}</div>
                    <div style="font-size:.8rem;opacity:.8;">Ativos no estoque</div>
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                </div>
                <p class="kpi-description mb-0">Quantidade total de itens cadastrados no estoque.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Categorias</div>
                <div>
                    <div class="kpi-value">{{ $totalCategories ?? 0 }}</div>
                    <div style="font-size:.8rem;opacity:.8;">Cadastradas</div>
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                </div>
                <p class="kpi-description mb-0">Divisões de produtos para um controle organizado.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Vendas</div>
                <div>
                    <div class="kpi-value">{{ $totalSales ?? 0 }}</div>
                    <div style="font-size:.8rem;opacity:.8;">No período</div>
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                </div>
                <p class="kpi-description mb-0">Número total de vendas filtradas por período.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Faturamento Líquido</div>
                <div>
                    <div class="kpi-value" style="font-size:1.55rem;">R$ {{ number_format($periodNetRevenue ?? 0,2,',','.') }}</div>
                    <div style="font-size:.75rem;opacity:.85;margin-top:.25rem;">
                        Bruto: R$ {{ number_format($periodRevenue ?? 0,2,',','.') }}
                        @if(($periodReturnsTotal ?? 0) > 0)
                            &nbsp;&mdash;&nbsp;<span style="color:#fca5a5;">Dev: &minus; R$ {{ number_format($periodReturnsTotal,2,',','.') }}</span>
                        @endif
                    </div>
                    @if(!is_null($revenueChangePercent ?? null))
                        <span class="kpi-trend {{ $revenueChangePercent >= 0 ? 'up' : 'down' }}">
                            <i class="bi bi-arrow-{{ $revenueChangePercent >= 0 ? 'up' : 'down' }}-short"></i>
                            {{ $revenueChangePercent >= 0 ? '+' : '' }}{{ number_format($revenueChangePercent,1,',','.') }}% vs anterior
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

{{-- PAINEL FINANCEIRO — somente Gerente e Admin --}}
@if(Auth::user()->isGerente())

<div class="mb-2 mt-2">
    <h5 class="text-white mb-1"><i class="bi bi-bank me-2 text-info"></i>Painel Financeiro</h5>
    <p class="text-soft mb-3" style="font-size:.82rem;">Contas a receber, a pagar, saldo previsto e vencimentos próximos.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:rgba(34,197,94,.25);">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">A RECEBER</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:#4ade80;">R$ {{ number_format($finReceivablePending,2,',','.') }}</p>
                @if($finReceivableOverdue > 0)
                    <small style="color:#f87171;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Vencido: R$ {{ number_format($finReceivableOverdue,2,',','.') }}</small>
                @else
                    <small class="text-soft">Sem vencimentos em atraso</small>
                @endif
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(34,197,94,.15);">
                <a href="{{ route('receivables.index') }}" class="text-decoration-none" style="font-size:.78rem;color:#4ade80;">Ver contas <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:rgba(239,68,68,.25);">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">A PAGAR</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:#f87171;">R$ {{ number_format($finPayablePending,2,',','.') }}</p>
                @if($finPayableOverdue > 0)
                    <small style="color:#fca5a5;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Vencido: R$ {{ number_format($finPayableOverdue,2,',','.') }}</small>
                @else
                    <small class="text-soft">Sem vencimentos em atraso</small>
                @endif
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(239,68,68,.15);">
                <a href="{{ route('bills.index') }}" class="text-decoration-none" style="font-size:.78rem;color:#f87171;">Ver contas <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        @php $balancePositive = $finCashBalance >= 0; @endphp
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:{{ $balancePositive ? 'rgba(251,191,36,.25)' : 'rgba(239,68,68,.25)' }};">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">SALDO PREVISTO</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:{{ $balancePositive ? '#fbbf24' : '#f87171' }};">
                    {{ $balancePositive ? '' : '-' }}R$ {{ number_format(abs($finCashBalance),2,',','.') }}
                </p>
                <small class="text-soft">Receber menos Pagar (pendentes)</small>
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(148,163,184,.1);">
                <span style="font-size:.78rem;color:{{ $balancePositive ? '#fbbf24' : '#f87171' }};">
                    <i class="bi bi-{{ $balancePositive ? 'arrow-up-circle' : 'arrow-down-circle' }} me-1"></i>
                    {{ $balancePositive ? 'Saldo positivo' : 'Saldo negativo' }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        @php
            $duePayToday = $upcomingPayables->filter(fn($p) => $p->due_date->isToday())->count();
            $duePayTotal = $upcomingPayables->count();
            $dueRecTotal = $upcomingReceivables->count();
        @endphp
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:rgba(99,102,241,.25);">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">VENCIMENTOS</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:#818cf8;">
                    {{ $duePayTotal }} conta(s) a pagar
                </p>
                @if($duePayToday > 0)
                    <small style="color:#fca5a5;"><i class="bi bi-alarm-fill me-1"></i>{{ $duePayToday }} vence(m) hoje</small>
                @elseif($duePayTotal === 0)
                    <small class="text-soft">Nenhum vencimento nos próximos 7 dias</small>
                @else
                    <small class="text-soft">Próximos 7 dias</small>
                @endif
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(99,102,241,.15);">
                <a href="{{ route('bills.index') }}" class="text-decoration-none" style="font-size:.78rem;color:#818cf8;">Ver agenda <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

@endif {{-- fim isGerente --}}

{{-- ═══════════════════════════════════════════════════════════════
     SEÇÃO: TOP 5 PRODUTOS + GRÁFICO DE PIZZA
═══════════════════════════════════════════════════════════════ --}}
@if($topSellingProducts->count() > 0)
<div class="mb-2 mt-2">
    <h5 class="text-white mb-1"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 Produtos Mais Vendidos</h5>
    <p class="text-soft mb-3" style="font-size:.82rem;">Ranking por quantidade vendida no período selecionado.</p>
</div>

<div class="row g-3 mb-4">
    {{-- Gráfico de Pizza --}}
    <div class="col-12 col-lg-5">
        <div class="card card-dark-bg h-100" style="border-color:rgba(251,191,36,.15);">
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                <div style="position:relative;width:100%;max-width:260px;">
                    <canvas id="topProductsChart" height="260"></canvas>
                    <div id="topChartCenter" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                        <div style="font-size:1.4rem;font-weight:700;color:#f1f5f9;" id="topChartCenterVal">{{ $topChartData->sum() }}</div>
                        <div style="font-size:.65rem;color:rgba(148,163,184,.7);text-transform:uppercase;letter-spacing:.06em;">un. vendidas</div>
                    </div>
                </div>
                <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center" id="topChartLegend"></div>
            </div>
        </div>
    </div>

    {{-- Ranking em lista --}}
    <div class="col-12 col-lg-7">
        <div class="card card-dark-bg h-100" style="border-color:rgba(251,191,36,.15);">
            <div class="card-header card-header-dark d-flex align-items-center justify-content-between py-3 px-4">
                <span class="fw-semibold text-white" style="font-size:.85rem;">
                    <i class="bi bi-bar-chart-steps me-2 text-warning"></i>Ranking de Vendas
                </span>
                <a href="{{ route('reports.top-products') }}" style="font-size:.75rem;color:#38BDF8;text-decoration:none;">
                    Ver relatório completo <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body px-4 py-3">
                @php
                    $topColors = ['#fbbf24','#94a3b8','#c97a3a','#6366f1','#22d3ee'];
                    $maxSold   = $topSellingProducts->max('total_sold') ?: 1;
                @endphp
                <div class="d-flex flex-column gap-3">
                @foreach($topSellingProducts as $i => $prod)
                @php
                    $pct      = round(($prod->total_sold / $maxSold) * 100);
                    $color    = $topColors[$i] ?? '#64748b';
                    $rankClass= match($i) { 0=>'top-rank-1', 1=>'top-rank-2', 2=>'top-rank-3', default=>'top-rank-n' };
                    $totalPct = $topChartData->sum() > 0 ? round(($prod->total_sold / $topChartData->sum()) * 100) : 0;
                @endphp
                <div>
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <div class="top-rank {{ $rankClass }}">{{ $i + 1 }}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span style="font-size:.875rem;font-weight:600;color:#e2e8f0;
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:55%;">
                                    {{ $prod->name }}
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:.78rem;color:{{ $color }};font-weight:700;">{{ (int)$prod->total_sold }} un.</span>
                                    <span style="font-size:.7rem;color:rgba(148,163,184,.5);">{{ $totalPct }}%</span>
                                    @if(isset($prod->total_revenue) && $prod->total_revenue > 0)
                                        <span style="font-size:.72rem;color:#4ade80;">R$ {{ number_format($prod->total_revenue,2,',','.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="top-bar-track ms-5 ps-2">
                        <div class="top-bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                    </div>
                </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif {{-- fim top produtos --}}

{{-- restante do dashboard (gráfico de linha, tabelas, etc.) --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
// ── Gráfico de Pizza — Top Produtos ─────────────────────────────
@if($topSellingProducts->count() > 0)
(function () {
    const labels  = @json($topChartLabels);
    const sold    = @json($topChartData);
    const revenue = @json($topChartRevenue);
    const colors  = ['#fbbf24','#94a3b8','#c97a3a','#6366f1','#22d3ee'];
    const total   = sold.reduce((a,b) => a+b, 0);

    const ctx = document.getElementById('topProductsChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: sold,
                backgroundColor: colors.map(c => c + 'cc'),
                borderColor:     colors,
                borderWidth: 2,
                hoverOffset: 10,
            }]
        },
        options: {
            cutout: '68%',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                            const rev = revenue[ctx.dataIndex];
                            return [
                                ` ${ctx.parsed} unidades (${pct}%)`,
                                ` R$ ${rev.toLocaleString('pt-BR',{minimumFractionDigits:2})}`
                            ];
                        }
                    },
                    backgroundColor: 'rgba(8,13,26,.95)',
                    borderColor: 'rgba(14,165,233,.2)',
                    borderWidth: 1,
                    titleColor: '#e2e8f0',
                    bodyColor: 'rgba(148,163,184,.9)',
                    padding: 10,
                }
            }
        }
    });

    // Centro dinâmico ao hover
    const centerVal = document.getElementById('topChartCenterVal');
    ctx.addEventListener('mousemove', function (e) {
        const pts = chart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, false);
        if (pts.length && centerVal) {
            centerVal.textContent = sold[pts[0].index];
        }
    });
    ctx.addEventListener('mouseleave', function () {
        if (centerVal) centerVal.textContent = total;
    });

    // Legenda customizada
    const legendEl = document.getElementById('topChartLegend');
    if (legendEl) {
        labels.forEach((label, i) => {
            const pct = total > 0 ? Math.round(sold[i] / total * 100) : 0;
            legendEl.innerHTML += `
                <div style="display:flex;align-items:center;gap:.35rem;font-size:.72rem;color:rgba(226,232,240,.7);">
                    <span style="width:10px;height:10px;border-radius:50%;background:${colors[i]};flex-shrink:0;"></span>
                    ${label} <span style="color:${colors[i]};font-weight:600;">${pct}%</span>
                </div>`;
        });
    }
})();
@endif
</script>
@endpush

@endsection
