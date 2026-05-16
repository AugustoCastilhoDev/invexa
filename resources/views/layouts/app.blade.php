<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Invexa') — Invexa</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%25' stop-color='%234f46e5'/><stop offset='100%25' stop-color='%237c3aed'/></linearGradient></defs><rect width='32' height='32' rx='8' fill='url(%23g)'/><text x='50%25' y='50%25' dominant-baseline='central' text-anchor='middle' font-family='Inter,system-ui,sans-serif' font-size='18' font-weight='700' fill='white'>I</text></svg>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: radial-gradient(circle at top left, rgba(96, 165, 250, 0.10), transparent 20%),
                        radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.12), transparent 18%),
                        #08101d;
            color: #e2e8f0;
        }
        .navbar-main {
            background: rgba(5, 10, 20, 0.92);
            border-bottom: 1px solid rgba(148, 163, 184, .10);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding-top: .6rem;
            padding-bottom: .6rem;
        }
        .navbar-brand-custom {
            display: flex; align-items: center; gap: .55rem;
            font-size: 1rem; font-weight: 700; color: #f1f5f9 !important;
            letter-spacing: -.01em; text-decoration: none; transition: opacity .2s ease;
        }
        .navbar-brand-custom:hover { opacity: .85; }
        .brand-icon {
            width: 2rem; height: 2rem; border-radius: .45rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem; color: #fff; flex-shrink: 0;
            box-shadow: 0 0 0 1px rgba(139,92,246,.25), 0 4px 10px rgba(79,70,229,.35);
        }
        .navbar-main .nav-link {
            color: rgba(226, 232, 240, .65) !important;
            font-size: .875rem; font-weight: 500;
            padding: .35rem .7rem !important; border-radius: .4rem;
            transition: color .2s ease, background .2s ease;
        }
        .navbar-main .nav-link:hover { color: #f1f5f9 !important; background: rgba(255,255,255,.06); }
        .navbar-main .nav-link.active { color: #f1f5f9 !important; background: rgba(255,255,255,.09); }
        .nav-divider { width: 1px; height: 1.25rem; background: rgba(148, 163, 184, .2); align-self: center; margin: 0 .25rem; }
        .navbar-main .dropdown-menu {
            background: rgba(10, 18, 35, .97);
            border: 1px solid rgba(148, 163, 184, .14);
            border-radius: .6rem;
            box-shadow: 0 16px 32px rgba(0,0,0,.4);
            min-width: 200px; padding: .4rem; margin-top: .4rem !important;
        }
        .navbar-main .dropdown-item {
            color: rgba(226, 232, 240, .75); font-size: .875rem;
            border-radius: .4rem; padding: .45rem .75rem;
            transition: background .15s ease, color .15s ease;
        }
        .navbar-main .dropdown-item:hover { background: rgba(255,255,255,.08); color: #f1f5f9; }
        .navbar-main .dropdown-item-text { font-size: .78rem; color: rgba(148, 163, 184, .7); padding: .4rem .75rem; }
        .navbar-main .dropdown-divider { border-color: rgba(148, 163, 184, .12); margin: .3rem 0; }
        .user-avatar {
            width: 1.75rem; height: 1.75rem; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .form-control, .form-select {
            background-color: rgba(15, 23, 42, .6) !important;
            border-color: rgba(148, 163, 184, .2) !important;
            color: #e2e8f0 !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 23, 42, .9) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, .25) !important;
        }
        .card-dark-bg { background: rgba(15, 23, 42, .92); border: 1px solid rgba(148, 163, 184, .14); color: #e2e8f0; }
        .card-header-dark { background: rgba(15, 23, 42, .92); border-color: rgba(148, 163, 184, .12); }
        .table-dark-custom { background: rgba(15, 23, 42, .88); }
        .text-soft { color: rgba(226, 232, 240, .72) !important; }
        .alert-success { --bs-alert-bg: rgba(34,197,94,.10); --bs-alert-border-color: rgba(34,197,94,.2); color: #4ade80; }
        .alert-danger  { --bs-alert-bg: rgba(239,68,68,.10);  --bs-alert-border-color: rgba(239,68,68,.2);  color: #f87171; }
        .footer-main { background: rgba(5, 10, 20, 0.7); border-top: 1px solid rgba(148, 163, 184, .08); }
    </style>

    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-main sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand-custom" href="{{ route('dashboard') }}">
                <div class="brand-icon"><i class="bi bi-box-seam-fill"></i></div>
                <span>INVEXA</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none p-1" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Menu">
                <i class="bi bi-list fs-4 text-light"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">

                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1 opacity-75"></i>Dashboard
                        </a>
                    </li>

                    @if(Auth::check() && Auth::user()->isGerente())

                        {{-- Produtos --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                               href="{{ route('products.index') }}">
                                <i class="bi bi-box-seam me-1 opacity-75"></i>Produtos
                            </a>
                        </li>

                        {{-- Categorias --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                               href="{{ route('categories.index') }}">
                                <i class="bi bi-tag me-1 opacity-75"></i>Categorias
                            </a>
                        </li>

                        {{-- Estoque --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"
                               href="{{ route('stock.index') }}">
                                <i class="bi bi-arrow-left-right me-1 opacity-75"></i>Estoque
                            </a>
                        </li>

                        {{-- Relatórios --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bar-chart-line me-1 opacity-75"></i>Relatórios
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('reports.index') }}">
                                        <i class="bi bi-trophy me-2"></i>Produtos mais vendidos
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endif

                    {{-- Vendas --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"
                           href="{{ route('sales.index') }}">
                            <i class="bi bi-basket3 me-1 opacity-75"></i>Vendas
                        </a>
                    </li>

                    {{-- Devoluções --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}"
                           href="{{ route('returns.index') }}">
                            <i class="bi bi-arrow-return-left me-1 opacity-75"></i>Devoluções
                        </a>
                    </li>

                    {{-- Clientes --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                           href="{{ route('customers.index') }}">
                            <i class="bi bi-people me-1 opacity-75"></i>Clientes
                        </a>
                    </li>

                    @if(Auth::check() && Auth::user()->isAdmin())
                        {{-- Usuários (somente admin) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                               href="{{ route('users.index') }}">
                                <i class="bi bi-shield-person me-1 opacity-75"></i>Usuários
                            </a>
                        </li>
                    @endif

                    <li class="nav-item d-none d-lg-flex"><div class="nav-divider"></div></li>

                    {{-- Menu do usuário --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center gap-2 pe-1" href="#"
                           id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
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
                                    @endif
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @if(Auth::check() && Auth::user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.index') }}">
                                        <i class="bi bi-people me-2"></i>Gerenciar Usuários
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
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

    <main class="py-4 min-vh-100">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="footer-main py-4">
        <div class="container text-center" style="color: rgba(148,163,184,.6); font-size: .8rem;">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <i class="bi bi-box-seam-fill" style="color: #7c3aed;"></i>
                <span class="fw-semibold" style="color: rgba(226,232,240,.7);">INVEXA</span>
                <span>© {{ date('Y') }}</span>
            </div>
            <div>
                Sistema profissional para gestão de estoque e vendas · Desenvolvido por
                <a href="https://www.instagram.com/castilho_digital/" target="_blank" rel="noopener noreferrer"
                   style="color: #60a5fa; text-decoration: none; font-weight: 600;">
                    Castilho Soluções Digitais
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
