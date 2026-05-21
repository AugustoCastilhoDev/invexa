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

{{-- Filtro de período --}}
<form method="GET" action="{{ route('dashboard') }}" class="mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <button type="submit" name="interval" value="today" class="btn btn-sm {{ ($interval ?? '') === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Hoje</button>
        <button type="submit" name="interval" value="7d"    class="btn btn-sm {{ ($interval ?? '') === '7d'    ? 'btn-primary' : 'btn-outline-secondary' }}">7 dias</button>
        <button type="submit" name="interval" value="month" class="btn btn-sm {{ ($interval ?? '') === 'month' ? 'btn-primary' : 'btn-outline-secondary' }}">Este mês</button>
        <div class="d-flex align-items-center gap-2 ms-2">
            <input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm" style="background:#0f172a;border-color:#334155;color:#e2e8f0;max-width:145px;">
            <span class="text-soft">até</span>
            <input type="date" name="to"   value="{{ $to ?? '' }}"   class="form-control form-control-sm" style="background:#0f172a;border-color:#334155;color:#e2e8f0;max-width:145px;">
            <button type="submit" class="btn btn-sm btn-outline-info">Filtrar</button>
        </div>
        @if($from || $to || $interval)
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-danger">Limpar</a>
        @endif
    </div>
</form>

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

{{-- ═══ TOP 5 PRODUTOS + GRÁFICO DE PIZZA ═══ --}}
@if($topSellingProducts->count() > 0)
<div class="mb-2 mt-2">
    <h5 class="text-white mb-1"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 Produtos Mais Vendidos</h5>
    <p class="text-soft mb-3" style="font-size:.82rem;">Ranking por quantidade vendida no período selecionado.</p>
</div>

<div class="row g-3 mb-4">
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
                                <span style="font-size:.875rem;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:55%;">
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
@endif

{{-- ═══ GRÁFICO DE LINHA — VENDAS POR DIA ═══ --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-dark-bg dashboard-card">
            <div class="card-header card-header-dark d-flex align-items-center justify-content-between py-3 px-4">
                <span class="fw-semibold text-white" style="font-size:.9rem;">
                    <i class="bi bi-graph-up-arrow me-2 text-success"></i>Evolução de Vendas
                </span>
                <div class="d-flex gap-3" style="font-size:.75rem;">
                    <span style="color:#4ade80;"><span style="display:inline-block;width:10px;height:3px;background:#4ade80;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Vendas</span>
                    <span style="color:#f87171;"><span style="display:inline-block;width:10px;height:3px;background:#f87171;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Devoluções</span>
                    <span style="color:#60a5fa;"><span style="display:inline-block;width:10px;height:3px;background:#60a5fa;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Líquido</span>
                </div>
            </div>
            <div class="card-body px-3 py-3">
                <canvas id="salesChart" height="90"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ═══ FLUXO DE CAIXA (só Gerente) ═══ --}}
@if(Auth::user()->isGerente())
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-dark-bg dashboard-card">
            <div class="card-header card-header-dark d-flex align-items-center justify-content-between py-3 px-4">
                <span class="fw-semibold text-white" style="font-size:.9rem;">
                    <i class="bi bi-cash-stack me-2 text-info"></i>Fluxo de Caixa
                    <span style="font-size:.72rem;color:rgba(148,163,184,.6);margin-left:.5rem;">{{ $cfPeriodLabel }}</span>
                </span>
                <div class="d-flex gap-3" style="font-size:.73rem;">
                    <span style="color:#4ade80;"><span style="display:inline-block;width:10px;height:3px;background:#4ade80;border-radius:2px;vertical-align:middle;margin-right:3px;"></span>A receber</span>
                    <span style="color:#34d399;"><span style="display:inline-block;width:10px;height:3px;background:#34d399;border-radius:2px;vertical-align:middle;margin-right:3px;"></span>Recebido</span>
                    <span style="color:#f87171;"><span style="display:inline-block;width:10px;height:3px;background:#f87171;border-radius:2px;vertical-align:middle;margin-right:3px;"></span>A pagar</span>
                    <span style="color:#60a5fa;"><span style="display:inline-block;width:10px;height:3px;background:#60a5fa;border-radius:2px;vertical-align:middle;margin-right:3px;"></span>Saldo</span>
                </div>
            </div>
            <div class="card-body px-3 py-3">
                <canvas id="cashflowChart" height="90"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ═══ TABELAS: Últimas vendas + Estoque crítico ═══ --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-7">
        <div class="card card-dark-bg dashboard-card h-100">
            <div class="card-header card-header-dark d-flex align-items-center justify-content-between py-3 px-4">
                <span class="fw-semibold text-white" style="font-size:.9rem;"><i class="bi bi-receipt me-2 text-success"></i>Últimas Vendas</span>
                <a href="{{ route('sales.index') }}" style="font-size:.75rem;color:#38BDF8;text-decoration:none;">Ver todas <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead><tr>
                            <th class="ps-4">#</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Total</th>
                        </tr></thead>
                        <tbody>
                        @forelse($latestSales as $sale)
                        <tr>
                            <td class="ps-4">{{ $sale->id }}</td>
                            <td>{{ $sale->customer_name ?: '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($sale->status) {
                                        'concluida' => 'badge-concluida',
                                        'pendente'  => 'badge-pendente',
                                        'cancelada' => 'badge-cancelada',
                                        default     => 'badge-default',
                                    };
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">{{ ucfirst($sale->status) }}</span>
                            </td>
                            <td class="pe-4 text-end fw-semibold" style="color:#4ade80;">R$ {{ number_format($sale->total,2,',','.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-soft py-4">Nenhuma venda registrada.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card card-dark-bg dashboard-card h-100">
            <div class="card-header card-header-dark d-flex align-items-center justify-content-between py-3 px-4">
                <span class="fw-semibold text-white" style="font-size:.9rem;"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Estoque Crítico</span>
                <a href="{{ route('products.index') }}" style="font-size:.75rem;color:#38BDF8;text-decoration:none;">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead><tr>
                            <th class="ps-4">Produto</th>
                            <th>Categoria</th>
                            <th class="pe-4 text-end">Qtd</th>
                        </tr></thead>
                        <tbody>
                        @forelse($lowStockProducts as $product)
                        <tr>
                            <td class="ps-4" style="font-size:.83rem;">{{ $product->name }}</td>
                            <td style="font-size:.8rem;color:rgba(148,163,184,.7);">{{ optional($product->category)->name ?? '—' }}</td>
                            <td class="pe-4 text-end">
                                <span class="fw-bold" style="color:#f87171;">{{ $product->quantity }}</span>
                                <span style="font-size:.7rem;color:rgba(148,163,184,.5);"> / {{ $product->min_quantity }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-soft py-4">Nenhum produto em estoque crítico.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Últimas Devoluções ═══ --}}
@if($latestReturns->count() > 0)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-dark-bg dashboard-card">
            <div class="card-header card-header-dark d-flex align-items-center justify-content-between py-3 px-4">
                <span class="fw-semibold text-white" style="font-size:.9rem;"><i class="bi bi-arrow-return-left me-2 text-danger"></i>Últimas Devoluções</span>
                <a href="{{ route('sale-returns.index') }}" style="font-size:.75rem;color:#38BDF8;text-decoration:none;">Ver todas <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead><tr>
                            <th class="ps-4">#</th>
                            <th>Venda</th>
                            <th>Motivo</th>
                            <th>Data</th>
                            <th class="pe-4 text-end">Total</th>
                        </tr></thead>
                        <tbody>
                        @foreach($latestReturns as $ret)
                        <tr>
                            <td class="ps-4">{{ $ret->id }}</td>
                            <td>#{{ optional($ret->sale)->id ?? '—' }}</td>
                            <td style="font-size:.82rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ret->reason ?: '—' }}</td>
                            <td style="font-size:.82rem;">{{ $ret->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>
                            <td class="pe-4 text-end fw-semibold" style="color:#f87171;">R$ {{ number_format($ret->total,2,',','.') }}</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
// ── Gráfico de Linha — Evolução de Vendas ───────────────────────
(function () {
    const labels      = @json($chartLabels);
    const sales       = @json($chartData);
    const returns     = @json($chartReturnsData);
    const net         = @json($chartNetData);

    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Vendas',
                    data: sales,
                    borderColor: '#4ade80',
                    backgroundColor: 'rgba(74,222,128,.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: .35,
                },
                {
                    label: 'Devoluções',
                    data: returns,
                    borderColor: '#f87171',
                    backgroundColor: 'rgba(248,113,113,.06)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: .35,
                },
                {
                    label: 'Líquido',
                    data: net,
                    borderColor: '#60a5fa',
                    backgroundColor: 'rgba(96,165,250,.06)',
                    borderWidth: 2,
                    borderDash: [5,4],
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    fill: false,
                    tension: .35,
                },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(8,13,26,.95)',
                    borderColor: 'rgba(14,165,233,.2)',
                    borderWidth: 1,
                    titleColor: '#e2e8f0',
                    bodyColor: 'rgba(148,163,184,.9)',
                    padding: 10,
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: R$ ${ctx.parsed.y.toLocaleString('pt-BR',{minimumFractionDigits:2})}`
                    }
                }
            },
            scales: {
                x: { grid: { color: 'rgba(148,163,184,.06)' }, ticks: { color: 'rgba(148,163,184,.6)', font: { size: 11 } } },
                y: {
                    grid: { color: 'rgba(148,163,184,.06)' },
                    ticks: {
                        color: 'rgba(148,163,184,.6)',
                        font: { size: 11 },
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR',{minimumFractionDigits:2})
                    }
                }
            }
        }
    });
})();

// ── Gráfico de Fluxo de Caixa ───────────────────────────────────
@if(Auth::user()->isGerente())
(function () {
    const labels    = @json($cfLabels);
    const recPend   = @json($cfDataRecPend);
    const recReceb  = @json($cfDataRecReceb);
    const payPend   = @json($cfDataPayPend);
    const payPaga   = @json($cfDataPayPaga);
    const balance   = @json($cfDataBalance);

    const ctx = document.getElementById('cashflowChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'A receber (pend.)', data: recPend,  backgroundColor: 'rgba(74,222,128,.5)',  borderColor: '#4ade80', borderWidth:1 },
                { label: 'Recebido',          data: recReceb, backgroundColor: 'rgba(52,211,153,.4)',  borderColor: '#34d399', borderWidth:1 },
                { label: 'A pagar',           data: payPend,  backgroundColor: 'rgba(248,113,113,.45)', borderColor: '#f87171', borderWidth:1 },
                { label: 'Pago',              data: payPaga,  backgroundColor: 'rgba(248,113,113,.25)', borderColor: '#fca5a5', borderWidth:1 },
                {
                    type: 'line',
                    label: 'Saldo acum.',
                    data: balance,
                    borderColor: '#60a5fa',
                    backgroundColor: 'rgba(96,165,250,.06)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: .3,
                    fill: false,
                    yAxisID: 'y',
                },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(8,13,26,.95)',
                    borderColor: 'rgba(14,165,233,.2)',
                    borderWidth: 1,
                    titleColor: '#e2e8f0',
                    bodyColor: 'rgba(148,163,184,.9)',
                    padding: 10,
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: R$ ${ctx.parsed.y.toLocaleString('pt-BR',{minimumFractionDigits:2})}`
                    }
                }
            },
            scales: {
                x: { stacked: true, grid: { color: 'rgba(148,163,184,.06)' }, ticks: { color: 'rgba(148,163,184,.6)', font: { size: 11 } } },
                y: {
                    stacked: false,
                    grid: { color: 'rgba(148,163,184,.06)' },
                    ticks: {
                        color: 'rgba(148,163,184,.6)',
                        font: { size: 11 },
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR',{minimumFractionDigits:2})
                    }
                }
            }
        }
    });
})();
@endif

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

    const centerVal = document.getElementById('topChartCenterVal');
    ctx.addEventListener('mousemove', function (e) {
        const pts = chart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, false);
        if (pts.length && centerVal) centerVal.textContent = sold[pts[0].index];
    });
    ctx.addEventListener('mouseleave', function () {
        if (centerVal) centerVal.textContent = total;
    });

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
