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

{{-- ── KPI Cards ── --}}
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
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card kpi-card dashboard-card text-white h-100"
             style="background: linear-gradient(135deg, #d97706, #f59e0b);">
            <div class="card-body d-flex flex-column justify-content-between gap-2">
                <div class="kpi-label">Faturamento Líquido</div>
                <div>
                    <div class="kpi-value" style="font-size: 1.55rem;">
                        R$ {{ number_format($periodNetRevenue ?? 0, 2, ',', '.') }}
                    </div>
                    <div style="font-size:.75rem; opacity:.85; margin-top:.25rem;">
                        Bruto: R$ {{ number_format($periodRevenue ?? 0, 2, ',', '.') }}
                        @if(($periodReturnsTotal ?? 0) > 0)
                            &nbsp;&mdash;&nbsp;
                            <span style="color:#fca5a5;">Dev: &minus; R$ {{ number_format($periodReturnsTotal, 2, ',', '.') }}</span>
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

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
{{-- MÓDULO 3.5 — PAINEL FINANCEIRO --}}
{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="mb-2 mt-2">
    <h5 class="text-white mb-1"><i class="bi bi-bank me-2 text-info"></i>Painel Financeiro</h5>
    <p class="text-soft mb-3" style="font-size:.82rem;">Contas a receber, a pagar, saldo previsto e vencimentos próximos.</p>
</div>

{{-- KPIs financeiros --}}
<div class="row g-3 mb-4">
    {{-- A Receber --}}
    <div class="col-6 col-xl-3">
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:rgba(34,197,94,.25);">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">A RECEBER</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:#4ade80;">
                    R$ {{ number_format($finReceivablePending, 2, ',', '.') }}
                </p>
                @if($finReceivableOverdue > 0)
                    <small style="color:#f87171;"><i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Vencido: R$ {{ number_format($finReceivableOverdue, 2, ',', '.') }}
                    </small>
                @else
                    <small class="text-soft">Sem vencimentos em atraso</small>
                @endif
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(34,197,94,.15);">
                <a href="{{ route('receivables.index') }}" class="text-decoration-none" style="font-size:.78rem;color:#4ade80;">
                    Ver contas <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- A Pagar --}}
    <div class="col-6 col-xl-3">
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:rgba(239,68,68,.25);">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">A PAGAR</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:#f87171;">
                    R$ {{ number_format($finPayablePending, 2, ',', '.') }}
                </p>
                @if($finPayableOverdue > 0)
                    <small style="color:#fca5a5;"><i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Vencido: R$ {{ number_format($finPayableOverdue, 2, ',', '.') }}
                    </small>
                @else
                    <small class="text-soft">Sem vencimentos em atraso</small>
                @endif
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(239,68,68,.15);">
                <a href="{{ route('payables.index') }}" class="text-decoration-none" style="font-size:.78rem;color:#f87171;">
                    Ver contas <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Saldo Previsto --}}
    <div class="col-6 col-xl-3">
        @php $balancePositive = $finCashBalance >= 0; @endphp
        <div class="card card-dark-bg dashboard-card h-100"
             style="border-color:{{ $balancePositive ? 'rgba(251,191,36,.25)' : 'rgba(239,68,68,.25)' }};">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">SALDO PREVISTO</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:{{ $balancePositive ? '#fbbf24' : '#f87171' }};">
                    {{ $balancePositive ? '' : '-' }}R$ {{ number_format(abs($finCashBalance), 2, ',', '.') }}
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

    {{-- Vencimentos hoje / 7 dias --}}
    <div class="col-6 col-xl-3">
        @php
            $dueToday = $upcomingPayables->filter(fn($p) => $p->due_date->isToday())->count()
                      + $upcomingReceivables->filter(fn($r) => $r->due_date->isToday())->count();
            $due7     = $upcomingPayables->count() + $upcomingReceivables->count();
        @endphp
        <div class="card card-dark-bg dashboard-card h-100" style="border-color:rgba(99,102,241,.25);">
            <div class="card-body py-3 px-4">
                <p class="text-soft mb-1" style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">VENCIMENTOS</p>
                <p class="fw-bold mb-1" style="font-size:1.35rem;color:#818cf8;">{{ $due7 }} conta(s)</p>
                @if($dueToday > 0)
                    <small style="color:#fca5a5;"><i class="bi bi-alarm-fill me-1"></i>{{ $dueToday }} vencem hoje</small>
                @else
                    <small class="text-soft">Nos próximos 7 dias</small>
                @endif
            </div>
            <div class="card-footer py-2 px-4" style="background:transparent;border-top:1px solid rgba(99,102,241,.15);">
                <span style="font-size:.78rem;color:#818cf8;">
                    <i class="bi bi-calendar-event me-1"></i>Próximos 7 dias
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Gráfico Fluxo de Caixa + Vencimentos próximos --}}
<div class="row g-3 mb-4">
    {{-- Gráfico --}}
    <div class="col-12 col-xl-7">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom">
                <h5 class="mb-1 text-white"><i class="bi bi-graph-up me-2 text-info"></i>Fluxo de Caixa — {{ now()->format('M/Y') }}</h5>
                <p class="text-soft mb-0" style="font-size:.8rem;">Entradas (recebíveis) vs Saídas (pagáveis) por data de vencimento no mês.</p>
            </div>
            <div class="card-body">
                @if($cfLabels->isEmpty())
                    <div class="text-center py-5 text-soft">
                        <i class="bi bi-bar-chart-line fs-3 d-block mb-2 opacity-40"></i>
                        Nenhum lançamento financeiro no mês atual.
                    </div>
                @else
                    <canvas id="cashFlowChart" height="130"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Vencimentos próximos 7 dias --}}
    <div class="col-12 col-xl-5">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom">
                <h5 class="mb-0 text-white"><i class="bi bi-calendar-week me-2 text-warning"></i>Próximos vencimentos</h5>
                <small class="text-soft">Contas a pagar e a receber nos próximos 7 dias</small>
            </div>
            <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
                @if($upcomingPayables->isEmpty() && $upcomingReceivables->isEmpty())
                    <div class="text-center py-5 text-soft">
                        <i class="bi bi-calendar-check fs-3 d-block mb-2 opacity-40"></i>
                        Nenhum vencimento nos próximos 7 dias.
                    </div>
                @else
                    <table class="table table-dark mb-0 align-middle table-dark-custom">
                        <thead>
