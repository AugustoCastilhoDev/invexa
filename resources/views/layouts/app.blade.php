<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Invexa') — Invexa</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23080D1A'/%3E%3Cpath d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/%3E%3Ccircle cx='24' cy='10' r='2.2' fill='%2338BDF8'/%3E%3C/svg%3E">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --brand-abyss:    #080D1A;
            --brand-navy:     #0D1929;
            --brand-sky:      #0EA5E9;
            --brand-electric: #38BDF8;
            --brand-ice:      #F0F9FF;
            --brand-glow:     rgba(14,165,233,.35);
        }
        body {
            background: radial-gradient(circle at top left,  rgba(14,165,233,.07), transparent 22%),
                        radial-gradient(circle at bottom right, rgba(56,189,248,.05), transparent 20%),
                        var(--brand-abyss);
            color: #e2e8f0;
        }
        .navbar-main {
            background: rgba(8,13,26,.93);
            border-bottom: 1px solid rgba(14,165,233,.12);
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
            padding-top:.6rem; padding-bottom:.6rem;
        }
        .navbar-brand-custom {
            display:flex; align-items:center; gap:.55rem;
            font-size:1rem; font-weight:700; color:#F0F9FF !important;
            letter-spacing:-.01em; text-decoration:none;
            transition:opacity .2s ease;
        }
        .navbar-brand-custom:hover { opacity:.82; }
        .brand-icon-svg { width:2rem; height:2rem; flex-shrink:0; filter: drop-shadow(0 0 6px var(--brand-glow)); }
        .company-logo-nav {
            height: 2rem; width: auto; max-width: 7rem; border-radius: 5px;
            object-fit: contain; border: 1px solid rgba(14,165,233,.15);
            padding: 2px 4px; background: rgba(13,25,41,.6);
        }
        .brand-divider { width: 1px; height: 1.4rem; background: rgba(14,165,233,.2); margin: 0 .1rem; }
        .navbar-main .nav-link {
            color:rgba(226,232,240,.65) !important; font-size:.875rem; font-weight:500;
            padding:.35rem .7rem !important; border-radius:.4rem;
            transition:color .2s ease,background .2s ease;
        }
        .navbar-main .nav-link:hover  { color:#F0F9FF !important; background:rgba(14,165,233,.08); }
        .navbar-main .nav-link.active { color:#38BDF8 !important; background:rgba(14,165,233,.12); }
        .nav-divider { width:1px; height:1.25rem; background:rgba(14,165,233,.18); align-self:center; margin:0 .25rem; }
        .nav-home-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem; border-radius: .4rem;
            color: rgba(226,232,240,.65) !important; font-size: 1rem;
            transition: color .2s ease, background .2s ease; text-decoration: none;
        }
        .nav-home-btn:hover  { color: #F0F9FF !important; background: rgba(14,165,233,.08); }
        .nav-home-btn.active { color: #38BDF8 !important; background: rgba(14,165,233,.12); }
        .navbar-main .dropdown-menu {
            background:rgba(10,18,35,.97); border:1px solid rgba(14,165,233,.14);
            border-radius:.6rem; box-shadow:0 16px 32px rgba(0,0,0,.45);
            min-width:200px; padding:.4rem; margin-top:.4rem !important;
        }
        .navbar-main .dropdown-item {
            color:rgba(226,232,240,.75); font-size:.875rem;
            border-radius:.4rem; padding:.45rem .75rem;
            transition:background .15s ease,color .15s ease;
        }
        .navbar-main .dropdown-item:hover  { background:rgba(14,165,233,.1); color:#F0F9FF; }
        .navbar-main .dropdown-item.active { background:rgba(14,165,233,.18); color:#38BDF8; }
        .navbar-main .dropdown-item-text   { font-size:.78rem; color:rgba(148,163,184,.7); padding:.4rem .75rem; }
        .navbar-main .dropdown-divider     { border-color:rgba(14,165,233,.12); margin:.3rem 0; }
        .user-avatar {
            width:1.75rem; height:1.75rem; border-radius:50%;
            background:linear-gradient(135deg,var(--brand-sky),var(--brand-electric));
            display:inline-flex; align-items:center; justify-content:center;
            font-size:.72rem; font-weight:700; color:var(--brand-abyss); flex-shrink:0;
        }
        .form-control, .form-select {
            background-color:rgba(13,25,41,.7) !important;
            border-color:rgba(14,165,233,.2) !important;
            color:#e2e8f0 !important;
        }
        .form-control:focus, .form-select:focus {
            background-color:rgba(13,25,41,.95) !important;
            border-color:var(--brand-sky) !important;
            box-shadow:0 0 0 0.2rem rgba(14,165,233,.25) !important;
        }
        .card-dark-bg  { background:rgba(13,25,41,.88); border:1px solid rgba(14,165,233,.1); color:#e2e8f0; }
        .card-header-dark { background:rgba(13,25,41,.95); border-color:rgba(14,165,233,.1); }
        .table-dark-custom { background:rgba(13,25,41,.82); }
        .text-soft { color:rgba(226,232,240,.72) !important; }
        .alert-success { --bs-alert-bg:rgba(34,197,94,.10); --bs-alert-border-color:rgba(34,197,94,.2); color:#4ade80; }
        .alert-danger  { --bs-alert-bg:rgba(239,68,68,.10);  --bs-alert-border-color:rgba(239,68,68,.2);  color:#f87171; }
        .trial-banner {
            background: linear-gradient(90deg, rgba(234,179,8,.12), rgba(234,179,8,.06));
            border-bottom: 1px solid rgba(234,179,8,.25);
            font-size: .82rem; padding: .45rem 1rem;
        }
        .trial-banner.urgent {
            background: linear-gradient(90deg, rgba(239,68,68,.15), rgba(239,68,68,.07));
            border-bottom-color: rgba(239,68,68,.3);
        }
        .impersonate-banner {
            background: linear-gradient(90deg, rgba(168,85,247,.18), rgba(139,92,246,.08));
            border-bottom: 2px solid rgba(168,85,247,.4);
            padding: .5rem 1rem; font-size: .82rem;
            position: sticky; top: 57px; z-index: 999;
            backdrop-filter: blur(8px);
        }
        .footer-main { background: rgba(8,13,26,.8); border-top: 1px solid rgba(14,165,233,.09); }
        .footer-dev-link {
            display: inline-flex; align-items: center; gap: .35rem;
            color: #38BDF8; text-decoration: none; font-weight: 600;
            transition: color .2s ease, opacity .2s ease;
        }
        .footer-dev-link:hover { color: #7DD3FC; opacity: .9; }
        .footer-dev-link .bi-instagram { font-size: .85rem; opacity: .85; }
        .stock-alert-badge {
            display:inline-flex; align-items:center; justify-content:center;
            min-width:1.1rem; height:1.1rem;
            background:#ef4444; color:#fff;
            border-radius:999px; font-size:.6rem; font-weight:700;
            padding:0 .28rem; line-height:1;
            position:relative; top:-1px; margin-left:.25rem;
            box-shadow:0 0 0 2px var(--brand-abyss);
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0%,100% { box-shadow:0 0 0 2px var(--brand-abyss),0 0 0 0 rgba(239,68,68,.6); }
            50%      { box-shadow:0 0 0 2px var(--brand-abyss),0 0 0 4px rgba(239,68,68,.0); }
        }
        /* Sino de notificações */
        #notif-list::-webkit-scrollbar { width: 4px; }
        #notif-list::-webkit-scrollbar-track { background: transparent; }
        #notif-list::-webkit-scrollbar-thumb { background: rgba(14,165,233,.3); border-radius: 4px; }
        .notif-item {
            display: flex; align-items: flex-start; gap: .75rem;
            padding: .65rem .9rem;
            border-bottom: 1px solid rgba(148,163,184,.07);
            cursor: pointer;
            transition: background .15s ease;
            text-decoration: none;
        }
        .notif-item:hover { background: rgba(14,165,233,.07); }
        .notif-item:last-child { border-bottom: 0; }
        .notif-icon-wrap {
            width: 2.1rem; height: 2.1rem; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 1rem;
        }
        .notif-icon-wrap.danger  { background: rgba(239,68,68,.18);  color: #f87171; }
        .notif-icon-wrap.warning { background: rgba(234,179,8,.15);  color: #facc15; }
        .notif-icon-wrap.info    { background: rgba(14,165,233,.15); color: #38BDF8; }
        .notif-icon-wrap.success { background: rgba(34,197,94,.15);  color: #4ade80; }
        .notif-title   { font-size: .8rem; font-weight: 600; color: #e2e8f0; line-height: 1.3; }
        .notif-message { font-size: .74rem; color: rgba(148,163,184,.85); line-height: 1.4; margin-top: .1rem; }
        .notif-time    { font-size: .68rem; color: rgba(148,163,184,.5); margin-top: .2rem; }
    </style>
    @stack('styles')
</head>
<body>

@php
    $authCompany = Auth::check() ? Auth::user()->company : null;
    $companyLogo = $authCompany?->logo ? Storage::url($authCompany->logo) : null;
    $companyName = $authCompany?->name ?? 'Invexa';
@endphp

<nav class="navbar navbar-expand-lg navbar-main sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand-custom" href="{{ Auth::check() ? route('home') : route('landing') }}">
            <svg class="brand-icon-svg" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#080D1A"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
            @if($companyLogo)
                <div class="brand-divider"></div>
                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" class="company-logo-nav" title="{{ $companyName }}">
            @else
                <span>{{ Auth::check() && $authCompany ? $companyName : 'INVEXA' }}</span>
            @endif
        </a>

        <button class="navbar-toggler border-0 shadow-none p-1" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Menu">
            <i class="bi bi-list fs-4 text-light"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">

                <li class="nav-item">
                    <a class="nav-home-btn {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}" title="Início"
                       data-bs-toggle="tooltip" data-bs-placement="bottom">
                        <i class="bi bi-house-fill"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1 opacity-75"></i>Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                        <i class="bi bi-basket3 me-1 opacity-75"></i>Vendas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                        <i class="bi bi-people me-1 opacity-75"></i>Clientes
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}" href="{{ route('returns.index') }}">
                        <i class="bi bi-arrow-return-left me-1 opacity-75"></i>Devoluções
                    </a>
                </li>

                @if(Auth::check() && Auth::user()->isGerente())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('stock.*','products.*','categories.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-boxes me-1 opacity-75"></i>Estoque
                        @if(!empty($lowStockAlert) && $lowStockAlert > 0)
                            <span class="stock-alert-badge">{{ $lowStockAlert > 99 ? '99+' : $lowStockAlert }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text">ESTOQUE</span></li>
                        <li><a class="dropdown-item {{ request()->routeIs('stock.*') ? 'active' : '' }}" href="{{ route('stock.index') }}"><i class="bi bi-arrow-left-right me-2"></i>Movimentações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item-text">CADASTROS</span></li>
                        <li><a class="dropdown-item {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam me-2"></i>Produtos</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tag me-2"></i>Categorias</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('suppliers.*','purchase-orders.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-truck me-1 opacity-75"></i>Compras
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-building me-2"></i>Fornecedores</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}"><i class="bi bi-cart-check me-2"></i>Ordens de Compra</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('bills.*','receivables.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-wallet2 me-1 opacity-75"></i>Financeiro
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('bills.*') ? 'active' : '' }}" href="{{ route('bills.index') }}"><i class="bi bi-credit-card-2-front me-2"></i>Contas a Pagar</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('receivables.*') ? 'active' : '' }}" href="{{ route('receivables.index') }}"><i class="bi bi-cash-coin me-2"></i>Contas a Receber</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bar-chart-line me-1 opacity-75"></i>Relatórios
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('reports.profitability') ? 'active' : '' }}" href="{{ route('reports.profitability') }}"><i class="bi bi-currency-dollar me-2"></i>Lucratividade</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.top-products') ? 'active' : '' }}" href="{{ route('reports.top-products') }}"><i class="bi bi-trophy me-2"></i>Produtos Mais Vendidos</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.purchases') ? 'active' : '' }}" href="{{ route('reports.purchases') }}"><i class="bi bi-cart-check me-2"></i>Relatório de Compras</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}" href="{{ route('reports.sales') }}"><i class="bi bi-graph-up me-2"></i>Relatório de Vendas</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.returns') ? 'active' : '' }}" href="{{ route('reports.returns') }}"><i class="bi bi-arrow-return-left me-2"></i>Relatório de Devoluções</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.financial') ? 'active' : '' }}" href="{{ route('reports.financial') }}"><i class="bi bi-wallet2 me-2"></i>Relatório Financeiro</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.stock') ? 'active' : '' }}" href="{{ route('reports.stock') }}"><i class="bi bi-boxes me-2"></i>Relatório de Estoque</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-grid me-2"></i>Ver Todos</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::check() && Auth::user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-shield-person me-1 opacity-75"></i>Usuários
                    </a>
                </li>
                @endif

                <li class="nav-item d-none d-lg-flex"><div class="nav-divider"></div></li>

                {{-- SINO DE NOTIFICAÇÕES --}}
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative px-2" href="#"
                       id="notifBell" role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false"
                       title="Notificações">
                        <i class="bi bi-bell fs-5"></i>
                        <span id="notif-badge"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                              style="background:#ef4444;font-size:.55rem;padding:.28rem .42rem;display:none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0"
                         style="min-width:340px;max-width:94vw;border:1px solid rgba(14,165,233,.18);background:rgba(10,18,35,.98);border-radius:.7rem;box-shadow:0 20px 40px rgba(0,0,0,.55);">
                        <div class="d-flex align-items-center justify-content-between px-3 py-2"
                             style="border-bottom:1px solid rgba(14,165,233,.12);">
                            <span class="fw-semibold text-white" style="font-size:.85rem;">
                                <i class="bi bi-bell me-1 text-info"></i>Notificações
                            </span>
                            <a href="#" id="notif-mark-all"
                               style="font-size:.72rem;color:#38BDF8;text-decoration:none;">
                                Marcar todas como lidas
                            </a>
                        </div>
                        <div id="notif-list" style="max-height:380px;overflow-y:auto;">
                            <div id="notif-empty" class="text-center py-4"
                                 style="color:rgba(148,163,184,.6);font-size:.82rem;">
                                <i class="bi bi-check2-circle d-block fs-4 mb-1 opacity-50"></i>
                                Nenhuma notificação nova
                            </div>
                        </div>
                        <div class="px-3 py-2" style="border-top:1px solid rgba(14,165,233,.10);">
                            <a href="{{ route('notifications.index') }}"
                               style="font-size:.76rem;color:#38BDF8;text-decoration:none;">
                                <i class="bi bi-list-ul me-1"></i>Ver todas as notificações
                            </a>
                        </div>
                    </div>
                </li>
                @endauth

                {{-- DROPDOWN USUÁRIO --}}
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 pe-1"
                       href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                        <span class="d-none d-lg-inline">{{ Auth::user()->name ?? 'Usuário' }}</span>
                        <i class="bi bi-chevron-down" style="font-size:.65rem; opacity:.6;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-item-text">
                                <div class="fw-semibold" style="color:#e2e8f0;">{{ Auth::user()->name ?? '' }}</div>
                                <div style="font-size:.75rem; color:rgba(148,163,184,.7);">{{ Auth::user()->email ?? '' }}</div>
                                @if(Auth::check())
                                    <span class="badge mt-1 bg-opacity-25 bg-{{ Auth::user()->role_badge }} text-{{ Auth::user()->role_badge }}" style="font-size:.65rem;">
                                        {{ Auth::user()->role_label }}
                                    </span>
                                    @if(Auth::user()->hasRole('admin') && Auth::user()->company)
                                        @php
                                            $planColors = ['free'=>'#94a3b8','pro'=>'#38BDF8','business'=>'#c084fc'];
                                            $pc = $planColors[Auth::user()->company->plan] ?? '#94a3b8';
                                        @endphp
                                        <span class="badge mt-1" style="background:rgba(14,165,233,.1); color:{{ $pc }}; font-size:.65rem; border:1px solid {{ $pc }}33;">
                                            Plano {{ strtoupper(Auth::user()->company->plan) }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear me-2"></i>Editar Perfil
                            </a>
                        </li>
                        @if(Auth::check() && Auth::user()->hasRole('admin'))
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('upgrade') ? 'active' : '' }}" href="{{ route('upgrade') }}">
                                <i class="bi bi-rocket-takeoff me-2"></i>Meu Plano
                                @if(Auth::user()->company?->plan === 'free')
                                    <span class="badge ms-1" style="background:rgba(14,165,233,.15); color:#38BDF8; font-size:.6rem;">Upgrade</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @if(Auth::check() && Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin())
                        <li>
                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                <i class="bi bi-people me-2"></i>Gerenciar Usuários
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color:#f87171;">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>

{{-- BANNER IMPERSONATE --}}
@if(!empty($isImpersonating) && $isImpersonating)
<div class="impersonate-banner">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-badge-fill" style="color:#c084fc;"></i>
            <span style="color:rgba(226,232,240,.9);">
                Modo Suporte ativo — você está visualizando como
                <strong style="color:#c084fc;">{{ $impersonatedCompany }}</strong>
            </span>
        </div>
        <form action="{{ route('admin.leave-impersonate') }}" method="POST" class="m-0">
            @csrf
            <button type="submit"
                style="background:rgba(168,85,247,.2); border:1px solid rgba(168,85,247,.4); color:#c084fc; font-size:.78rem; padding:.25rem .85rem; border-radius:.4rem; cursor:pointer;">
                <i class="bi bi-box-arrow-left me-1"></i>Sair do Modo Suporte
            </button>
        </form>
    </div>
</div>
@endif

{{-- BANNER TRIAL --}}
@auth
    @php
        $company   = auth()->user()->company;
        $trialDays = $company?->trialDaysLeft() ?? 0;
        $isOnTrial = $company?->isOnTrial() ?? false;
        $isUrgent  = $isOnTrial && $trialDays <= 3;
    @endphp
    @if($isOnTrial && auth()->user()->hasRole('admin'))
        <div class="trial-banner {{ $isUrgent ? 'urgent' : '' }}">
            <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock{{ $isUrgent ? '-fill text-danger' : ' text-warning' }}"></i>
                    @if($trialDays === 0)
                        <span class="{{ $isUrgent ? 'text-danger' : 'text-warning' }} fw-semibold">Seu período de teste encerra <strong>hoje</strong>!</span>
                    @elseif($trialDays === 1)
                        <span class="{{ $isUrgent ? 'text-danger' : 'text-warning' }} fw-semibold">Seu período de teste encerra <strong>amanhã</strong>!</span>
                    @else
                        <span style="color:rgba(226,232,240,.8);">Trial gratuito: <strong class="{{ $isUrgent ? 'text-danger' : 'text-warning' }}">{{ $trialDays }} dias restantes</strong></span>
                    @endif
                </div>
                <a href="{{ route('upgrade') }}" class="btn btn-sm {{ $isUrgent ? 'btn-danger' : 'btn-warning' }} fw-semibold py-0 px-3" style="font-size:.78rem; height:1.75rem; line-height:1.75rem;">
                    <i class="bi bi-rocket-takeoff me-1"></i>Ver planos
                </a>
            </div>
        </div>
    @endif
@endauth

<main class="py-4 min-vh-100">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
</main>

<footer class="footer-main py-4">
    <div class="container" style="color:rgba(148,163,184,.5); font-size:.78rem;">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <svg width="16" height="16" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#0D1929"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
            <span class="fw-semibold" style="color:rgba(226,232,240,.55); letter-spacing:.04em;">INVEXA</span>
            <span style="color:rgba(148,163,184,.25);">·</span>
            <span>Gestão de Estoque e Vendas</span>
            <span style="color:rgba(148,163,184,.25);">·</span>
            <span>&copy; {{ date('Y') }}</span>
        </div>
        <div style="height:1px; background:rgba(14,165,233,.07); margin-bottom:.75rem;"></div>
        <div class="text-center">
            Desenvolvido por
            <a href="https://www.instagram.com/castilho_digital/" target="_blank" rel="noopener noreferrer" class="footer-dev-link ms-1">
                <i class="bi bi-instagram"></i>Castilho Soluções Digitais
            </a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el))</script>

@auth
<script>
(function () {
    const POLL_INTERVAL = 60000;

    function fetchNotifications() {
        fetch('{{ route("notifications.unread") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const list  = document.getElementById('notif-list');
            const empty = document.getElementById('notif-empty');
            if (!badge || !list) return;

            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = data.count > 0 ? 'inline' : 'none';

            list.querySelectorAll('.notif-item').forEach(el => el.remove());

            if (data.items.length === 0) {
                if (empty) empty.style.display = 'block';
                return;
            }
            if (empty) empty.style.display = 'none';

            data.items.forEach(n => {
                const el = document.createElement('a');
                el.className = 'notif-item';
                el.href = n.url || '#';
                el.dataset.id = n.id;
                el.innerHTML = `
                    <div class="notif-icon-wrap ${n.type}">
                        <i class="bi ${n.icon}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="notif-title">${n.title}</div>
                        <div class="notif-message">${n.message}</div>
                        <div class="notif-time">${n.time}</div>
                    </div>
                `;
                el.addEventListener('click', () => markRead(n.id));
                list.insertBefore(el, empty);
            });
        })
        .catch(() => {});
    }

    function markRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => fetchNotifications()).catch(() => {});
    }

    document.getElementById('notif-mark-all')?.addEventListener('click', function (e) {
        e.preventDefault();
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(r => {
            // aceita tanto JSON quanto redirect
            fetchNotifications();
        }).catch(() => {});
    });

    document.getElementById('notifBell')?.addEventListener('show.bs.dropdown', fetchNotifications);

    document.addEventListener('DOMContentLoaded', function () {
        fetchNotifications();
        setInterval(fetchNotifications, POLL_INTERVAL);
    });
})();
</script>
@endauth

@stack('scripts')
</body>
</html>
