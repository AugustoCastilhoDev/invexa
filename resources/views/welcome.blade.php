<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invexa — Controle de Estoque e Vendas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f172a;
            --bg2:       #1e293b;
            --border:    rgba(148,163,184,.12);
            --primary:   #4f46e5;
            --primary2:  #7c3aed;
            --text:      #f1f5f9;
            --soft:      #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── NAV ── */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(15,23,42,.85);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: 1.3rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--text);
        }

        .nav-brand .logo-icon {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: .5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #fff;
        }

        .nav-actions { display: flex; gap: .75rem; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem 1.25rem;
            border-radius: .5rem;
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all .2s;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }
        .btn-outline:hover { border-color: rgba(148,163,184,.4); background: rgba(255,255,255,.04); }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary2));
            color: #fff;
            box-shadow: 0 4px 15px rgba(79,70,229,.35);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,.45); }

        /* ── HERO ── */
        .hero {
            text-align: center;
            padding: 6rem 1.5rem 4rem;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(79,70,229,.18), transparent);
            pointer-events: none;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(79,70,229,.15);
            border: 1px solid rgba(79,70,229,.3);
            color: #a5b4fc;
            font-size: .78rem;
            font-weight: 500;
            padding: .35rem .9rem;
            border-radius: 999px;
            margin-bottom: 1.75rem;
        }

        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            letter-spacing: -.02em;
        }

        .hero h1 span {
            background: linear-gradient(135deg, #818cf8, #a78bfa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.15rem;
            color: var(--soft);
            max-width: 580px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .hero-actions .btn { padding: .75rem 1.75rem; font-size: 1rem; }

        /* ── STATS ── */
        .stats {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            flex-wrap: wrap;
            margin: 0 auto 5rem;
            max-width: 700px;
        }

        .stat { text-align: center; }
        .stat-value { font-size: 1.8rem; font-weight: 700; color: var(--text); }
        .stat-label { font-size: .8rem; color: var(--soft); margin-top: .2rem; }

        /* ── FEATURES ── */
        .section { padding: 4rem 1.5rem; max-width: 1100px; margin: 0 auto; }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 700;
            margin-bottom: .6rem;
        }

        .section-title p { color: var(--soft); font-size: 1rem; }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.75rem;
            transition: border-color .2s, transform .2s;
        }

        .feature-card:hover {
            border-color: rgba(79,70,229,.4);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 2.8rem;
            height: 2.8rem;
            border-radius: .6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 { font-size: 1rem; font-weight: 600; margin-bottom: .5rem; }
        .feature-card p  { font-size: .875rem; color: var(--soft); line-height: 1.6; }

        /* ── CTA ── */
        .cta-section {
            text-align: center;
            padding: 5rem 1.5rem;
            position: relative;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(124,58,237,.12), transparent);
            pointer-events: none;
        }

        .cta-box {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 3.5rem 2rem;
            max-width: 640px;
            margin: 0 auto;
            position: relative;
        }

        .cta-box h2 { font-size: 1.8rem; font-weight: 700; margin-bottom: .75rem; }
        .cta-box p  { color: var(--soft); margin-bottom: 2rem; }

        /* ── FOOTER ── */
        footer {
            border-top: 1px solid var(--border);
            text-align: center;
            padding: 1.75rem;
            color: var(--soft);
            font-size: .82rem;
        }
    </style>
</head>
<body>

    {{-- ── NAVBAR ── --}}
    <nav>
        <a href="/" class="nav-brand">
            <div class="logo-icon"><i class="bi bi-box-seam-fill"></i></div>
            Invexa
        </a>
        <div class="nav-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Entrar</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="bi bi-rocket-takeoff"></i> Começar agora
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="hero">
        <div class="badge-pill">
            <i class="bi bi-stars"></i> Gestão inteligente de estoque e vendas
        </div>

        <h1>
            Controle total do seu<br>
            <span>negócio em um só lugar</span>
        </h1>

        <p>
            Gerencie produtos, categorias, estoque e vendas com uma interface
            moderna, rápida e intuitiva. Do registro ao relatório, tudo integrado.
        </p>

        <div class="hero-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2"></i> Acessar Dashboard
                </a>
            @else
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="bi bi-rocket-takeoff"></i> Criar conta grátis
                    </a>
                @endif
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="bi bi-box-arrow-in-right"></i> Fazer login
                </a>
            @endauth
        </div>

        <div class="stats">
            <div class="stat">
                <div class="stat-value">100%</div>
                <div class="stat-label">Web — acesse de qualquer lugar</div>
            </div>
            <div class="stat">
                <div class="stat-value">3</div>
                <div class="stat-label">Perfis de acesso</div>
            </div>
            <div class="stat">
                <div class="stat-value">∞</div>
                <div class="stat-label">Produtos cadastráveis</div>
            </div>
        </div>
    </section>

    {{-- ── FEATURES ── --}}
    <div class="section">
        <div class="section-title">
            <h2>Tudo que você precisa</h2>
            <p>Funcionalidades pensadas para o dia a dia do seu negócio</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(79,70,229,.15); color: #818cf8;">
                    <i class="bi bi-boxes"></i>
                </div>
                <h3>Gestão de Estoque</h3>
                <p>Cadastre produtos, defina quantidades mínimas e acompanhe o nível do seu estoque em tempo real.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(16,185,129,.15); color: #6ee7b7;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <h3>Registro de Vendas</h3>
                <p>Lance vendas rapidamente, vincule a clientes e gere histórico completo com totais automáticos.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(245,158,11,.15); color: #fcd34d;">
                    <i class="bi bi-tags"></i>
                </div>
                <h3>Categorias</h3>
                <p>Organize seu catálogo por categorias customizadas para facilitar buscas e relatórios.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(239,68,68,.15); color: #fca5a5;">
                    <i class="bi bi-people"></i>
                </div>
                <h3>Gestão de Usuários</h3>
                <p>Controle de acesso com perfis de Administrador, Gerente e Vendedor com permissões distintas.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(6,182,212,.15); color: #67e8f9;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3>Dashboard Analítico</h3>
                <p>Visualize os principais indicadores do seu negócio em cards e gráficos atualizados.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(168,85,247,.15); color: #d8b4fe;">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h3>Segurança</h3>
                <p>Autenticação segura, controle de sessão e proteção de rotas por perfil de acesso.</p>
            </div>
        </div>
    </div>

    {{-- ── CTA ── --}}
    <section class="cta-section">
        <div class="cta-box">
            <h2>Pronto para começar?</h2>
            <p>Crie sua conta e tenha controle total do seu estoque e vendas hoje mesmo.</p>

            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary" style="font-size:1rem; padding:.8rem 2rem;">
                    <i class="bi bi-speedometer2"></i> Ir ao Dashboard
                </a>
            @else
                <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary" style="font-size:1rem; padding:.8rem 2rem;">
                            <i class="bi bi-rocket-takeoff"></i> Criar conta grátis
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="btn btn-outline" style="font-size:1rem; padding:.8rem 2rem;">
                        <i class="bi bi-box-arrow-in-right"></i> Fazer login
                    </a>
                </div>
            @endauth
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <footer>
        &copy; {{ date('Y') }} <strong>Invexa</strong> — Controle de Estoque e Vendas
    </footer>

</body>
</html>