<tr><th class="ps-3">Descrição</th><th>Vencimento</th><th>Valor</th><th>Tipo</th></tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingPayables as $p)
                            <tr>
                                <td class="ps-3" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->description }}</td>
                                <td>
                                    <span style="color:{{ $p->due_date->isToday() ? '#fca5a5' : '#94a3b8' }};font-size:.82rem;
                                                 font-weight:{{ $p->due_date->isToday() ? '700' : '400' }};">
                                        {{ $p->due_date->format('d/m') }}
                                        @if($p->due_date->isToday()) <i class="bi bi-alarm-fill"></i>@endif
                                    </span>
                                </td>
                                <td style="color:#f87171;font-weight:600;">R$ {{ number_format($p->amount, 2, ',', '.') }}</td>
                                <td><span class="badge" style="background:rgba(239,68,68,.2);color:#fca5a5;font-size:.7rem;">Pagar</span></td>
                            </tr>
                            @endforeach
                            @foreach($upcomingReceivables as $r)
                            <tr>
                                <td class="ps-3" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->description }}</td>
                                <td>
                                    <span style="color:{{ $r->due_date->isToday() ? '#fca5a5' : '#94a3b8' }};font-size:.82rem;
                                                 font-weight:{{ $r->due_date->isToday() ? '700' : '400' }};">
                                        {{ $r->due_date->format('d/m') }}
                                        @if($r->due_date->isToday()) <i class="bi bi-alarm-fill"></i>@endif
                                    </span>
                                </td>
                                <td style="color:#4ade80;font-weight:600;">R$ {{ number_format($r->amount, 2, ',', '.') }}</td>
                                <td><span class="badge" style="background:rgba(34,197,94,.2);color:#4ade80;font-size:.7rem;">Receber</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- FIM MÓDULO 3.5 --}}

