@extends('layouts.app')
@section('title', 'Início')

@push('styles')
<style>
    .home-hero {
        padding: 3rem 0 2rem;
        border-bottom: 1px solid rgba(14,165,233,.08);
    }
    .home-greeting {
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 700;
        color: #F0F9FF;
        letter-spacing: -.02em;
        margin-bottom: .25rem;
    }
    .home-sub   { font-size: .9rem; color: rgba(148,163,184,.7); }
    .home-date  { font-size: .82rem; color: rgba(14,165,233,.8); font-weight: 500; }
    .home-logo-mark { width: 52px; height: 52px; filter: drop-shadow(0 0 12px rgba(14,165,233,.45)); }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(220px,100%), 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }
    .module-card {
        background: rgba(13,25,41,.75);
        border: 1px solid rgba(14,165,233,.1);
        border-radius: .75rem;
        padding: 1.25rem 1.35rem;
        text-decoration: none;
        color: #e2e8f0;
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        display: flex; flex-direction: column; gap: .6rem;
    }
    .module-card:hover {
        transform: translateY(-3px);
        border-color: rgba(14,165,233,.35);
        background: rgba(14,165,233,.06);
        box-shadow: 0 8px 24px rgba(0,0,0,.3), 0 0 0 1px rgba(14,165,233,.12);
        color: #F0F9FF;
    }
    .module-card-icon {
        width: 2.4rem; height: 2.4rem;
        border-radius: .55rem;
        background: rgba(14,165,233,.1);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: #38BDF8; flex-shrink: 0;
        transition: background .18s ease;
    }
    .module-card:hover .module-card-icon { background: rgba(14,165,233,.18); }
    .module-card-title { font-size: .92rem; font-weight: 600; color: #e2e8f0; line-height: 1.3; }
    .module-card-desc  { font-size: .78rem; color: rgba(148,163,184,.65); line-height: 1.45; }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(180px,100%), 1fr));
        gap: .75rem; margin-top: 1.5rem;
    }
    .stat-item {
        background: rgba(13,25,41,.65);
        border: 1px solid rgba(14,165,233,.09);
        border-radius: .65rem;
        padding: 1rem 1.15rem;
        display: flex; flex-direction: column; gap: .2rem;
    }
    .stat-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: rgba(148,163,184,.55); }
    .stat-value { font-size: 1.35rem; font-weight: 700; color: #F0F9FF; letter-spacing: -.02em; line-height: 1; }
    .stat-hint  { font-size: .72rem; color: rgba(148,163,184,.5); }

    .section-label {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .1em; color: rgba(14,165,233,.7); margin-bottom: .6rem;
    }

    .plan-card {
        background: rgba(13,25,41,.75);
        border: 1px solid rgba(14,165,233,.12);
        border-radius: .75rem;
        padding: 1.25rem 1.35rem;
    }
    .plan-usage-bar { height: 6px; background: rgba(255,255,255,.07); border-radius: 999px; overflow: hidden; margin: .3rem 0 .2rem; }
    .plan-usage-fill { height: 100%; border-radius: 999px; transition: width .4s; }

    .stock-pill {
        display: inline-flex; align-items: center; gap: .3rem;
        background: rgba(239,68,68,.12);
        border: 1px solid rgba(239,68,68,.2);
        border-radius: 999px;
        padding: .2rem .65rem;
        font-size: .75rem; font-weight: 600; color: #f87171;
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
        50%      { box-shadow: 0 0 0 5px rgba(239,68,68,.0); }
    }
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="home-hero">
    <div class="d-flex align-items-center gap-3">
        <svg class="home-logo-mark" viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="7" fill="#0D1929"/>
            <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
            <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
        </svg>
        <div>
            <div class="home-greeting">Olá, {{ Auth::user()->name ?? 'bem-vindo' }} 👋</div>
            <div class="home-sub">O que vamos fazer hoje?</div>
        </div>
        <div class="ms-auto text-end d-none d-md-block">
            <div class="home-date" id="liveClock"></div>
            <div id="liveTime" style="font-size:.78rem; color:rgba(148,163,184,.5);"></div>
        </div>
    </div>
</div>

{{-- RESUMO DO DIA (gerente+) --}}
@if(Auth::check() && Auth::user()->isGerente())
<div class="mt-4">
    <div class="section-label">Resumo do dia</div>
    <div class="stat-row">
        <div class="stat-item">
            <span class="stat-label">Vendas hoje</span>
            <span class="stat-value">{{ $salesToday ?? '—' }}</span>
            <span class="stat-hint">registros</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Receita hoje</span>
            <span class="stat-value">R$&nbsp;{{ number_format($revenueToday ?? 0, 2, ',', '.') }}</span>
            <span class="stat-hint">em vendas confirmadas</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Contas vencendo</span>
            <span class="stat-value">{{ $billsDueToday ?? '—' }}</span>
            <span class="stat-hint">hoje ou em atraso</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">A receber hoje</span>
            <span class="stat-value">{{ $receivablesToday ?? '—' }}</span>
            <span class="stat-hint">contas previstas</span>
        </div>
    </div>
    @if(!empty($lowStockAlert) && $lowStockAlert > 0)
    <div class="mt-3">
        <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="stock-pill text-decoration-none">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $lowStockAlert }} produto(s) com estoque abaixo do mínimo — clique para ver
        </a>
    </div>
    @endif
