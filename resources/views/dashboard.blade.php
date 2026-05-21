@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
/*
 * ══════════════════════════════════════════════════
 * Paleta padrão do app — única fonte de verdade CSS
 * Verde    #4ade80  vendas / positivo
 * Vermelho #f87171  devoluções / negativo / danger
 * Azul     #60a5fa  líquido / links / neutro
 * Amarelo  #fbbf24  aviso / rank #1
 * Cinza    #94a3b8  muted / rank #2
 * Marrom   #c97a3a  rank #3
 * Roxo     #6366f1  rank #4
 * ══════════════════════════════════════════════════
 */
body {
    background: radial-gradient(circle at top left, rgba(96,165,250,.09), transparent 22%),
                radial-gradient(circle at bottom right, rgba(34,197,94,.10), transparent 20%),
                #08101d;
    color: #e2e8f0;
}
/* ── Cards base ── */
.card-dark { background:rgba(13,20,35,.92); border:1px solid rgba(148,163,184,.10); border-radius:.75rem; }
.card-dark-hover { transition:transform .2s ease,box-shadow .2s ease; }
.card-dark-hover:hover { transform:translateY(-3px); box-shadow:0 .75rem 1.25rem rgba(0,0,0,.3); }
/* ── KPI ── */
.kpi-card { border:0; border-radius:.75rem; position:relative; overflow:hidden; }
.kpi-card::after { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,.08),transparent 60%); pointer-events:none; }
.kpi-val  { font-size:1.75rem; font-weight:700; line-height:1.1; letter-spacing:-.025em; }
.kpi-lbl  { font-size:.6rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; opacity:.8; }
.kpi-desc { font-size:.72rem; opacity:.7; }
.kpi-trend { display:inline-flex; align-items:center; gap:.2rem; font-size:.7rem; font-weight:600; padding:.12rem .45rem; border-radius:999px; margin-top:.3rem; }
.kpi-trend.up      { background:rgba(255,255,255,.18); color:#fff; }
.kpi-trend.down    { background:rgba(0,0,0,.18); color:rgba(255,255,255,.8); }
.kpi-trend.neutral { background:rgba(255,255,255,.12); color:rgba(255,255,255,.7); }
/* ── Texto ── */
.text-muted-soft { color:rgba(148,163,184,.65) !important; }
.section-title { font-size:.78rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:rgba(148,163,184,.75); margin-bottom:.6rem; }
/* ── Fin mini cards ── */
.fin-card { border-radius:.6rem; padding:.75rem 1rem; border:1px solid rgba(148,163,184,.1); background:rgba(15,23,42,.85); }
.fin-val  { font-size:1.1rem; font-weight:700; line-height:1.2; }
.fin-lbl  { font-size:.6rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; }
/* ── Chart wrappers ── */
.chart-card { border-radius:.7rem; }
.chart-header { font-size:.78rem; font-weight:600; color:#e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:.65rem 1rem; border-bottom:1px solid rgba(148,163,184,.08); }
/* ── Toggle de legenda dos gráficos ── */
.chart-toggle { display:inline-flex; align-items:center; gap:.3rem; font-size:.68rem; font-weight:600;
    padding:.2rem .55rem; border-radius:999px; cursor:pointer; user-select:none;
    border:1px solid transparent; transition:opacity .2s,border-color .2s; }
.chart-toggle:hover { opacity:.85; }
.chart-toggle.active { opacity:1; }
.chart-toggle.inactive { opacity:.35; border-style:dashed; }
.chart-toggle-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; display:inline-block; }
/* ── Tables ── */
.tbl th { font-size:.62rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
    color:rgba(148,163,184,.75) !important; border-bottom:1px solid rgba(148,163,184,.18) !important;
    padding:.55rem .75rem; white-space:nowrap; background:transparent !important; }
.tbl td { font-size:.8rem; color:#cbd5e1 !important; border-color:rgba(148,163,184,.06) !important;
    vertical-align:middle; padding:.5rem .75rem; background:transparent !important; }
.tbl tr:last-child td { border-bottom:0 !important; }
/* ── Badge status — forçar sobre Bootstrap ── */
.bs { display:inline-flex !important; align-items:center; gap:.3rem; font-size:.68rem !important; font-weight:600 !important;
    padding:.25rem .6rem !important; border-radius:999px !important; text-transform:capitalize !important; }
.bs::before { content:''; width:5px; height:5px; border-radius:50%; flex-shrink:0; }
.bs-ok  { background:rgba(74,222,128,.12) !important; color:#4ade80 !important; border:1px solid rgba(74,222,128,.28) !important; }
.bs-ok::before  { background:#4ade80; }
.bs-pen { background:rgba(251,191,36,.12) !important; color:#fbbf24 !important; border:1px solid rgba(251,191,36,.28) !important; }
.bs-pen::before { background:#fbbf24; }
.bs-can { background:rgba(248,113,113,.12) !important; color:#f87171 !important; border:1px solid rgba(248,113,113,.28) !important; }
.bs-can::before { background:#f87171; }
.bs-def { background:rgba(148,163,184,.09) !important; color:#94a3b8 !important; border:1px solid rgba(148,163,184,.18) !important; }
.bs-def::before { background:#94a3b8; }
/* ── Top rank ── */
.rk { width:1.4rem;height:1.4rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0; }
.rk1{background:rgba(251,191,36,.18);color:#fbbf24;border:1px solid rgba(251,191,36,.3);}
.rk2{background:rgba(148,163,184,.13);color:#94a3b8;border:1px solid rgba(148,163,184,.22);}
.rk3{background:rgba(180,120,60,.16);color:#c97a3a;border:1px solid rgba(180,120,60,.28);}
.rkn{background:rgba(100,116,139,.09);color:#64748b;border:1px solid rgba(100,116,139,.18);}
.bar-track{height:3px;border-radius:999px;background:rgba(255,255,255,.05);overflow:hidden;margin-top:3px;}
.bar-fill {height:100%;border-radius:999px;transition:width .7s cubic-bezier(.4,0,.2,1);}
/* ── Filter bar ── */
.filter-bar {
    background: rgba(13,20,35,.85);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .65rem;
    padding: .55rem .85rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .5rem;
}
.filter-bar .btn-period {
    font-size: .72rem;
    padding: .28rem .75rem;
    border-radius: .4rem;
    font-weight: 600;
    border: 1px solid rgba(148,163,184,.22);
    background: transparent;
    color: rgba(148,163,184,.8);
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
    white-space: nowrap;
}
.filter-bar .btn-period:hover {
    background: rgba(96,165,250,.10);
    border-color: rgba(96,165,250,.35);
    color: #e2e8f0;
}
.filter-bar .btn-period.active {
    background: rgba(96,165,250,.18);
    border-color: rgba(96,165,250,.5);
    color: #60a5fa;
}
.filter-bar .filter-sep {
    width: 1px;
    height: 18px;
    background: rgba(148,163,184,.15);
    flex-shrink: 0;
}
.filter-bar input[type=date] {
    background: rgba(8,13,26,.8);
    border: 1px solid rgba(148,163,184,.22);
    color: #e2e8f0;
    font-size: .72rem;
    border-radius: .4rem;
    padding: .28rem .55rem;
    height: 28px;
    width: 120px;
    outline: none;
}
.filter-bar input[type=date]:focus {
    border-color: rgba(96,165,250,.45);
    box-shadow: 0 0 0 .15rem rgba(96,165,250,.12);
}
.filter-bar .btn-filter {
    font-size: .72rem;
    padding: .28rem .65rem;
    border-radius: .4rem;
    font-weight: 600;
    border: 1px solid rgba(96,165,250,.3);
    background: rgba(96,165,250,.12);
    color: #60a5fa;
    cursor: pointer;
    white-space: nowrap;
}
.filter-bar .btn-filter:hover { background: rgba(96,165,250,.22); }
.filter-bar .btn-clear {
    font-size: .72rem;
    padding: .28rem .65rem;
    border-radius: .4rem;
    font-weight: 600;
    border: 1px solid rgba(248,113,113,.25);
    background: rgba(248,113,113,.08);
    color: #f87171;
    text-decoration: none;
    white-space: nowrap;
    line-height: 1.4;
}
.filter-bar .btn-clear:hover { background: rgba(248,113,113,.16); color: #f87171; }
.filter-date-label {
    font-size: .68rem;
    color: rgba(148,163,184,.55);
    white-space: nowrap;
}
/* ── Link padrão do app — forçar sobre Bootstrap ── */
a.app-link, .app-link { font-size:.68rem !important; color:#60a5fa !important; text-decoration:none !important; }
a.app-link:hover, .app-link:hover { color:#93c5fd !important; }
/* ── Valor negativo / devolução ── */
.val-neg { color:#f87171 !important; }
.val-pos { color:#4ade80 !important; }
</style>
@endpush

@section('content')

{{-- ── Filtro de período ── --}}
<form method="GET" action="{{ route('dashboard') }}" class="filter-bar mb-3">

    {{-- Atalhos de período --}}
    <div class="d-flex align-items-center gap-1 flex-wrap">
        <button type="submit" name="interval" value="today"
                class="btn-period {{ ($interval??'')==='today' ? 'active' : '' }}">
            <i class="bi bi-sun me-1" style="font-size:.65rem;"></i>Hoje
        </button>
        <button type="submit" name="interval" value="7d"
                class="btn-period {{ ($interval??'')==='7d' ? 'active' : '' }}">
            <i class="bi bi-calendar-week me-1" style="font-size:.65rem;"></i>7 dias
        </button>
        <button type="submit" name="interval" value="month"
                class="btn-period {{ ($interval??'')==='month' ? 'active' : '' }}">
            <i class="bi bi-calendar-month me-1" style="font-size:.65rem;"></i>Este mês
        </button>
    </div>

    <div class="filter-sep d-none d-md-block"></div>

    {{-- Filtro de datas personalizado --}}
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="filter-date-label">De</span>
        <input type="date" name="from" value="{{ $from??'' }}">
        <span class="filter-date-label">até</span>
        <input type="date" name="to"   value="{{ $to??'' }}">
        <button type="submit" class="btn-filter">
            <i class="bi bi-funnel me-1"></i>Filtrar
        </button>
    </div>

    {{-- Limpar --}}
    @if($from||$to||$interval)
        <a href="{{ route('dashboard') }}" class="btn-clear">
            <i class="bi bi-x-circle me-1"></i>Limpar
        </a>
    @endif

    {{-- Links de atalho (direita) --}}
    <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        @if(Auth::user()->isGerente())
            <a href="{{ route('products.index') }}"   class="btn-period">Produtos</a>
            <a href="{{ route('categories.index') }}" class="btn-period">Categorias</a>
        @endif
        <a href="{{ route('sales.index') }}" class="btn-period">Vendas</a>
    </div>

</form>

<h1 class="h5 fw-bold text-white mb-1">Dashboard</h1>
<p class="mb-3" style="font-size:.78rem;color:rgba(148,163,184,.65);">Visão geral de estoque, vendas e desempenho comercial.</p>

{{-- ════ ROW 1 — KPI cards ════ --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-xl-3">
        <div class="card kpi-card card-dark-hover h-100 text-white" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
            <div class="card-body py-3 px-3">
                <div class="kpi-lbl mb-2">Produtos</div>
                <div class="kpi-val">{{ $totalProducts??0 }}</div>
                <div class="kpi-desc mt-1">Ativos no estoque</div>
                <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card kpi-card card-dark-hover h-100 text-white" style="background:linear-gradient(135deg,#1e40af,#3b82f6);">
            <div class="card-body py-3 px-3">
                <div class="kpi-lbl mb-2">Categorias</div>
                <div class="kpi-val">{{ $totalCategories??0 }}</div>
                <div class="kpi-desc mt-1">Cadastradas</div>
                <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card kpi-card card-dark-hover h-100 text-white" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
            <div class="card-body py-3 px-3">
                <div class="kpi-lbl mb-2">Vendas</div>
                <div class="kpi-val">{{ $totalSales??0 }}</div>
                <div class="kpi-desc mt-1">No período</div>
                <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card kpi-card card-dark-hover h-100 text-white" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
            <div class="card-body py-3 px-3">
                <div class="kpi-lbl mb-2">Faturamento Líquido</div>
                <div class="kpi-val" style="font-size:1.35rem;">R$ {{ number_format($periodNetRevenue??0,2,',','.') }}</div>
                <div class="kpi-desc mt-1">
                    Bruto: R$ {{ number_format($periodRevenue??0,2,',','.') }}
                    @if(($periodReturnsTotal??0)>0)
                        &nbsp;<span style="color:#fca5a5;">Dev: -R$ {{ number_format($periodReturnsTotal,2,',','.') }}</span>
                    @endif
                </div>
                @if(!is_null($revenueChangePercent??null))
                    <span class="kpi-trend {{ $revenueChangePercent>=0?'up':'down' }}">
                        <i class="bi bi-arrow-{{ $revenueChangePercent>=0?'up':'down' }}-short"></i>
                        {{ $revenueChangePercent>=0?'+':'' }}{{ number_format($revenueChangePercent,1,',','.') }}% vs anterior
                    </span>
                @else
                    <span class="kpi-trend neutral"><i class="bi bi-dash"></i> Sem comparativo</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ════ ROW 2 — Painel Financeiro (só Gerente) ════ --}}
@if(Auth::user()->isGerente())
@php $balPos = $finCashBalance >= 0; @endphp
<div class="row g-2 mb-3">
    <div class="col-12"><p class="section-title mb-2"><i class="bi bi-bank me-1" style="color:#60a5fa;"></i>Painel Financeiro</p></div>
    <div class="col-6 col-xl-3">
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">A Receber</div>
            <div class="fin-val val-pos">R$ {{ number_format($finReceivablePending,2,',','.') }}</div>
            @if($finReceivableOverdue>0)
                <small class="val-neg" style="font-size:.68rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Vencido: R$ {{ number_format($finReceivableOverdue,2,',','.') }}</small>
            @else
                <small class="text-muted-soft" style="font-size:.68rem;">Sem atrasos</small>
            @endif
            <div class="mt-2"><a href="{{ route('receivables.index') }}" class="app-link">Ver contas <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">A Pagar</div>
            <div class="fin-val val-neg">R$ {{ number_format($finPayablePending,2,',','.') }}</div>
            @if($finPayableOverdue>0)
                <small class="val-neg" style="font-size:.68rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Vencido: R$ {{ number_format($finPayableOverdue,2,',','.') }}</small>
            @else
                <small class="text-muted-soft" style="font-size:.68rem;">Sem atrasos</small>
            @endif
            <div class="mt-2"><a href="{{ route('bills.index') }}" class="app-link">Ver contas <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">Saldo Previsto</div>
            <div class="fin-val {{ $balPos?'val-pos':'val-neg' }}">{{ $balPos?'':'&minus;' }}R$ {{ number_format(abs($finCashBalance),2,',','.') }}</div>
            <small class="text-muted-soft" style="font-size:.68rem;">Receber menos Pagar</small>
            <div class="mt-2"><span style="font-size:.68rem;" class="{{ $balPos?'val-pos':'val-neg' }}"><i class="bi bi-{{ $balPos?'arrow-up-circle':'arrow-down-circle' }} me-1"></i>{{ $balPos?'Positivo':'Negativo' }}</span></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        @php
            $duePayToday = $upcomingPayables->filter(fn($p)=>$p->due_date->isToday())->count();
            $duePayTotal = $upcomingPayables->count();
        @endphp
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">Vencimentos</div>
            <div class="fin-val" style="color:#60a5fa;">{{ $duePayTotal }} a pagar</div>
            @if($duePayToday>0)
                <small class="val-neg" style="font-size:.68rem;"><i class="bi bi-alarm-fill me-1"></i>{{ $duePayToday }} vence(m) hoje</small>
            @else
                <small class="text-muted-soft" style="font-size:.68rem;">Próximos 7 dias</small>
            @endif
            <div class="mt-2"><a href="{{ route('bills.index') }}" class="app-link">Ver agenda <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </div>
</div>
@endif

{{-- ════ ROW 3 — Evolução de Vendas (col-8) + Doughnut (col-4) ════ --}}
<div class="row g-2 mb-2">
    <div class="col-12 col-xl-8">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-bar-chart-fill me-2" style="color:#4ade80;"></i>Evolução de Vendas</span>
                <div class="d-flex gap-2 flex-wrap" id="salesLegend">
                    <span class="chart-toggle active" data-chart="salesChart" data-index="0" style="border-color:rgba(74,222,128,.3);">
                        <span class="chart-toggle-dot" style="background:#4ade80;"></span>Vendas
                    </span>
                    <span class="chart-toggle active" data-chart="salesChart" data-index="1" style="border-color:rgba(248,113,113,.3);">
                        <span class="chart-toggle-dot" style="background:#f87171;"></span>Devoluções
                    </span>
                    <span class="chart-toggle active" data-chart="salesChart" data-index="2" style="border-color:rgba(96,165,250,.3);">
                        <span class="chart-toggle-dot" style="background:#60a5fa;"></span>Líquido
                    </span>
                </div>
            </div>
            <div class="p-2">
                <canvas id="salesChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-trophy me-2" style="color:#fbbf24;"></i>Top Produtos</span>
                <a href="{{ route('reports.top-products') }}" class="app-link">Relatório <i class="bi bi-arrow-right"></i></a>
            </div>
            @if($topSellingProducts->count()>0)
            <div class="p-2 d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:100%;max-width:180px;">
                    <canvas id="topProductsChart" height="180"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                        <div style="font-size:1.2rem;font-weight:700;color:#f1f5f9;" id="topChartCenterVal">{{ $topChartData->sum() }}</div>
                        <div style="font-size:.58rem;color:rgba(148,163,184,.65);text-transform:uppercase;letter-spacing:.05em;">un. vendidas</div>
                    </div>
                </div>
                <div class="mt-2 d-flex flex-wrap gap-2 justify-content-center" id="topChartLegend"></div>
            </div>
            @else
            <div class="p-4 text-center text-muted-soft" style="font-size:.78rem;">Nenhum dado no período.</div>
            @endif
        </div>
    </div>
</div>

{{-- ════ ROW 4 — Fluxo de Caixa (col-7) + Ranking (col-5) ════ --}}
@if(Auth::user()->isGerente() || $topSellingProducts->count()>0)
<div class="row g-2 mb-2">
    @if(Auth::user()->isGerente())
    <div class="col-12 col-xl-7">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-bar-chart-line-fill me-2" style="color:#60a5fa;"></i>Fluxo de Caixa <small class="text-muted-soft ms-1" style="font-size:.65rem;">{{ $cfPeriodLabel }}</small></span>
                <div class="d-flex gap-2 flex-wrap" id="cashflowLegend">
                    <span class="chart-toggle active" data-chart="cashflowChart" data-index="0" style="border-color:rgba(74,222,128,.35);">
                        <span class="chart-toggle-dot" style="background:#4ade80;"></span>A receber
                    </span>
                    <span class="chart-toggle active" data-chart="cashflowChart" data-index="1" style="border-color:rgba(74,222,128,.2);">
                        <span class="chart-toggle-dot" style="background:rgba(74,222,128,.5);border:1px solid #4ade80;"></span>Recebido
                    </span>
                    <span class="chart-toggle active" data-chart="cashflowChart" data-index="2" style="border-color:rgba(248,113,113,.35);">
                        <span class="chart-toggle-dot" style="background:#f87171;"></span>A pagar
                    </span>
                    <span class="chart-toggle active" data-chart="cashflowChart" data-index="3" style="border-color:rgba(248,113,113,.2);">
                        <span class="chart-toggle-dot" style="background:rgba(248,113,113,.5);border:1px solid #f87171;"></span>Pago
                    </span>
                    <span class="chart-toggle active" data-chart="cashflowChart" data-index="4" style="border-color:rgba(96,165,250,.3);">
                        <span class="chart-toggle-dot" style="background:#60a5fa;"></span>Saldo
                    </span>
                </div>
            </div>
            <div class="p-2">
                <canvas id="cashflowChart" height="110"></canvas>
            </div>
        </div>
    </div>
    @endif

    @if($topSellingProducts->count()>0)
    <div class="col-12 col-xl-{{ Auth::user()->isGerente()?'5':'12' }}">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-bar-chart-steps me-2" style="color:#fbbf24;"></i>Ranking de Vendas</span>
            </div>
            <div class="px-3 py-2">
                @php
                    $topColors = ['#fbbf24','#94a3b8','#c97a3a','#6366f1','#60a5fa'];
                    $maxSold   = $topSellingProducts->max('total_sold') ?: 1;
                    $sumSold   = $topChartData->sum() ?: 1;
                @endphp
                <div class="d-flex flex-column gap-2">
                @foreach($topSellingProducts as $i => $prod)
                @php
                    $pct      = round(($prod->total_sold/$maxSold)*100);
                    $color    = $topColors[$i]??'#64748b';
                    $rkClass  = match($i){0=>'rk1',1=>'rk2',2=>'rk3',default=>'rkn'};
                    $totalPct = round(($prod->total_sold/$sumSold)*100);
                @endphp
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rk {{ $rkClass }}">{{ $i+1 }}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.77rem;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:52%;">{{ $prod->name }}</span>
                                <div class="d-flex gap-2 align-items-center">
                                    <span style="font-size:.72rem;color:{{ $color }};font-weight:700;">{{ (int)$prod->total_sold }} un.</span>
                                    <span style="font-size:.65rem;color:rgba(148,163,184,.45);">{{ $totalPct }}%</span>
                                    @if(isset($prod->total_revenue)&&$prod->total_revenue>0)
                                        <span class="val-pos" style="font-size:.68rem;">R$ {{ number_format($prod->total_revenue,2,',','.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bar-track ms-4 ps-2"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
                </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

{{-- ════ ROW 5 — Tabelas: Últimas Vendas + Estoque Crítico ════ --}}
<div class="row g-2 mb-2">
    <div class="col-12 col-xl-7">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-receipt me-2" style="color:#4ade80;"></i>Últimas Vendas</span>
                <a href="{{ route('sales.index') }}" class="app-link">Ver todas <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table tbl mb-0">
                    <thead><tr>
                        <th class="ps-3">#</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th class="pe-3 text-end">Total</th>
                    </tr></thead>
                    <tbody>
                    @forelse($latestSales as $sale)
                    <tr>
                        <td class="ps-3">{{ $sale->id }}</td>
                        <td>{{ $sale->customer_name ?: '—' }}</td>
                        <td>@php
                            $bc = match($sale->status){
                                'concluida'=>'bs-ok','pendente'=>'bs-pen','cancelada'=>'bs-can',default=>'bs-def'
                            };
                        @endphp<span class="bs {{ $bc }}">{{ ucfirst($sale->status) }}</span></td>
                        <td class="pe-3 text-end fw-semibold val-pos">R$ {{ number_format($sale->total,2,',','.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted-soft">Nenhuma venda registrada.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-exclamation-triangle me-2" style="color:#fbbf24;"></i>Estoque Crítico</span>
                <a href="{{ route('products.index') }}" class="app-link">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table tbl mb-0">
                    <thead><tr>
                        <th class="ps-3">Produto</th>
                        <th>Categoria</th>
                        <th class="pe-3 text-end">Qtd</th>
                    </tr></thead>
                    <tbody>
                    @forelse($lowStockProducts as $product)
                    <tr>
                        <td class="ps-3" style="font-size:.77rem;">{{ $product->name }}</td>
                        <td style="color:rgba(148,163,184,.6);">{{ optional($product->category)->name ?? '—' }}</td>
                        <td class="pe-3 text-end">
                            <span class="fw-bold val-neg">{{ $product->quantity }}</span>
                            <span style="font-size:.65rem;color:rgba(148,163,184,.4);"> / {{ $product->min_quantity }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-4 text-muted-soft">Nenhum produto crítico.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ════ ROW 6 — Últimas Devoluções ════ --}}
@if($latestReturns->count()>0)
<div class="row g-2 mb-2">
    <div class="col-12">
        <div class="card-dark chart-card">
            <div class="chart-header">
                <span><i class="bi bi-arrow-return-left me-2" style="color:#f87171;"></i>Últimas Devoluções</span>
                <a href="{{ route('returns.index') }}" class="app-link">Ver todas <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table tbl mb-0">
                    <thead><tr>
                        <th class="ps-3">#</th>
                        <th>Venda</th>
                        <th>Motivo</th>
                        <th>Data</th>
                        <th class="pe-3 text-end">Total</th>
                    </tr></thead>
                    <tbody>
                    @foreach($latestReturns as $ret)
                    <tr>
                        <td class="ps-3">{{ $ret->id }}</td>
                        <td>#{{ optional($ret->sale)->id??'—' }}</td>
                        <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ret->reason??'—' }}</td>
                        <td>{{ $ret->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>
                        <td class="pe-3 text-end fw-semibold val-neg">R$ {{ number_format($ret->total,2,',','.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const APP = {
    green  : '#4ade80',
    red    : '#f87171',
    blue   : '#60a5fa',
    yellow : '#fbbf24',
    gray   : '#94a3b8',
    brown  : '#c97a3a',
    purple : '#6366f1',
    greenBg     : 'rgba(74,222,128,.35)',
    greenBgLight: 'rgba(74,222,128,.15)',
    redBg       : 'rgba(248,113,113,.35)',
    redBgLight  : 'rgba(248,113,113,.15)',
    blueBg      : 'rgba(96,165,250,.35)',
    blueBgLight : 'rgba(96,165,250,.15)',
};
const CHART = {
    tooltip: {
        backgroundColor : 'rgba(8,13,26,.96)',
        borderColor     : 'rgba(96,165,250,.2)',
        borderWidth     : 1,
        titleColor      : '#e2e8f0',
        bodyColor       : 'rgba(148,163,184,.85)',
        padding         : 10,
    },
    grid : 'rgba(148,163,184,.05)',
    tick : 'rgba(148,163,184,.55)',
    font : { size:10 },
};
const fmtBRL = v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits:2 });
const _charts = {};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.chart-toggle').forEach(el => {
        el.addEventListener('click', function() {
            const chartId = this.dataset.chart;
            const idx     = parseInt(this.dataset.index);
            const chart   = _charts[chartId];
            if (!chart) return;
            const meta = chart.getDatasetMeta(idx);
            meta.hidden = !meta.hidden;
            chart.update();
            this.classList.toggle('active',   !meta.hidden);
            this.classList.toggle('inactive',  meta.hidden);
        });
    });
});

(function(){
    const ctx = document.getElementById('salesChart'); if(!ctx) return;
    const chart = new Chart(ctx, {
        data: {
            labels: @json($chartLabels),
            datasets: [
                { type:'bar', label:'Vendas',     data:@json($chartData),        backgroundColor:APP.greenBg,  borderColor:APP.green, borderWidth:1.5, borderRadius:3, order:3 },
                { type:'bar', label:'Devoluções', data:@json($chartReturnsData),  backgroundColor:APP.redBg,    borderColor:APP.red,   borderWidth:1.5, borderRadius:3, order:3 },
                { type:'bar', label:'Líquido',    data:@json($chartNetData),      backgroundColor:APP.blueBg,   borderColor:APP.blue,  borderWidth:1.5, borderRadius:3, order:3 },
            ]
        },
        options: {
            responsive:true, maintainAspectRatio:true,
            interaction:{ mode:'index', intersect:false },
            plugins:{ legend:{ display:false }, tooltip:{ ...CHART.tooltip, callbacks:{ label: c => ` ${c.dataset.label}: ${fmtBRL(c.parsed.y)}` }}},
            scales:{
                x:{ grid:{ color:CHART.grid }, ticks:{ color:CHART.tick, font:CHART.font, maxTicksLimit:10 }},
                y:{ grid:{ color:CHART.grid }, ticks:{ color:CHART.tick, font:CHART.font, callback: v => 'R$'+Number(v).toLocaleString('pt-BR',{minimumFractionDigits:0}) }}
            }
        }
    });
    _charts['salesChart'] = chart;
})();

@if(Auth::user()->isGerente())
(function(){
    const ctx = document.getElementById('cashflowChart'); if(!ctx) return;
    const chart = new Chart(ctx, {
        data: {
            labels: @json($cfLabels),
            datasets: [
                { type:'bar', label:'A receber', data:@json($cfDataRecPend),   backgroundColor:APP.greenBg,      borderColor:APP.green, borderWidth:1.5, borderRadius:3, order:3 },
                { type:'bar', label:'Recebido',  data:@json($cfDataRecReceb),  backgroundColor:APP.greenBgLight, borderColor:APP.green, borderWidth:1,   borderRadius:3, order:3 },
                { type:'bar', label:'A pagar',   data:@json($cfDataPayPend),   backgroundColor:APP.redBg,        borderColor:APP.red,   borderWidth:1.5, borderRadius:3, order:3 },
                { type:'bar', label:'Pago',      data:@json($cfDataPayPaga),   backgroundColor:APP.redBgLight,   borderColor:APP.red,   borderWidth:1,   borderRadius:3, order:3 },
                { type:'bar', label:'Saldo',     data:@json($cfDataBalance),   backgroundColor:APP.blueBg,       borderColor:APP.blue,  borderWidth:1.5, borderRadius:3, order:3 },
            ]
        },
        options: {
            responsive:true, maintainAspectRatio:true,
            interaction:{ mode:'index', intersect:false },
            plugins:{ legend:{ display:false }, tooltip:{ ...CHART.tooltip, callbacks:{ label: c => ` ${c.dataset.label}: ${fmtBRL(c.parsed.y)}` }}},
            scales:{
                x:{ grid:{ color:CHART.grid }, ticks:{ color:CHART.tick, font:CHART.font, maxTicksLimit:10 }},
                y:{ grid:{ color:CHART.grid }, ticks:{ color:CHART.tick, font:CHART.font, callback: v => 'R$'+Number(v).toLocaleString('pt-BR',{minimumFractionDigits:0}) }}
            }
        }
    });
    _charts['cashflowChart'] = chart;
})();
@endif

@if($topSellingProducts->count()>0)
(function(){
    const labels  = @json($topChartLabels);
    const sold    = @json($topChartData);
    const revenue = @json($topChartRevenue);
    const colors  = [APP.yellow, APP.gray, APP.brown, APP.purple, APP.blue];
    const total   = sold.reduce((a,b)=>a+b, 0);
    const ctx = document.getElementById('topProductsChart'); if(!ctx) return;
    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data:sold, backgroundColor:colors.map(c=>c+'cc'), borderColor:colors, borderWidth:2, hoverOffset:8 }] },
        options: {
            cutout:'65%', responsive:true,
            plugins:{ legend:{ display:false }, tooltip:{ ...CHART.tooltip, callbacks:{ label: ctx => {
                const pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                return [` ${ctx.parsed} un. (${pct}%)`, ` ${fmtBRL(revenue[ctx.dataIndex])}`];
            }}}}
        }
    });
    const cv = document.getElementById('topChartCenterVal');
    ctx.addEventListener('mousemove', e => {
        const pts = chart.getElementsAtEventForMode(e, 'nearest', {intersect:true}, false);
        if(pts.length && cv) cv.textContent = sold[pts[0].index];
    });
    ctx.addEventListener('mouseleave', () => { if(cv) cv.textContent = total; });
    const leg = document.getElementById('topChartLegend');
    if(leg) labels.forEach((l,i) => {
        const pct = total > 0 ? Math.round(sold[i]/total*100) : 0;
        leg.innerHTML += `<div style="display:flex;align-items:center;gap:.3rem;font-size:.65rem;color:rgba(226,232,240,.65);">
            <span style="width:8px;height:8px;border-radius:50%;background:${colors[i]};flex-shrink:0;"></span>
            ${l} <b style="color:${colors[i]}">${pct}%</b></div>`;
    });
})();
@endif
</script>
@endpush

@endsection
