@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
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
.chart-legend-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:3px; vertical-align:middle; }
/* ── Tables ── */
.tbl th { font-size:.62rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(148,163,184,.75) !important; border-bottom:1px solid rgba(148,163,184,.18) !important; padding:.55rem .75rem; white-space:nowrap; }
.tbl td { font-size:.8rem; color:#cbd5e1; border-color:rgba(148,163,184,.06); vertical-align:middle; padding:.5rem .75rem; }
.tbl tr:last-child td { border-bottom:0; }
/* ── Badge status ── */
.bs { display:inline-flex; align-items:center; gap:.3rem; font-size:.68rem; font-weight:600; padding:.25rem .6rem; border-radius:999px; text-transform:capitalize; }
.bs::before { content:''; width:5px; height:5px; border-radius:50%; flex-shrink:0; }
.bs-ok  { background:rgba(25,135,84,.18); color:#4ade80; border:1px solid rgba(25,135,84,.22); } .bs-ok::before  { background:#4ade80; }
.bs-pen { background:rgba(255,193,7,.14); color:#facc15; border:1px solid rgba(255,193,7,.22); } .bs-pen::before { background:#facc15; }
.bs-can { background:rgba(220,53,69,.13); color:#f87171; border:1px solid rgba(220,53,69,.20); } .bs-can::before { background:#f87171; }
.bs-def { background:rgba(148,163,184,.09); color:#94a3b8; border:1px solid rgba(148,163,184,.16); } .bs-def::before { background:#94a3b8; }
/* ── Top rank ── */
.rk { width:1.4rem;height:1.4rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0; }
.rk1{background:rgba(251,191,36,.18);color:#fbbf24;border:1px solid rgba(251,191,36,.3);}
.rk2{background:rgba(148,163,184,.13);color:#94a3b8;border:1px solid rgba(148,163,184,.22);}
.rk3{background:rgba(180,120,60,.16);color:#c97a3a;border:1px solid rgba(180,120,60,.28);}
.rkn{background:rgba(100,116,139,.09);color:#64748b;border:1px solid rgba(100,116,139,.18);}
.bar-track{height:3px;border-radius:999px;background:rgba(255,255,255,.05);overflow:hidden;margin-top:3px;}
.bar-fill {height:100%;border-radius:999px;transition:width .7s cubic-bezier(.4,0,.2,1);}
/* ── Filter bar ── */
.filter-bar .btn-sm { font-size:.72rem; padding:.3rem .7rem; border-radius:.45rem; }
.filter-bar input[type=date] { background:#0d1424;border-color:#1e293b;color:#e2e8f0;font-size:.72rem;border-radius:.45rem; }
</style>
@endpush

@section('content')

{{-- ── Filtro de período ── --}}
<div class="filter-bar d-flex flex-wrap gap-2 align-items-center mb-3">
    <form method="GET" action="{{ route('dashboard') }}" class="d-contents">
        <button type="submit" name="interval" value="today" class="btn btn-sm {{ ($interval??'')==='today'?'btn-primary':'btn-outline-secondary' }}">Hoje</button>
        <button type="submit" name="interval" value="7d"    class="btn btn-sm {{ ($interval??'')==='7d'   ?'btn-primary':'btn-outline-secondary' }}">7 dias</button>
        <button type="submit" name="interval" value="month" class="btn btn-sm {{ ($interval??'')==='month'?'btn-primary':'btn-outline-secondary' }}">Este mês</button>
        <div class="d-flex align-items-center gap-2 ms-1">
            <input type="date" name="from" value="{{ $from??'' }}" class="form-control form-control-sm" style="max-width:130px;">
            <span class="text-muted-soft" style="font-size:.72rem;">até</span>
            <input type="date" name="to"   value="{{ $to??'' }}"   class="form-control form-control-sm" style="max-width:130px;">
            <button type="submit" class="btn btn-sm btn-outline-info">Filtrar</button>
        </div>
        @if($from||$to||$interval)
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-danger">Limpar</a>
        @endif
    </form>
    <div class="ms-auto d-flex gap-2">
        @if(Auth::user()->isGerente())
            <a href="{{ route('products.index') }}"  class="btn btn-sm btn-outline-light">Produtos</a>
            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-light">Categorias</a>
        @endif
        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-light">Vendas</a>
    </div>
</div>

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
        <div class="card kpi-card card-dark-hover h-100 text-white" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
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
    <div class="col-12"><p class="section-title mb-2"><i class="bi bi-bank me-1 text-info"></i>Painel Financeiro</p></div>
    <div class="col-6 col-xl-3">
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">A Receber</div>
            <div class="fin-val" style="color:#4ade80;">R$ {{ number_format($finReceivablePending,2,',','.') }}</div>
            @if($finReceivableOverdue>0)
                <small style="color:#f87171;font-size:.68rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Vencido: R$ {{ number_format($finReceivableOverdue,2,',','.') }}</small>
            @else
                <small class="text-muted-soft" style="font-size:.68rem;">Sem atrasos</small>
            @endif
            <div class="mt-2"><a href="{{ route('receivables.index') }}" style="font-size:.68rem;color:#4ade80;text-decoration:none;">Ver contas <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">A Pagar</div>
            <div class="fin-val" style="color:#f87171;">R$ {{ number_format($finPayablePending,2,',','.') }}</div>
            @if($finPayableOverdue>0)
                <small style="color:#fca5a5;font-size:.68rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Vencido: R$ {{ number_format($finPayableOverdue,2,',','.') }}</small>
            @else
                <small class="text-muted-soft" style="font-size:.68rem;">Sem atrasos</small>
            @endif
            <div class="mt-2"><a href="{{ route('bills.index') }}" style="font-size:.68rem;color:#f87171;text-decoration:none;">Ver contas <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">Saldo Previsto</div>
            <div class="fin-val" style="color:{{ $balPos?'#fbbf24':'#f87171' }};">{{ $balPos?'':'&minus;' }}R$ {{ number_format(abs($finCashBalance),2,',','.') }}</div>
            <small class="text-muted-soft" style="font-size:.68rem;">Receber menos Pagar</small>
            <div class="mt-2"><span style="font-size:.68rem;color:{{ $balPos?'#fbbf24':'#f87171' }};"><i class="bi bi-{{ $balPos?'arrow-up-circle':'arrow-down-circle' }} me-1"></i>{{ $balPos?'Positivo':'Negativo' }}</span></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        @php
            $duePayToday = $upcomingPayables->filter(fn($p)=>$p->due_date->isToday())->count();
            $duePayTotal = $upcomingPayables->count();
        @endphp
        <div class="fin-card h-100">
            <div class="fin-lbl text-muted-soft mb-1">Vencimentos</div>
            <div class="fin-val" style="color:#818cf8;">{{ $duePayTotal }} a pagar</div>
            @if($duePayToday>0)
                <small style="color:#fca5a5;font-size:.68rem;"><i class="bi bi-alarm-fill me-1"></i>{{ $duePayToday }} vence(m) hoje</small>
            @else
                <small class="text-muted-soft" style="font-size:.68rem;">Próximos 7 dias</small>
            @endif
            <div class="mt-2"><a href="{{ route('bills.index') }}" style="font-size:.68rem;color:#818cf8;text-decoration:none;">Ver agenda <i class="bi bi-arrow-right"></i></a></div>
        </div>
    </div>
</div>
@endif

{{-- ════ ROW 3 — Evolução de Vendas (col-8) + Doughnut (col-4) ════ --}}
<div class="row g-2 mb-2">
    {{-- Gráfico de linha --}}
    <div class="col-12 col-xl-8">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-graph-up-arrow me-2 text-success"></i>Evolução de Vendas</span>
                <div class="d-flex gap-3" style="font-size:.68rem;">
                    <span><span class="chart-legend-dot" style="background:#4ade80;"></span>Vendas</span>
                    <span><span class="chart-legend-dot" style="background:#f87171;"></span>Devoluções</span>
                    <span><span class="chart-legend-dot" style="background:#60a5fa;"></span>Líquido</span>
                </div>
            </div>
            <div class="p-2">
                <canvas id="salesChart" height="110"></canvas>
            </div>
        </div>
    </div>
    {{-- Doughnut --}}
    <div class="col-12 col-xl-4">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-trophy me-2 text-warning"></i>Top Produtos</span>
                <a href="{{ route('reports.top-products') }}" style="font-size:.68rem;color:#38bdf8;text-decoration:none;">Relatório <i class="bi bi-arrow-right"></i></a>
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
                <span><i class="bi bi-cash-stack me-2 text-info"></i>Fluxo de Caixa <small class="text-muted-soft ms-1" style="font-size:.65rem;">{{ $cfPeriodLabel }}</small></span>
                <div class="d-flex gap-3" style="font-size:.65rem;">
                    <span><span class="chart-legend-dot" style="background:#4ade80;"></span>Receber</span>
                    <span><span class="chart-legend-dot" style="background:#34d399;"></span>Recebido</span>
                    <span><span class="chart-legend-dot" style="background:#f87171;"></span>Pagar</span>
                    <span><span class="chart-legend-dot" style="background:#60a5fa;"></span>Saldo</span>
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
                <span><i class="bi bi-bar-chart-steps me-2 text-warning"></i>Ranking de Vendas</span>
            </div>
            <div class="px-3 py-2">
                @php
                    $topColors = ['#fbbf24','#94a3b8','#c97a3a','#6366f1','#22d3ee'];
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
                                        <span style="font-size:.68rem;color:#4ade80;">R$ {{ number_format($prod->total_revenue,2,',','.') }}</span>
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
                <span><i class="bi bi-receipt me-2 text-success"></i>Últimas Vendas</span>
                <a href="{{ route('sales.index') }}" style="font-size:.68rem;color:#38bdf8;text-decoration:none;">Ver todas <i class="bi bi-arrow-right"></i></a>
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
                        <td class="pe-3 text-end fw-semibold" style="color:#4ade80;">R$ {{ number_format($sale->total,2,',','.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4" style="color:rgba(148,163,184,.5);">Nenhuma venda registrada.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="card-dark chart-card h-100">
            <div class="chart-header">
                <span><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Estoque Crítico</span>
                <a href="{{ route('products.index') }}" style="font-size:.68rem;color:#38bdf8;text-decoration:none;">Ver todos <i class="bi bi-arrow-right"></i></a>
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
                            <span class="fw-bold" style="color:#f87171;">{{ $product->quantity }}</span>
                            <span style="font-size:.65rem;color:rgba(148,163,184,.4);"> / {{ $product->min_quantity }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-4" style="color:rgba(148,163,184,.5);">Nenhum produto crítico.</td></tr>
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
                <span><i class="bi bi-arrow-return-left me-2 text-danger"></i>Últimas Devoluções</span>
                <a href="{{ route('returns.index') }}" style="font-size:.68rem;color:#38bdf8;text-decoration:none;">Ver todas <i class="bi bi-arrow-right"></i></a>
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
                        <td class="pe-3 text-end fw-semibold" style="color:#f87171;">R$ {{ number_format($ret->total,2,',','.') }}</td>
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
const _chartDefaults = {
    tooltip: {
        backgroundColor:'rgba(8,13,26,.96)',borderColor:'rgba(14,165,233,.18)',borderWidth:1,
        titleColor:'#e2e8f0',bodyColor:'rgba(148,163,184,.85)',padding:10,
    },
    gridColor: 'rgba(148,163,184,.05)',
    tickColor: 'rgba(148,163,184,.55)',
};

// ─── Evolução de Vendas ────────────────────────────────────
(function(){
    const ctx = document.getElementById('salesChart'); if(!ctx) return;
    new Chart(ctx,{
        type:'line',
        data:{
            labels: @json($chartLabels),
            datasets:[
                {label:'Vendas',    data:@json($chartData),        borderColor:'#4ade80',backgroundColor:'rgba(74,222,128,.07)',borderWidth:2,pointRadius:2,fill:true, tension:.35},
                {label:'Devoluções',data:@json($chartReturnsData), borderColor:'#f87171',backgroundColor:'rgba(248,113,113,.05)',borderWidth:2,pointRadius:2,fill:true, tension:.35},
                {label:'Líquido',    data:@json($chartNetData),     borderColor:'#60a5fa',backgroundColor:'transparent',         borderWidth:1.5,pointRadius:1,fill:false,tension:.35,borderDash:[4,3]},
            ]
        },
        options:{
            responsive:true, maintainAspectRatio:true,
            interaction:{mode:'index',intersect:false},
            plugins:{legend:{display:false},tooltip:{..._chartDefaults.tooltip,callbacks:{label:c=>` ${c.dataset.label}: R$ ${c.parsed.y.toLocaleString('pt-BR',{minimumFractionDigits:2})}`}}},
            scales:{
                x:{grid:{color:_chartDefaults.gridColor},ticks:{color:_chartDefaults.tickColor,font:{size:10},maxTicksLimit:10}},
                y:{grid:{color:_chartDefaults.gridColor},ticks:{color:_chartDefaults.tickColor,font:{size:10},callback:v=>'R$'+v.toLocaleString('pt-BR',{minimumFractionDigits:0})}}
            }
        }
    });
})();

// ─── Fluxo de Caixa ───────────────────────────────────────
@if(Auth::user()->isGerente())
(function(){
    const ctx = document.getElementById('cashflowChart'); if(!ctx) return;
    new Chart(ctx,{
        type:'bar',
        data:{
            labels: @json($cfLabels),
            datasets:[
                {label:'A receber', data:@json($cfDataRecPend),  backgroundColor:'rgba(74,222,128,.45)', borderColor:'#4ade80', borderWidth:1},
                {label:'Recebido',  data:@json($cfDataRecReceb), backgroundColor:'rgba(52,211,153,.35)', borderColor:'#34d399', borderWidth:1},
                {label:'A pagar',   data:@json($cfDataPayPend),  backgroundColor:'rgba(248,113,113,.4)', borderColor:'#f87171', borderWidth:1},
                {label:'Pago',      data:@json($cfDataPayPaga),  backgroundColor:'rgba(248,113,113,.2)', borderColor:'#fca5a5', borderWidth:1},
                {type:'line',label:'Saldo',data:@json($cfDataBalance),borderColor:'#60a5fa',backgroundColor:'transparent',borderWidth:2,pointRadius:2,fill:false,tension:.3,yAxisID:'y'},
            ]
        },
        options:{
            responsive:true, maintainAspectRatio:true,
            interaction:{mode:'index',intersect:false},
            plugins:{legend:{display:false},tooltip:{..._chartDefaults.tooltip,callbacks:{label:c=>` ${c.dataset.label}: R$ ${c.parsed.y.toLocaleString('pt-BR',{minimumFractionDigits:2})}`}}},
            scales:{
                x:{stacked:true, grid:{color:_chartDefaults.gridColor},ticks:{color:_chartDefaults.tickColor,font:{size:10},maxTicksLimit:10}},
                y:{stacked:false,grid:{color:_chartDefaults.gridColor},ticks:{color:_chartDefaults.tickColor,font:{size:10},callback:v=>'R$'+v.toLocaleString('pt-BR',{minimumFractionDigits:0})}}
            }
        }
    });
})();
@endif

// ─── Doughnut Top Produtos ─────────────────────────────────
@if($topSellingProducts->count()>0)
(function(){
    const labels  = @json($topChartLabels);
    const sold    = @json($topChartData);
    const revenue = @json($topChartRevenue);
    const colors  = ['#fbbf24','#94a3b8','#c97a3a','#6366f1','#22d3ee'];
    const total   = sold.reduce((a,b)=>a+b,0);
    const ctx = document.getElementById('topProductsChart'); if(!ctx) return;
    const chart = new Chart(ctx,{
        type:'doughnut',
        data:{labels,datasets:[{data:sold,backgroundColor:colors.map(c=>c+'cc'),borderColor:colors,borderWidth:2,hoverOffset:8}]},
        options:{
            cutout:'65%',responsive:true,
            plugins:{
                legend:{display:false},
                tooltip:{..._chartDefaults.tooltip,callbacks:{label:ctx=>{
                    const pct=total>0?Math.round(ctx.parsed/total*100):0;
                    return [` ${ctx.parsed} un. (${pct}%)`,` R$ ${revenue[ctx.dataIndex].toLocaleString('pt-BR',{minimumFractionDigits:2})}`];
                }}}
            }
        }
    });
    const cv=document.getElementById('topChartCenterVal');
    ctx.addEventListener('mousemove',e=>{
        const pts=chart.getElementsAtEventForMode(e,'nearest',{intersect:true},false);
        if(pts.length&&cv) cv.textContent=sold[pts[0].index];
    });
    ctx.addEventListener('mouseleave',()=>{ if(cv) cv.textContent=total; });
    const leg=document.getElementById('topChartLegend');
    if(leg) labels.forEach((l,i)=>{
        const pct=total>0?Math.round(sold[i]/total*100):0;
        leg.innerHTML+=`<div style="display:flex;align-items:center;gap:.3rem;font-size:.65rem;color:rgba(226,232,240,.65);"><span style="width:8px;height:8px;border-radius:50%;background:${colors[i]};flex-shrink:0;"></span>${l} <b style="color:${colors[i]}">${pct}%</b></div>`;
    });
})();
@endif
</script>
@endpush

@endsection