{{-- ── Gráfico de vendas + Resumo rápido ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="mb-1 text-white">Faturamento por dia</h5>
                    <p class="text-soft mb-0">Bruto (azul) e devoluções (vermelho). Passe o mouse para ver o líquido.</p>
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
                    <div class="metric-content"><div><div class="metric-label">Faturamento bruto</div><div class="metric-value">R$ {{ number_format($periodRevenue ?? 0, 2, ',', '.') }}</div></div></div>
                    <div class="metric-icon" style="background: rgba(34,197,94,.12); color: #4ade80;"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="metric-row">
                    <div class="metric-content"><div><div class="metric-label">Devoluções ({{ $periodReturnsCount ?? 0 }})</div><div class="metric-value" style="color:#f87171;">@if(($periodReturnsTotal ?? 0) > 0)&minus; R$ {{ number_format($periodReturnsTotal, 2, ',', '.') }}@else R$ 0,00 @endif</div></div></div>
                    <div class="metric-icon" style="background: rgba(248,113,113,.12); color: #f87171;"><i class="bi bi-arrow-return-left"></i></div>
                </div>
                <div class="metric-row" style="border-color:rgba(251,191,36,.2);">
                    <div class="metric-content"><div><div class="metric-label">Faturamento líquido</div><div class="metric-value" style="color:#fbbf24; font-size:1.1rem;">R$ {{ number_format($periodNetRevenue ?? 0, 2, ',', '.') }}</div></div></div>
                    <div class="metric-icon" style="background: rgba(251,191,36,.15); color: #fbbf24;"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <div class="metric-row">
                    <div class="metric-content"><div><div class="metric-label">Venda líquida hoje</div><div class="metric-value">R$ {{ number_format($salesTodayNet ?? 0, 2, ',', '.') }}</div></div></div>
                    <div class="metric-icon" style="background: rgba(251,191,36,.12); color: #fbbf24;"><i class="bi bi-sun"></i></div>
                </div>
                <div class="metric-row">
                    <div class="metric-content"><div><div class="metric-label">Ticket médio</div><div class="metric-value">R$ {{ number_format($averageTicket ?? 0, 2, ',', '.') }}</div></div></div>
                    <div class="metric-icon" style="background: rgba(99,102,241,.15); color: #818cf8;"><i class="bi bi-calculator"></i></div>
                </div>
                <div class="metric-row">
                    <div class="metric-content"><div><div class="metric-label">Variação receita líquida</div><div class="metric-value">@if (!is_null($revenueChangePercent))<span style="color: {{ $revenueChangePercent >= 0 ? '#4ade80' : '#f87171' }}">{{ $revenueChangePercent >= 0 ? '+' : '' }}{{ number_format($revenueChangePercent, 2, ',', '.') }}%</span>@else<span class="text-soft">—</span>@endif</div></div></div>
                    <div class="metric-icon" style="background: rgba(96,165,250,.12); color: #60a5fa;"><i class="bi bi-arrow-repeat"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Últimas vendas + Estoque baixo ── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-6">
        <div class="card dashboard-card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
                <div><h5 class="mb-0 text-white">Últimas vendas</h5><small class="text-soft">Últimos 5 registros</small></div>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-light flex-shrink-0">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                        <thead><tr><th class="ps-3">#</th><th>Data</th><th>Cliente</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($latestSales as $sale)
                            <tr>
                                <td class="ps-3 text-soft">{{ $sale->id }}</td>
                                <td>{{ optional($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->customer_name ?: '—' }}</td>
                                <td class="fw-semibold text-white">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusMap = ['concluida'=>['class'=>'badge-concluida','label'=>'Concluída'],'pendente'=>['class'=>'badge-pendente','label'=>'Pendente'],'cancelada'=>['class'=>'badge-cancelada','label'=>'Cancelada']];
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
                <div><h5 class="mb-0 text-white">Produtos em estoque baixo</h5><small class="text-soft">Itens próximos ao estoque mínimo</small></div>
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

{{-- Últimas devoluções --}}
@if($latestReturns->isNotEmpty())
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card dashboard-card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center"
                 style="border-color:rgba(248,113,113,.2) !important;">
                <div><h5 class="mb-0" style="color:#f87171;"><i class="bi bi-arrow-return-left me-1"></i>Devoluções recentes</h5><small class="text-soft">Últimos 5 registros</small></div>
                <a href="{{ route('returns.index') }}" class="btn btn-sm btn-outline-danger flex-shrink-0">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                        <thead><tr><th class="ps-3">#</th><th>Venda</th><th>Cliente</th><th>Motivo</th><th>Valor Estornado</th><th>Data</th><th></th></tr></thead>
                        <tbody>
                            @foreach($latestReturns as $ret)
                            <tr>
                                <td class="ps-3 text-soft">#{{ $ret->id }}</td>
                                <td><a href="{{ route('sales.show', $ret->sale_id) }}" class="text-white text-decoration-none">Venda #{{ $ret->sale_id }}</a></td>
                                <td class="text-soft">{{ $ret->sale->customer_name ?? 'Não informado' }}</td>
                                <td class="text-soft">{{ $ret->reason_label }}</td>
                                <td class="fw-bold" style="color:#f87171;">&minus; R$ {{ number_format($ret->total, 2, ',', '.') }}</td>
                                <td class="text-soft" style="font-size:.82rem;">{{ $ret->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                <td><a href="{{ route('returns.show', $ret) }}" class="btn btn-outline-danger btn-sm">Ver</a></td>
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

{{-- Ações rápidas --}}
<div class="card dashboard-card card-dark-bg shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
            <div><h5 class="mb-1 text-white">Ações rápidas</h5><p class="text-soft mb-0">Exportações de relatórios e navegação direta.</p></div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('dashboard.export.csv', request()->all()) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV</a>
                <a href="{{ route('dashboard.export.pdf', request()->all()) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF</a>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary"><i class="bi bi-basket3 me-1"></i>Nova Venda</a>
                <a href="{{ route('returns.create') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-arrow-return-left me-1"></i>Nova Devolução</a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-4"><div class="p-3 rounded-3 bg-white bg-opacity-10 text-white"><div class="text-soft small">Média de faturamento</div><div class="h4 mb-0">R$ {{ number_format($periodAverageTicket ?? 0, 2, ',', '.') }}</div></div></div>
            <div class="col-12 col-md-4"><div class="p-3 rounded-3 bg-white bg-opacity-10 text-white"><div class="text-soft small">Menor venda</div><div class="h4 mb-0">R$ {{ number_format($periodMinSale ?? 0, 2, ',', '.') }}</div></div></div>
            <div class="col-12 col-md-4"><div class="p-3 rounded-3 bg-white bg-opacity-10 text-white"><div class="text-soft small">Vendas período anterior</div><div class="h4 mb-0">R$ {{ number_format($previousRevenue ?? 0, 2, ',', '.') }}</div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Gráfico de Vendas ──
    const salesCtx = document.getElementById('salesTrendChart');
    if (salesCtx) {
        const labels     = {!! json_encode($chartLabels->values() ?? []) !!};
        const grossData  = {!! json_encode($chartData->values() ?? []) !!};
        const returnData = {!! json_encode($chartReturnsData->values() ?? []) !!};
        const netData    = {!! json_encode($chartNetData->values() ?? []) !!};
        const canvasCtx  = salesCtx.getContext('2d');
        const gradient   = canvasCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(96, 165, 250, 0.90)');
        gradient.addColorStop(1, 'rgba(96, 165, 250, 0.30)');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Bruto', data: grossData, backgroundColor: gradient, borderColor: 'transparent', borderWidth: 0, borderRadius: 6, borderSkipped: false, barPercentage: 0.6, categoryPercentage: 0.8 },
                    { label: 'Devoluções', data: returnData, backgroundColor: 'rgba(248, 113, 113, 0.70)', borderColor: 'transparent', borderWidth: 0, borderRadius: 6, borderSkipped: false, barPercentage: 0.6, categoryPercentage: 0.8 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 600, easing: 'easeOutQuart' },
                scales: {
                    x: { stacked: false, grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } }, border: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.10)', drawTicks: false }, ticks: { color: '#94a3b8', font: { size: 11 }, padding: 8, callback: v => v >= 1000 ? 'R$ '+(v/1000).toLocaleString('pt-BR',{minimumFractionDigits:0})+'k' : 'R$ '+v.toLocaleString('pt-BR',{minimumFractionDigits:0}) }, border: { display: false } }
                },
                plugins: {
                    legend: { display: true, labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 12, padding: 16 } },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)', borderColor: 'rgba(148,163,184,0.2)', borderWidth: 1,
                        titleColor: '#e2e8f0', bodyColor: '#e2e8f0', padding: { x: 14, y: 10 }, cornerRadius: 8,
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': R$ ' + Number(ctx.raw||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}),
                            afterBody: items => { const net = netData[items[0].dataIndex]??0; return ['','\uD83D\uDCB0 Líquido: R$ '+Number(net).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})]; }
                        }
                    }
                }
            }
        });
    }

    // ── Gráfico Fluxo de Caixa ──
    const cfCtx = document.getElementById('cashFlowChart');
    if (cfCtx) {
        const cfLabels  = {!! json_encode($cfLabels->values()) !!};
        const cfDataRec = {!! json_encode($cfDataRec->values()) !!};
        const cfDataPay = {!! json_encode($cfDataPay->values()) !!};
        new Chart(cfCtx, {
            type: 'bar',
            data: {
                labels: cfLabels,
                datasets: [
                    { label: 'A Receber', data: cfDataRec, backgroundColor: 'rgba(74,222,128,0.65)', borderRadius: 5, borderSkipped: false, barPercentage: 0.6, categoryPercentage: 0.75 },
                    { label: 'A Pagar',   data: cfDataPay, backgroundColor: 'rgba(248,113,113,0.65)', borderRadius: 5, borderSkipped: false, barPercentage: 0.6, categoryPercentage: 0.75 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 500, easing: 'easeOutQuart' },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } }, border: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.08)' }, ticks: { color: '#94a3b8', font: { size: 10 }, callback: v => 'R$ '+(v>=1000?(v/1000).toFixed(0)+'k':v) }, border: { display: false } }
                },
                plugins: {
                    legend: { display: true, labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 12, padding: 12 } },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)', borderColor: 'rgba(148,163,184,0.2)', borderWidth: 1,
                        titleColor: '#e2e8f0', bodyColor: '#e2e8f0', cornerRadius: 8, padding: { x: 12, y: 8 },
                        callbacks: { label: ctx => ctx.dataset.label+': R$ '+Number(ctx.raw||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}) }
                    }
                }
            }
        });
    }
});
</script>
@endpush
