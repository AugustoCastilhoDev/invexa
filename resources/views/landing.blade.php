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

        /* VS PLANILHAS */
        .vs-section { background: var(--abyss); }
        .vs-table-wrap { overflow-x: auto; }
        .vs-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 14px; overflow: hidden; }
        .vs-table thead tr th { padding: 1rem 1.25rem; font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .vs-table thead .th-feature { background: rgba(13,25,41,.9); color: rgba(148,163,184,.6); width: 40%; }
        .vs-table thead .th-invexa  { background: rgba(14,165,233,.15); color: var(--electric); text-align: center; border-left: 1px solid rgba(14,165,233,.2); }
        .vs-table thead .th-sheet   { background: rgba(30,30,30,.7); color: rgba(148,163,184,.5); text-align: center; border-left: 1px solid rgba(255,255,255,.05); }
        .vs-table tbody tr { border-bottom: 1px solid rgba(255,255,255,.04); }
        .vs-table tbody tr:last-child { border-bottom: none; }
        .vs-table tbody tr:hover .td-feature,
        .vs-table tbody tr:hover .td-invexa,
        .vs-table tbody tr:hover .td-sheet { background-color: rgba(255,255,255,.025); }
        .vs-table .td-feature { padding: .9rem 1.25rem; font-size: .875rem; color: #e2e8f0; background: rgba(13,25,41,.7); }
        .vs-table .td-invexa  { padding: .9rem 1.25rem; text-align: center; background: rgba(14,165,233,.06); border-left: 1px solid rgba(14,165,233,.1); }
        .vs-table .td-sheet   { padding: .9rem 1.25rem; text-align: center; background: rgba(13,25,41,.5); border-left: 1px solid rgba(255,255,255,.04); }
        .vs-table .ic-yes  { color: #4ade80; font-size: 1.1rem; }
        .vs-table .ic-no   { color: #f87171; font-size: 1.1rem; }
        .vs-table .ic-par  { color: #fbbf24; font-size: .85rem; }
        /* CTA pós-tabela */
        .vs-cta-block {
            background: linear-gradient(135deg, rgba(14,165,233,.1), rgba(56,189,248,.05));
            border: 1px solid rgba(14,165,233,.2);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .vs-cta-block .vs-cta-text p { margin: 0; font-size: .88rem; color: rgba(226,232,240,.6); margin-top: .3rem; }
        .vs-cta-block .vs-cta-text strong { font-size: 1.05rem; color: #f1f5f9; font-weight: 700; }
        /* Cards de vantagens */
        .adv-card { background: rgba(13,25,41,.8); border: 1px solid rgba(14,165,233,.1); border-radius: 14px; padding: 24px; height: 100%; transition: border-color .25s, transform .2s; }
        .adv-card:hover { border-color: rgba(14,165,233,.3); transform: translateY(-3px); }
        .adv-icon { width: 44px; height: 44px; border-radius: 10px; background: rgba(14,165,233,.12); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--sky); margin-bottom: .85rem; flex-shrink: 0; }
        .adv-card h6 { color: #f1f5f9; font-weight: 700; font-size: .95rem; margin-bottom: .35rem; }
        .adv-card p  { font-size: .82rem; color: rgba(226,232,240,.6); margin: 0; line-height: 1.55; }

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

        /* NOTA TRIAL */
        .trial-notice {
            display: inline-flex; align-items: flex-start; gap: .65rem;
            background: rgba(251,146,60,.08);
            border: 1px solid rgba(251,146,60,.35);
            border-radius: 10px;
            padding: .85rem 1.25rem;
            font-size: .82rem;
            color: #FED7AA;
            max-width: 600px;
        }
        .trial-notice i { color: #FB923C; font-size: 1rem; flex-shrink: 0; margin-top: .05rem; }
        .trial-notice strong { color: #FDBA74; }

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
<!-- WhatsApp Suporte Invexa -->
<a href="https://wa.me/5532999669302?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20Invexa%20e%20gostaria%20de%20tirar%20uma%20d%C3%BAvida."
   target="_blank"
   rel="noopener noreferrer"
   title="Falar com suporte"
   style="position:fixed;bottom:24px;right:24px;z-index:9999;
          background:#25D366;border-radius:50%;width:56px;height:56px;
          display:flex;align-items:center;justify-content:center;
          box-shadow:0 4px 16px rgba(0,0,0,0.25);text-decoration:none;
          transition:transform 0.2s ease,box-shadow 0.2s ease;"
   onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)'"
   onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 16px rgba(0,0,0,0.25)'">
  <svg width="30" height="30" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.532 5.862L.057 23.55a.75.75 0 00.919.908l5.8-1.522A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.714 9.714 0 01-4.964-1.362l-.356-.211-3.644.956.973-3.533-.231-.365A9.714 9.714 0 012.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/>
  </svg>
</a>
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
                <li class="nav-item"><a class="nav-link" href="#vs">Vs. Planilhas</a></li>
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

{{-- SCREENSHOTS --}}
<section style="padding: 80px 0; background: rgba(8,13,26,.7);" id="screenshots">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Interface real</span>
            <h2 class="section-title">Veja o sistema em ação</h2>
            <p class="section-sub">Telas reais do Invexa — sem demonstrações fabricadas.</p>
        </div>

        {{-- Home --}}
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <span class="section-label">Página inicial</span>
                <h3 style="color:#f1f5f9;font-weight:700;font-size:1.4rem;margin-bottom:.75rem;">Tudo ao seu alcance ao fazer login</h3>
                <p style="color:rgba(226,232,240,.65);font-size:.95rem;line-height:1.7;">Assim que entra no sistema, você vê o resumo do dia: vendas, receita, contas vencendo — além de acesso rápido a todos os módulos.</p>
                <ul style="list-style:none;padding:0;margin-top:1rem;">
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Resumo financeiro do dia</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Acesso direto a todos os módulos</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Visão do seu plano e limites</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div style="border-radius:14px;overflow:hidden;border:1px solid rgba(14,165,233,.2);box-shadow:0 8px 40px rgba(0,0,0,.4);">
                    <img src="{{ asset('images/screenshots/home.jpeg') }}" alt="Página inicial do Invexa" class="img-fluid w-100" style="display:block;">
                </div>
            </div>
        </div>

        {{-- Dashboard --}}
        <div class="row align-items-center g-5 mb-5 flex-lg-row-reverse">
            <div class="col-lg-6">
                <span class="section-label">Dashboard analítico</span>
                <h3 style="color:#f1f5f9;font-weight:700;font-size:1.4rem;margin-bottom:.75rem;">Visão completa do desempenho do negócio</h3>
                <p style="color:rgba(226,232,240,.65);font-size:.95rem;line-height:1.7;">Acompanhe faturamento, evolução de vendas, fluxo de caixa, top produtos e ranking — tudo com filtros por período e em tempo real.</p>
                <ul style="list-style:none;padding:0;margin-top:1rem;">
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> KPIs de vendas e financeiro</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Gráficos de evolução e fluxo de caixa</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Ranking e top produtos vendidos</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div style="border-radius:14px;overflow:hidden;border:1px solid rgba(14,165,233,.2);box-shadow:0 8px 40px rgba(0,0,0,.4);">
                    <img src="{{ asset('images/screenshots/dash.jpeg') }}" alt="Dashboard do Invexa" class="img-fluid w-100" style="display:block;">
                </div>
            </div>
        </div>

        {{-- Vendas --}}
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <span class="section-label">Gestão de vendas</span>
                <h3 style="color:#f1f5f9;font-weight:700;font-size:1.4rem;margin-bottom:.75rem;">Controle total sobre cada pedido</h3>
                <p style="color:rgba(226,232,240,.65);font-size:.95rem;line-height:1.7;">Acompanhe todas as vendas com status em tempo real, filtros por cliente e período, e tenha a receita total sempre visível no topo.</p>
                <ul style="list-style:none;padding:0;margin-top:1rem;">
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Status por pedido (pendente, concluído)</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Filtros por cliente, data e status</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Receita total acumulada no período</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div style="border-radius:14px;overflow:hidden;border:1px solid rgba(14,165,233,.2);box-shadow:0 8px 40px rgba(0,0,0,.4);">
                    <img src="{{ asset('images/screenshots/vendas.jpeg') }}" alt="Gestão de vendas do Invexa" class="img-fluid w-100" style="display:block;">
                </div>
            </div>
        </div>

        {{-- Produtos --}}
        <div class="row align-items-center g-5 flex-lg-row-reverse">
            <div class="col-lg-6">
                <span class="section-label">Controle de estoque</span>
                <h3 style="color:#f1f5f9;font-weight:700;font-size:1.4rem;margin-bottom:.75rem;">Catálogo com margem e estoque em tempo real</h3>
                <p style="color:rgba(226,232,240,.65);font-size:.95rem;line-height:1.7;">Gerencie o catálogo com SKU, categoria, preço de venda, margem calculada automaticamente, quantidade e estoque mínimo configurável.</p>
                <ul style="list-style:none;padding:0;margin-top:1rem;">
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Margem de lucro calculada por produto</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Alerta de estoque mínimo</li>
                    <li style="color:rgba(226,232,240,.7);font-size:.88rem;padding:.35rem 0;display:flex;gap:.6rem;align-items:center;"><i class="bi bi-check-circle-fill" style="color:#0EA5E9;"></i> Importação em massa via CSV</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div style="border-radius:14px;overflow:hidden;border:1px solid rgba(14,165,233,.2);box-shadow:0 8px 40px rgba(0,0,0,.4);">
                    <img src="{{ asset('images/screenshots/produtos.jpeg') }}" alt="Controle de estoque do Invexa" class="img-fluid w-100" style="display:block;">
                </div>
            </div>
        </div>

    </div>
</section>

<div class="divider"></div>

{{-- INVEXA VS PLANILHAS --}}
<section class="vs-section" id="vs">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Por que trocar?</span>
            <h2 class="section-title">Invexa vs. Planilhas</h2>
            <p class="section-sub">Planilhas até funcionam no começo — mas chegam rápido no limite. Veja o que muda quando você usa um sistema feito para o seu negócio.</p>
        </div>

        {{-- Tabela comparativa --}}
        <div class="vs-table-wrap mb-4">
            <table class="vs-table">
                <thead>
                    <tr>
                        <th class="th-feature">Recurso</th>
                        <th class="th-invexa"><i class="bi bi-stars me-1"></i>Invexa</th>
                        <th class="th-sheet"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Planilha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="td-feature">Estoque atualizado automaticamente a cada venda</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><i class="bi bi-x-circle-fill ic-no"></i></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Alertas de estoque mínimo em tempo real</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><i class="bi bi-x-circle-fill ic-no"></i></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Contas a pagar e receber integradas</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><i class="bi bi-x-circle-fill ic-no"></i></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Relatórios e gráficos gerados automaticamente</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><span class="ic-par">Manual e trabalhoso</span></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Múltiplos usuários com permissões separadas</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><i class="bi bi-x-circle-fill ic-no"></i></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Histórico de vendas e movimentações auditável</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><span class="ic-par">Fácil de apagar por acidente</span></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Acesso pelo celular sem instalar nada</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><span class="ic-par">Limitado</span></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Dados seguros com backup automático</td>
                        <td class="td-invexa"><i class="bi bi-check-circle-fill ic-yes"></i></td>
                        <td class="td-sheet"><i class="bi bi-x-circle-fill ic-no"></i></td>
                    </tr>
                    <tr>
                        <td class="td-feature">Tempo para registrar uma venda</td>
                        <td class="td-invexa" style="color:#4ade80; font-weight:700; font-size:.82rem;">Segundos</td>
                        <td class="td-sheet" style="color:#f87171; font-size:.82rem;">Minutos (manual)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- CTA pós-tabela --}}
        <div class="vs-cta-block mb-5">
            <div class="vs-cta-text">
                <strong>Pronto para sair das planilhas de vez?</strong>
                <p>Crie sua conta grátis agora — sem cartão, sem burocracia. Em 3 minutos você já tem tudo funcionando.</p>
            </div>
            <a href="{{ route('register') }}" class="btn-hero-primary flex-shrink-0" style="white-space:nowrap;">
                <i class="bi bi-rocket-takeoff me-2"></i>Começar grátis por 14 dias
            </a>
        </div>

        {{-- Cards de vantagens --}}
        <div class="row g-3">
            <div class="col-sm-6 col-lg-4">
                <div class="adv-card">
                    <div class="adv-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                    <h6>Zero retrabalho</h6>
                    <p>Cada venda atualiza estoque, financeiro e relatórios automaticamente. Sem copiar e colar dados entre abas.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="adv-card">
                    <div class="adv-icon"><i class="bi bi-shield-check"></i></div>
                    <h6>Dados sempre seguros</h6>
                    <p>Backup automático diário na nuvem. Nada se perde por acidente, travamento ou exclusão acidental.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="adv-card">
                    <div class="adv-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <h6>Decisões baseadas em dados</h6>
                    <p>Dashboard com gráficos em tempo real. Saiba o que vende mais, qual produto dá mais lucro e quando pagar suas contas.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="adv-card">
                    <div class="adv-icon"><i class="bi bi-people-fill"></i></div>
                    <h6>Toda a equipe na mesma página</h6>
                    <p>Vendedor registra a venda, gerente acompanha em tempo real. Cada um com seu acesso e permissões.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="adv-card">
                    <div class="adv-icon"><i class="bi bi-alarm-fill"></i></div>
                    <h6>Nunca mais perca um vencimento</h6>
                    <p>Alertas de contas vencendo, estoque crítico e inadimplência — tudo visível logo na tela inicial.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="adv-card">
                    <div class="adv-icon"><i class="bi bi-clock-history"></i></div>
                    <h6>Horas devolvidas por semana</h6>
                    <p>Tudo o que antes tomava horas em planilhas fica pronto em minutos. Você foca no negócio, o Invexa cuida dos números.</p>
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
                        Começar trial grátis — Pro
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
                        Começar trial grátis — Business
                    </a>
                </div>
            </div>

        </div>

        {{-- Nota sobre trial --}}
        <div class="d-flex justify-content-center mt-4">
            <div class="trial-notice">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>
                    <strong>Atenção:</strong> após os 14 dias de trial, o acesso ao sistema é <strong>bloqueado</strong> até a assinatura de um plano.
                    Seus dados (produtos, clientes, vendas) são preservados e você retoma exatamente de onde parou.
                </span>
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
                            <div class="accordion-body">Não. O trial de 14 dias é totalmente gratuito e não exige dados de pagamento. Você só escolhe um um plano quando decidir continuar.</div>
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
                            <div class="accordion-body">Após os 14 dias de trial, o acesso ao sistema é bloqueado até que um plano seja assinado. Todos os seus dados são preservados — produtos, clientes, vendas e histórico ficam intactos. Basta assinar um plano para retomar de onde parou.</div>
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
                <a href="#vs">Vs. Planilhas</a>
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
            btnPro.textContent = 'Começar trial grátis — Pro Anual';
            btnBusiness.href = bizAnnualUrl;
            btnBusiness.textContent = 'Começar trial grátis — Business Anual';
        } else {
            btnPro.href = proMonthlyUrl;
            btnPro.textContent = 'Começar trial grátis — Pro';
            btnBusiness.href = bizMonthlyUrl;
            btnBusiness.textContent = 'Começar trial grátis — Business';
        }
    });
})();
</script>
</body>
</html>