</div>
@endif

{{-- CARD DO PLANO — somente admin da empresa --}}
@auth
@if(Auth::user()->isAdmin() && Auth::user()->company)
@php
    $company   = Auth::user()->company;
    $planLabel = ['free' => 'Free', 'pro' => 'Pro', 'business' => 'Business'][$company->plan] ?? 'Free';
    $planColor = ['free' => '#94a3b8', 'pro' => '#38BDF8', 'business' => '#c084fc'][$company->plan] ?? '#94a3b8';
    $resources = [
        ['products',  'Produtos',  'bi-box-seam'],
        ['customers', 'Clientes',  'bi-people'],
        ['users',     'Usuários',  'bi-shield-person'],
    ];
@endphp
<div class="mt-4">
    <div class="section-label">Seu Plano</div>
    <div class="plan-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:1.05rem; font-weight:700; color:{{ $planColor }};">{{ $planLabel }}</span>
                @if($company->isOnTrial())
                    <span style="background:rgba(234,179,8,.12); border:1px solid rgba(234,179,8,.25); color:#fbbf24; font-size:.68rem; font-weight:700; padding:.1rem .55rem; border-radius:999px;">TRIAL • {{ $company->trialDaysLeft() }}d restantes</span>
                @endif
            </div>
            <a href="{{ route('upgrade') }}" class="btn btn-sm" style="background:rgba(14,165,233,.1); border:1px solid rgba(14,165,233,.25); color:#38BDF8; font-size:.78rem;">
                <i class="bi bi-rocket-takeoff me-1"></i>
                {{ $company->plan === 'business' ? 'Ver planos' : 'Fazer Upgrade' }}
            </a>
        </div>
        <div class="row g-3">
            @foreach($resources as [$key, $label, $icon])
                @php
                    $limit = $company->limit($key);
                    $isInf = $limit === PHP_INT_MAX;
                    $pct   = $company->usagePercent($key);
                    $barColor = $pct >= 90 ? '#f87171' : ($pct >= 70 ? '#fbbf24' : '#4ade80');
                @endphp
                <div class="col-12 col-md-4">
                    <div style="font-size:.75rem; color:rgba(148,163,184,.6);">
                        <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                    </div>
                    @if($isInf)
                        <div style="font-size:.85rem; color:#4ade80; font-weight:600; margin-top:.2rem;">
                            <i class="bi bi-infinity me-1"></i>Ilimitado
                        </div>
                    @else
                        <div class="plan-usage-bar">
                            <div class="plan-usage-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                        </div>
                        <div style="font-size:.72rem; color:rgba(148,163,184,.5);">{{ $pct }}% — limite {{ number_format($limit) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endauth

{{-- MÓDULOS --}}
<div class="mt-4 mb-2">
    <div class="section-label">Módulos</div>
    <div class="module-grid">

        <a href="{{ route('dashboard') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-speedometer2"></i></div>
            <div><div class="module-card-title">Dashboard</div><div class="module-card-desc">Visão analítica completa de vendas, receitas e desempenho.</div></div>
        </a>

        <a href="{{ route('sales.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-basket3"></i></div>
            <div><div class="module-card-title">Vendas</div><div class="module-card-desc">Registre novas vendas, consulte histórico e emita recibos.</div></div>
        </a>

        <a href="{{ route('customers.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-people"></i></div>
            <div><div class="module-card-title">Clientes</div><div class="module-card-desc">Cadastre e gerencie a base de clientes da empresa.</div></div>
        </a>

        <a href="{{ route('returns.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-arrow-return-left"></i></div>
            <div><div class="module-card-title">Devoluções</div><div class="module-card-desc">Processe devoluções e estorne itens ao estoque.</div></div>
        </a>

        @if(Auth::check() && Auth::user()->isGerente())

        <a href="{{ route('products.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-box-seam"></i></div>
            <div><div class="module-card-title">Produtos</div><div class="module-card-desc">Gerencie o catálogo de produtos e preços.</div></div>
        </a>

        <a href="{{ route('stock.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-boxes"></i></div>
            <div><div class="module-card-title">Estoque</div><div class="module-card-desc">Movimentações, entradas, saídas e alertas de mínimo.</div></div>
        </a>

        <a href="{{ route('suppliers.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-building"></i></div>
            <div><div class="module-card-title">Fornecedores</div><div class="module-card-desc">Cadastro e gestão dos fornecedores parceiros.</div></div>
        </a>

        <a href="{{ route('purchase-orders.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-cart-check"></i></div>
            <div><div class="module-card-title">Ordens de Compra</div><div class="module-card-desc">Crie e acompanhe ordens de compra com fornecedores.</div></div>
        </a>

        <a href="{{ route('bills.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div><div class="module-card-title">Contas a Pagar</div><div class="module-card-desc">Controle despesas, vencimentos e baixas financeiras.</div></div>
        </a>

        <a href="{{ route('receivables.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-cash-coin"></i></div>
            <div><div class="module-card-title">Contas a Receber</div><div class="module-card-desc">Gerencie recebimentos, parcelas e confirmações.</div></div>
        </a>

        <a href="{{ route('reports.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-bar-chart-line"></i></div>
            <div><div class="module-card-title">Relatórios</div><div class="module-card-desc">Análises de vendas, compras e produtos mais vendidos.</div></div>
        </a>

        @endif

        @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('users.index') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-shield-person"></i></div>
            <div><div class="module-card-title">Usuários</div><div class="module-card-desc">Gerencie acessos, funções e permissões do sistema.</div></div>
        </a>
        @endif

        @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('upgrade') }}" class="module-card" style="border-color:rgba(14,165,233,.18);">
            <div class="module-card-icon" style="background:rgba(14,165,233,.12); color:#38BDF8;"><i class="bi bi-rocket-takeoff"></i></div>
            <div>
                <div class="module-card-title">Meu Plano</div>
                <div class="module-card-desc">Veja limites do seu plano e opções de upgrade.</div>
            </div>
        </a>
        @endif

        <a href="{{ route('profile.edit') }}" class="module-card">
            <div class="module-card-icon"><i class="bi bi-person-gear"></i></div>
            <div><div class="module-card-title">Meu Perfil</div><div class="module-card-desc">Atualize seus dados pessoais e senha de acesso.</div></div>
        </a>

    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Relógio em tempo real
(function () {
    const DAYS   = ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
    const MONTHS = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const now  = new Date();
        const date = `${DAYS[now.getDay()]}, ${now.getDate()} de ${MONTHS[now.getMonth()]} de ${now.getFullYear()}`;
        const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        const clockEl = document.getElementById('liveClock');
        const timeEl  = document.getElementById('liveTime');
        if (clockEl) clockEl.textContent = date;
        if (timeEl)  timeEl.textContent  = time;
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endpush
