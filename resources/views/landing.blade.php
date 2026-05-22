<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO --}}
    <title>Invexa — Sistema de Gestão para Pequenas Empresas</title>
    <meta name="description" content="Controle vendas, estoque, contas a pagar e receber em um só lugar. Experimente grátis por 14 dias, sem cartão de crédito.">
    <meta name="keywords" content="sistema de gestão, ERP, controle de estoque, vendas, contas a pagar, pequenas empresas, SaaS">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Invexa — Sistema de Gestão para Pequenas Empresas">
    <meta property="og:description" content="Controle vendas, estoque, contas a pagar e receber em um só lugar. 14 dias grátis.">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23080D1A'/%3E%3Cpath d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/%3E%3Ccircle cx='24' cy='10' r='2.2' fill='%2338BDF8'/%3E%3C/svg%3E">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --abyss:    #080D1A;
            --navy:     #0D1929;
            --sky:      #0EA5E9;
            --electric: #38BDF8;
            --ice:      #F0F9FF;
            --glow:     rgba(14,165,233,.35);
        }
        * { scroll-behavior: smooth; }
        body { background: var(--abyss); color: #e2e8f0; font-family: system-ui, -apple-system, sans-serif; }

        /* NAV */
        .lp-nav { background: rgba(8,13,26,.92); border-bottom: 1px solid rgba(14,165,233,.1); backdrop-filter: blur(14px); padding: .6rem 0; }
        .lp-nav .navbar-brand { display: flex; align-items: center; gap: .5rem; font-weight: 700; color: var(--ice) !important; font-size: 1rem; text-decoration: none; }
        .lp-nav .nav-link { color: rgba(226,232,240,.65) !important; font-size: .875rem; }
        .lp-nav .nav-link:hover { color: var(--ice) !important; }

        /* HERO */
        .hero { padding: 100px 0 80px; background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(14,165,233,.18), transparent), radial-gradient(circle at 80% 80%, rgba(56,189,248,.07), transparent 40%); }
        .hero-badge { display: inline-flex; align-items: center; gap: .4rem; background: rgba(14,165,233,.12); border: 1px solid rgba(14,165,233,.25); border-radius: 999px; padding: .25rem .9rem; font-size: .78rem; font-weight: 600; color: var(--electric); margin-bottom: 1.5rem; }
        .hero h1 { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800; line-height: 1.15; color: #f1f5f9; }
        .hero h1 span { background: linear-gradient(90deg, var(--sky), var(--electric)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p.lead { font-size: 1.1rem; color: rgba(226,232,240,.7); max-width: 540px; margin: 1.25rem auto 2rem; }
        .btn-hero-primary { background: linear-gradient(135deg, var(--sky), #0284c7); border: none; color: #fff; font-weight: 700; padding: .8rem 2rem; border-radius: .6rem; font-size: 1rem; box-shadow: 0 4px 20px rgba(14,165,233,.35); transition: transform .2s, box-shadow .2s; text-decoration: none; display: inline-block; }
        .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(14,165,233,.45); color: #fff; }
        .btn-hero-secondary { background: transparent; border: 1px solid rgba(14,165,233,.3); color: rgba(226,232,240,.8); padding: .8rem 1.8rem; border-radius: .6rem; font-size: 1rem; transition: border-color .2s, color .2s; text-decoration: none; display: inline-block; }
        .btn-hero-secondary:hover { border-color: var(--sky); color: var(--ice); }
        .hero-metrics { display: flex; justify-content: center; gap: 2.5rem; margin-top: 3.5rem; flex-wrap: wrap; }
        .metric-item { text-align: center; }
        .metric-item .value { font-size: 1.8rem; font-weight: 800; background: linear-gradient(90deg, var(--sky), var(--electric)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .metric-item .label { font-size: .78rem; color: rgba(148,163,184,.7); }

        /* SEÇÕES */
        section { padding: 80px 0; }
        .section-label { display: inline-block; font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--sky); margin-bottom: .6rem; }
        .section-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; color: #f1f5f9; margin-bottom: .75rem; }
        .section-sub { color: rgba(226,232,240,.6); max-width: 520px; margin: 0 auto 3rem; }
        .divider { height: 1px; background: rgba(14,165,233,.08); margin: 0; }

        /* SEGMENTOS */
        .segments { padding: 32px 0; background: rgba(13,25,41,.4); }
        .segment-pill { display: inline-flex; align-items: center; gap: .45rem; background: rgba(14,165,233,.07); border: 1px solid rgba(14,165,233,.12); border-radius: 999px; padding: .35rem 1rem; font-size: .82rem; color: rgba(226,232,240,.65); }
        .segment-pill i { color: var(--sky); }

        /* COMO FUNCIONA */
        .how-it-works { background: rgba(13,25,41,.35); }
        .step-connector { display: flex; flex-direction: column; align-items: center; }
        .step-circle { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, rgba(14,165,233,.2), rgba(56,189,248,.1)); border: 2px solid rgba(14,165,233,.35); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--sky); flex-shrink: 0; position: relative; z-index: 1; }
        .step-number { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; border-radius: 50%; background: var(--sky); color: var(--abyss); font-size: .65rem; font-weight: 800; display: flex; align-items: center; justify-content: center; }
        .step-line { width: 2px; flex: 1; background: linear-gradient(to bottom, rgba(14,165,233,.3), rgba(14,165,233,.05)); min-height: 40px; }
        .step-content { background: rgba(13,25,41,.7); border: 1px solid rgba(14,165,233,.1); border-radius: 14px; padding: 22px 24px; transition: border-color .25s; }
        .step-content:hover { border-color: rgba(14,165,233,.3); }
        .step-content h5 { color: #f1f5f9; font-weight: 700; font-size: .95rem; margin-bottom: .4rem; }
        .step-content p { color: rgba(226,232,240,.6); font-size: .85rem; margin: 0; }

        /* FEATURES */
        .features { background: rgba(13,25,41,.5); }
        .feature-card { background: rgba(13,25,41,.8); border: 1px solid rgba(14,165,233,.1); border-radius: 14px; padding: 28px; height: 100%; transition: border-color .25s, transform .25s; }
        .feature-card:hover { border-color: rgba(14,165,233,.35); transform: translateY(-4px); }
        .feature-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(14,165,233,.12); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--sky); margin-bottom: 1rem; }
        .feature-card h5 { color: #f1f5f9; font-weight: 700; font-size: 1rem; }
        .feature-card p { font-size: .875rem; color: rgba(226,232,240,.6); margin: 0; }

        /* DEPOIMENTOS */
        .testimonials { background: var(--abyss); }
        .testimonial-card { background: rgba(13,25,41,.85); border: 1px solid rgba(14,165,233,.1); border-radius: 14px; padding: 28px; height: 100%; }
        .testimonial-card .stars { color: #FBBF24; font-size: .85rem; margin-bottom: .75rem; letter-spacing: .05em; }
        .testimonial-card blockquote { font-size: .9rem; color: rgba(226,232,240,.8); line-height: 1.65; font-style: italic; margin: 0 0 1.25rem; }
        .testimonial-card blockquote::before { content: '\201C'; color: var(--sky); font-size: 1.6rem; line-height: 0; vertical-align: -0.45em; margin-right: .15rem; }
        .testimonial-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--sky), var(--electric)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; color: var(--abyss); flex-shrink: 0; }
        .testimonial-name { font-size: .85rem; font-weight: 700; color: #e2e8f0; }
        .testimonial-role { font-size: .75rem; color: rgba(148,163,184,.6); }

        /* PLANOS */
        .plans { background: rgba(8,13,26,.6); }
        .plan-card { background: rgba(13,25,41,.85); border: 1px solid rgba(14,165,233,.12); border-radius: 16px; padding: 32px 28px; height: 100%; position: relative; transition: border-color .25s, box-shadow .25s; }
        .plan-card.featured { border-color: var(--sky); box-shadow: 0 0 40px rgba(14,165,233,.15); }
        .plan-badge { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: linear-gradient(90deg, var(--sky), var(--electric)); color: var(--abyss); font-size: .72rem; font-weight: 700; padding: .2rem 1rem; border-radius: 999px; white-space: nowrap; }
        .plan-name { font-size: .875rem; font-weight: 700; color: var(--electric); text-transform: uppercase; letter-spacing: .08em; }
        .plan-price { font-size: 2.4rem; font-weight: 800; color: #f1f5f9; line-height: 1; margin: .5rem 0 .1rem; }
        .plan-price span { font-size: .95rem; font-weight: 400; color: rgba(148,163,184,.6); }
        .plan-price-billed { font-size: .75rem; color: rgba(148,163,184,.5); margin-bottom: .25rem; }
        .plan-price-old { font-size: 1rem; color: rgba(148,163,184,.45); text-decoration: line-through; margin-bottom: .1rem; }
        .plan-offer-badge { display: inline-block; background: rgba(251,191,36,.15); border: 1px solid rgba(251,191,36,.35); color: #FCD34D; font-size: .7rem; font-weight: 700; padding: .15rem .7rem; border-radius: 999px; margin-bottom: .5rem; }
        .plan-desc { font-size: .82rem; color: rgba(148,163,184,.6); margin-bottom: 1.5rem; }
        .plan-features li { font-size: .875rem; color: rgba(226,232,240,.75); padding: .4rem 0; border-bottom: 1px solid rgba(14,165,233,.06); display: flex; align-items: center; gap: .5rem; }
        .plan-features li:last-child { border-bottom: none; }
        .plan-features li i.bi-check-circle-fill { color: var(--sky); flex-shrink: 0; }
        .plan-features li.disabled { color: rgba(148,163,184,.35); }
        .plan-features li.disabled i { color: rgba(148,163,184,.25); flex-shrink: 0; }

        /* TOGGLE */
        .billing-toggle { display: flex; align-items: center; justify-content: center; gap: .75rem; margin-bottom: 2.5rem; }
        .billing-toggle .toggle-label { font-size: .875rem; color: rgba(226,232,240,.6); cursor: pointer; transition: color .2s; }
        .billing-toggle .toggle-label.active { color: var(--ice); font-weight: 600; }
        .toggle-switch { position: relative; width: 52px; height: 28px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; inset: 0; background: rgba(14,165,233,.2); border: 1px solid rgba(14,165,233,.3); border-radius: 999px; cursor: pointer; transition: background .25s; }
        .toggle-slider:before { content: ''; position: absolute; width: 20px; height: 20px; left: 3px; top: 3px; background: var(--sky); border-radius: 50%; transition: transform .25s; }
        .toggle-switch input:checked + .toggle-slider { background: rgba(14,165,233,.3); }
        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(24px); }
        .annual-badge { display: inline-block; background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.3); color: #4ade80; font-size: .68rem; font-weight: 700; padding: .1rem .55rem; border-radius: 999px; }

        /* FAQ */
        .faq { background: rgba(13,25,41,.4); }
        .faq .accordion-item { background: rgba(13,25,41,.7); border: 1px solid rgba(14,165,233,.1); border-radius: 10px !important; margin-bottom: .75rem; }
        .faq .accordion-button { background: transparent; color: #e2e8f0; font-weight: 600; font-size: .9rem; border-radius: 10px !important; box-shadow: none; }
        .faq .accordion-button:not(.collapsed) { color: var(--electric); background: transparent; }
        .faq .accordion-button::after { filter: invert(1) brightness(.6); }
        .faq .accordion-body { color: rgba(226,232,240,.65); font-size: .875rem; }

        /* CTA FINAL */
        .cta-final { background: linear-gradient(135deg, rgba(14,165,233,.12), rgba(56,189,248,.06)); border-top: 1px solid rgba(14,165,233,.15); border-bottom: 1px solid rgba(14,165,233,.15); text-align: center; }
        .cta-final h2 { font-size: clamp(1.6rem, 3vw, 2.4rem); font-weight: 800; color: #f1f5f9; }
        .cta-final p { color: rgba(226,232,240,.6); }

        /* FOOTER */
        .lp-footer { background: rgba(8,13,26,.95); border-top: 1px solid rgba(14,165,233,.08); padding: 2.5rem 0; font-size: .8rem; color: rgba(148,163,184,.5); }
        .lp-footer a { color: var(--electric); text-decoration: none; }
        .lp-footer a:hover { color: #7DD3FC; }
    </style>
</head>
<body>

{{-- NAV --}}
<nav class="lp-nav navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#080D1A"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
            INVEXA
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#lpNav">
            <i class="bi bi-list fs-4 text-light"></i>
        </button>
        <div class="collapse navbar-collapse" id="lpNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <li class="nav-item"><a class="nav-link" href="#how">Como funciona</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Funcionalidades</a></li>
                <li class="nav-item"><a class="nav-link" href="#plans">Planos</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light" style="border-color:rgba(14,165,233,.4); font-size:.82rem;">Entrar</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary" style="background:var(--sky); border:none; font-size:.82rem; font-weight:600;">Começar grátis</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="hero text-center">
    <div class="container">
        <div class="hero-badge"><i class="bi bi-stars"></i>14 dias grátis · Sem cartão de crédito</div>
        <h1>Gerencie seu negócio<br><span>com simplicidade e controle</span></h1>
        <p class="lead mx-auto">Invexa é o sistema de gestão completo para pequenas e médias empresas: vendas, estoque, financeiro e relatórios — tudo em um só lugar.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register') }}" class="btn-hero-primary">Criar conta grátis</a>
            <a href="#how" class="btn-hero-secondary">Ver como funciona</a>
        </div>
        <div class="hero-metrics">
            <div class="metric-item"><div class="value">14 dias</div><div class="label">Trial gratuito</div></div>
            <div class="metric-item"><div class="value">3 min</div><div class="label">Para começar</div></div>
            <div class="metric-item"><div class="value">100%</div><div class="label">Web — sem instalação</div></div>
            <div class="metric-item"><div class="value">Multi</div><div class="label">Usuários por empresa</div></div>
        </div>
    </div>
</section>

<div class="divider"></div>

{{-- SEGMENTOS --}}
<section class="segments">
    <div class="container">
        <p class="text-center mb-3" style="font-size:.78rem; color:rgba(148,163,184,.5); letter-spacing:.08em; text-transform:uppercase;">Ideal para</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <span class="segment-pill"><i class="bi bi-shop"></i> Lojas de varejo</span>
            <span class="segment-pill"><i class="bi bi-tools"></i> Prestadores de serviço</span>
            <span class="segment-pill"><i class="bi bi-cup-hot"></i> Alimentação e delivery</span>
            <span class="segment-pill"><i class="bi bi-bag"></i> Moda e confecção</span>
            <span class="segment-pill"><i class="bi bi-building"></i> Distribuidores</span>
            <span class="segment-pill"><i class="bi bi-phone"></i> Assistências técnicas</span>
        </div>
    </div>
</section>

<div class="divider"></div>

{{-- COMO FUNCIONA --}}
<section class="how-it-works" id="how">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Simples assim</span>
            <h2 class="section-title">Como funciona</h2>
            <p class="section-sub">Do cadastro à primeira venda em menos de 10 minutos.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-7">

                {{-- Passo 1 --}}
                <div class="d-flex gap-4 mb-2">
                    <div class="step-connector">
                        <div class="step-circle position-relative">
                            <i class="bi bi-person-plus-fill"></i>
                            <span class="step-number">1</span>
                        </div>
                        <div class="step-line"></div>
                    </div>
                    <div class="step-content w-100 mb-0" style="margin-top:0;">
                        <h5>Crie sua conta em 2 minutos</h5>
                        <p>Informe nome, e-mail e dados da empresa. Sem burocracia — seu painel fica pronto na hora. Nenhum cartão de crédito necessário.</p>
                    </div>
                </div>

                {{-- Passo 2 --}}
                <div class="d-flex gap-4 mb-2">
                    <div class="step-connector">
                        <div class="step-circle position-relative">
                            <i class="bi bi-box-seam"></i>
                            <span class="step-number">2</span>
                        </div>
                        <div class="step-line"></div>
                    </div>
                    <div class="step-content w-100">
                        <h5>Cadastre produtos e clientes</h5>
                        <p>Adicione seu estoque com preço de custo, venda e quantidade mínima. O assistente de onboarding guia você passo a passo.</p>
                    </div>
                </div>

                {{-- Passo 3 --}}
                <div class="d-flex gap-4 mb-2">
                    <div class="step-connector">
                        <div class="step-circle position-relative">
                            <i class="bi bi-basket3-fill"></i>
                            <span class="step-number">3</span>
                        </div>
                        <div class="step-line"></div>
                    </div>
                    <div class="step-content w-100">
                        <h5>Registre sua primeira venda</h5>
                        <p>Use o PDV para adicionar itens, aplicar descontos e registrar o pagamento. O estoque é atualizado automaticamente.</p>
                    </div>
                </div>

                {{-- Passo 4 --}}
                <div class="d-flex gap-4">
                    <div class="step-connector">
                        <div class="step-circle position-relative">
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span class="step-number">4</span>
                        </div>
                    </div>
                    <div class="step-content w-100">
                        <h5>Acompanhe pelo Dashboard</h5>
                        <p>Veja faturamento, lucro, produtos mais vendidos e alertas de estoque em tempo real. Exporte relatórios em PDF ou CSV quando precisar.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<div class="divider"></div>

{{-- FEATURES --}}
<section class="features" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Funcionalidades</span>
            <h2 class="section-title">Tudo que sua empresa precisa</h2>
            <p class="section-sub">Módulos integrados para você ter visibilidade total do negócio sem complicação.</p>
        </div>
        <div class="row g-4">
            <div class="col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-basket3-fill"></i></div>
                    <h5>Ponto de Venda (PDV)</h5>
                    <p>Registre vendas com múltiplos itens, formas de pagamento, descontos e gere nota em segundos.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-boxes"></i></div>
                    <h5>Controle de Estoque</h5>
                    <p>Movimentações automáticas por venda e compra, alertas de estoque mínimo e histórico completo.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-wallet2"></i></div>
                    <h5>Financeiro</h5>
                    <p>Contas a pagar e receber com parcelamento, recorrência e controle de inadimplência.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-cart-check"></i></div>
                    <h5>Ordens de Compra</h5>
                    <p>Gerencie compras de fornecedores, receba mercadorias e atualize o estoque automaticamente.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
                    <h5>Relatórios &amp; Dashboard</h5>
                    <p>Gráficos em tempo real, relatórios de vendas, estoque, lucratividade e financeiro — exportáveis em PDF e CSV.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-person-lock"></i></div>
                    <h5>Controle de Acesso</h5>
                    <p>Perfis Admin, Gerente e Vendedor com permissões distintas para cada área do sistema.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="divider"></div>

{{-- DEPOIMENTOS --}}
<section class="testimonials" id="testimonials">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Quem já usa</span>
            <h2 class="section-title">O que nossos clientes dizem</h2>
            <p class="section-sub">Pequenas empresas que ganharam tempo e clareza financeira com o Invexa.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <blockquote>Antes eu controlava tudo em planilha e perdia horas toda semana. Com o Invexa, sei exatamente o que vendi, o que lucrei e o que tenho em estoque em tempo real.</blockquote>
                    <div class="d-flex align-items-center gap-2">
                        <div class="testimonial-avatar">MC</div>
                        <div>
                            <div class="testimonial-name">Mariana C.</div>
                            <div class="testimonial-role">Loja de roupas — MG</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <blockquote>Sistema simples e completo. Cadastrei os produtos, treinei minha equipe em menos de uma tarde e já estávamos vendendo. O relatório de lucratividade por produto é incrível.</blockquote>
                    <div class="d-flex align-items-center gap-2">
                        <div class="testimonial-avatar">RF</div>
                        <div>
                            <div class="testimonial-name">Ricardo F.</div>
                            <div class="testimonial-role">Distribuidora de alimentos — SP</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <blockquote>O controle financeiro me salvou. Agora consigo ver contas a pagar e receber lado a lado, nunca mais perdi um vencimento. Recomendo para qualquer pequeno negócio.</blockquote>
                    <div class="d-flex align-items-center gap-2">
                        <div class="testimonial-avatar">AP</div>
                        <div>
                            <div class="testimonial-name">Ana P.</div>
                            <div class="testimonial-role">Assistência técnica — RJ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="divider"></div>

{{-- PLANOS --}}
<section class="plans" id="plans">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-label">Planos &amp; Preços</span>
            <h2 class="section-title">Simples, transparente e justo</h2>
            <p class="section-sub mb-0">Comece grátis por 14 dias. Sem cartão de crédito. Cancele quando quiser.</p>
        </div>

        <div class="billing-toggle">
            <span class="toggle-label active" id="label-monthly">Mensal</span>
            <label class="toggle-switch">
                <input type="checkbox" id="billingToggle">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label" id="label-annual">Anual <span class="annual-badge ms-1">-20%</span></span>
        </div>

        <div class="row g-4 justify-content-center">

            {{-- FREE --}}
            <div class="col-md-4">
                <div class="plan-card">
                    <div class="plan-name">Free</div>
                    <div class="plan-price">R$ 0 <span>/mês</span></div>
                    <p class="plan-desc">Para conhecer o sistema sem custo.</p>
                    <ul class="list-unstyled plan-features">
                        <li><i class="bi bi-check-circle-fill"></i> Até 50 produtos</li>
                        <li><i class="bi bi-check-circle-fill"></i> Até 100 clientes</li>
                        <li><i class="bi bi-check-circle-fill"></i> 2 usuários</li>
                        <li><i class="bi bi-check-circle-fill"></i> PDV, estoque e financeiro</li>
                        <li><i class="bi bi-check-circle-fill"></i> Relatórios completos</li>
                        <li class="disabled"><i class="bi bi-x-circle-fill"></i> Suporte prioritário</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mt-4">Começar grátis</a>
                </div>
            </div>

            {{-- PRO --}}
            <div class="col-md-4">
                <div class="plan-card featured">
                    <span class="plan-badge">Mais popular</span>
                    <div class="plan-name">Pro</div>
                    <div class="plan-offer-badge"><i class="bi bi-lightning-fill"></i> Oferta de Lançamento</div>

                    <div class="price-monthly">
                        <div class="plan-price-old">R$ 59,90/mês</div>
                        <div class="plan-price">R$ 39,90 <span>/mês</span></div>
                        <div class="plan-price-billed">cobrado mensalmente</div>
                    </div>
                    <div class="price-annual" style="display:none;">
                        <div class="plan-price-old">R$ 39,90/mês</div>
                        <div class="plan-price">R$ 31,92 <span>/mês</span></div>
                        <div class="plan-price-billed" style="color:#4ade80;"><i class="bi bi-check-circle-fill me-1"></i>R$ 383,04 cobrado anualmente</div>
                    </div>

                    <p class="plan-desc mt-2">Para negócios em crescimento.</p>
                    <ul class="list-unstyled plan-features">
                        <li><i class="bi bi-check-circle-fill"></i> Até 500 produtos</li>
                        <li><i class="bi bi-check-circle-fill"></i> Até 1.000 clientes</li>
                        <li><i class="bi bi-check-circle-fill"></i> 10 usuários</li>
                        <li><i class="bi bi-check-circle-fill"></i> Todos os módulos completos</li>
                        <li><i class="bi bi-check-circle-fill"></i> Relatórios + PDF/CSV</li>
                        <li><i class="bi bi-check-circle-fill"></i> Suporte por e-mail</li>
                    </ul>
                    <a id="btn-pro"
                       href="{{ route('register') }}?plan=pro_launch&billing=monthly"
                       class="btn btn-primary w-100 mt-4"
                       style="background:var(--sky); border:none; font-weight:700;">
                        Assinar Pro — R$ 39,90/mês
                    </a>
                </div>
            </div>

            {{-- BUSINESS --}}
            <div class="col-md-4">
                <div class="plan-card">
                    <div class="plan-name">Business</div>

                    <div class="price-monthly">
                        <div class="plan-price">R$ 119,90 <span>/mês</span></div>
                        <div class="plan-price-billed">cobrado mensalmente</div>
                    </div>
                    <div class="price-annual" style="display:none;">
                        <div class="plan-price-old">R$ 119,90/mês</div>
                        <div class="plan-price">R$ 95,92 <span>/mês</span></div>
                        <div class="plan-price-billed" style="color:#4ade80;"><i class="bi bi-check-circle-fill me-1"></i>R$ 1.151,04 cobrado anualmente</div>
                    </div>

                    <p class="plan-desc mt-2">Para empresas sem limites.</p>
                    <ul class="list-unstyled plan-features">
                        <li><i class="bi bi-check-circle-fill"></i> Produtos ilimitados</li>
                        <li><i class="bi bi-check-circle-fill"></i> Clientes ilimitados</li>
                        <li><i class="bi bi-check-circle-fill"></i> Usuários ilimitados</li>
                        <li><i class="bi bi-check-circle-fill"></i> Todos os recursos do Pro</li>
                        <li><i class="bi bi-check-circle-fill"></i> Multi-usuário avançado</li>
                        <li><i class="bi bi-check-circle-fill"></i> Suporte prioritário</li>
                    </ul>
                    <a id="btn-business"
                       href="{{ route('register') }}?plan=business&billing=monthly"
                       class="btn btn-outline-primary w-100 mt-4">
                        Assinar Business — R$ 119,90/mês
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="divider"></div>

{{-- FAQ --}}
<section class="faq" id="faq">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Dúvidas</span>
            <h2 class="section-title">Perguntas frequentes</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Preciso de cartão de crédito para testar?</button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Não. O trial de 14 dias é totalmente gratuito e não exige dados de pagamento. Você só escolhe um plano quando decidir continuar.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Posso cancelar a qualquer momento?</button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Sim. Não há fidelidade. Você pode cancelar quando quiser sem multa ou burocracia.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">O sistema funciona em celular?</button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Sim. O Invexa é 100% responsivo — funciona em computador, tablet e smartphone sem precisar instalar nada.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">Meus dados ficam seguros?</button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Cada empresa tem seus dados completamente isolados dos demais clientes. Utilizamos criptografia em trânsito (HTTPS) e backups automáticos diários.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">Consigo migrar meus dados de outro sistema?</button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Entre em contato com nosso suporte — auxiliamos na importação de produtos e clientes via planilha CSV durante o período de onboarding.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">O que acontece quando o trial termina?</button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">Após 14 dias você escolhe um plano para continuar. Se não assinar, o acesso fica limitado ao plano Free — seus dados não são apagados.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="divider"></div>

{{-- CTA FINAL --}}
<section class="cta-final">
    <div class="container">
        <div class="hero-badge mx-auto mb-3" style="width:fit-content;"><i class="bi bi-rocket-takeoff"></i> Comece agora — é grátis</div>
        <h2>Pronto para ter controle<br>total do seu negócio?</h2>
        <p class="mt-2 mb-4">14 dias de acesso completo. Sem cartão, sem compromisso.</p>
        <a href="{{ route('register') }}" class="btn-hero-primary">Criar minha conta grátis</a>
    </div>
</section>

{{-- FOOTER --}}
<footer class="lp-footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
                        <rect width="32" height="32" rx="7" fill="#0D1929"/>
                        <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                        <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
                    </svg>
                    <span class="fw-bold" style="color:rgba(226,232,240,.6);">INVEXA</span>
                </div>
                <div>Gestão de Estoque e Vendas</div>
            </div>
            <div class="col-md-4 text-md-center mb-3 mb-md-0">
                <a href="#how">Como funciona</a>
                <span class="mx-2" style="opacity:.3;">·</span>
                <a href="#features">Funcionalidades</a>
                <span class="mx-2" style="opacity:.3;">·</span>
                <a href="#plans">Planos</a>
                <span class="mx-2" style="opacity:.3;">·</span>
                <a href="#faq">FAQ</a>
            </div>
            <div class="col-md-4 text-md-end">
                <div>© {{ date('Y') }} Invexa · Desenvolvido por</div>
                <a href="https://www.instagram.com/castilho_digital/" target="_blank" rel="noopener">
                    <i class="bi bi-instagram me-1"></i>Castilho Soluções Digitais
                </a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const toggle       = document.getElementById('billingToggle');
    const labelMonthly = document.getElementById('label-monthly');
    const labelAnnual  = document.getElementById('label-annual');
    const btnPro       = document.getElementById('btn-pro');
    const btnBusiness  = document.getElementById('btn-business');
    const registerBase = "{{ route('register') }}";

    const proMonthlyUrl = registerBase + '?plan=pro_launch&billing=monthly';
    const proAnnualUrl  = registerBase + '?plan=pro_launch&billing=annual';
    const bizMonthlyUrl = registerBase + '?plan=business&billing=monthly';
    const bizAnnualUrl  = registerBase + '?plan=business&billing=annual';

    toggle.addEventListener('change', function () {
        const isAnnual = this.checked;
        labelMonthly.classList.toggle('active', !isAnnual);
        labelAnnual.classList.toggle('active', isAnnual);
        document.querySelectorAll('.price-monthly').forEach(el => el.style.display = isAnnual ? 'none' : '');
        document.querySelectorAll('.price-annual').forEach(el => el.style.display = isAnnual ? '' : 'none');
        if (isAnnual) {
            btnPro.href = proAnnualUrl;
            btnPro.textContent = 'Assinar Pro — R$ 383,04/ano';
            btnBusiness.href = bizAnnualUrl;
            btnBusiness.textContent = 'Assinar Business — R$ 1.151,04/ano';
        } else {
            btnPro.href = proMonthlyUrl;
            btnPro.textContent = 'Assinar Pro — R$ 39,90/mês';
            btnBusiness.href = bizMonthlyUrl;
            btnBusiness.textContent = 'Assinar Business — R$ 119,90/mês';
        }
    });
})();
</script>
</body>
</html>
