<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha seu plano — Invexa</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%23080D1A'/><path d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/><circle cx='24' cy='10' r='2.2' fill='%2338BDF8'/></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --brand-abyss:#080D1A; --brand-sky:#0EA5E9; --brand-sky2:#38BDF8; }
        body {
            background: radial-gradient(circle at top left,rgba(14,165,233,.08),transparent 24%),
                        radial-gradient(circle at bottom right,rgba(14,165,233,.05),transparent 20%),
                        var(--brand-abyss);
            min-height:100vh; color:#e2e8f0; font-family:'Segoe UI',system-ui,sans-serif;
        }
        .upgrade-wrap{max-width:960px;}
        .trial-banner{background:linear-gradient(90deg,rgba(245,158,11,.12),rgba(245,158,11,.06));border:1px solid rgba(245,158,11,.35);border-radius:12px;padding:14px 20px;}
        .trial-expired-banner{background:linear-gradient(90deg,rgba(239,68,68,.12),rgba(239,68,68,.06));border:1px solid rgba(239,68,68,.35);border-radius:12px;padding:14px 20px;}
        .billing-toggle{background:rgba(255,255,255,.06);border-radius:30px;padding:4px;display:inline-flex;gap:2px;}
        .billing-toggle button{border:none;background:transparent;color:rgba(226,232,240,.6);border-radius:26px;padding:6px 20px;font-size:.88rem;font-weight:500;transition:all .2s;cursor:pointer;}
        .billing-toggle button.active{background:var(--brand-sky);color:#fff;}
        .annual-badge{background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.3);font-size:.7rem;font-weight:700;padding:2px 8px;border-radius:20px;}
        .plan-card{background:rgba(13,25,41,.9);border:1px solid rgba(14,165,233,.12);border-radius:16px;padding:28px 24px;transition:border-color .25s,transform .2s;height:100%;display:flex;flex-direction:column;}
        .plan-card:hover{border-color:rgba(14,165,233,.35);transform:translateY(-2px);}
        .plan-card.featured{border-color:var(--brand-sky);box-shadow:0 0 32px rgba(14,165,233,.15);}
        .plan-card.current-plan{border-color:rgba(34,197,94,.5);}
        .badge-popular{background:#f59e0b;color:#1a1a1a;font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.03em;}
        .badge-current{background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.3);font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;}
        .plan-label{font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;font-weight:600;margin-bottom:14px;}
        .plan-price{font-size:2.4rem;font-weight:800;color:#fff;line-height:1.1;}
        .plan-price small{font-size:.95rem;font-weight:400;color:rgba(226,232,240,.45);}
        .plan-price .old-price{font-size:1rem;color:rgba(226,232,240,.3);text-decoration:line-through;margin-left:8px;}
        .plan-desc{font-size:.84rem;margin-bottom:20px;}
        .feature-list li{font-size:.86rem;color:rgba(226,232,240,.8);padding:5px 0;display:flex;align-items:center;gap:8px;}
        .feature-list .bi-check-circle-fill{color:#4ade80;}
        .feature-list .bi-check-circle-fill.sky{color:var(--brand-sky2);}
        .feature-list .bi-x-circle{color:rgba(226,232,240,.2);}
        .guarantee-strip{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:16px 24px;}
        .guarantee-item{display:flex;align-items:center;gap:8px;font-size:.82rem;color:rgba(226,232,240,.6);}
        .guarantee-item i{font-size:1rem;color:var(--brand-sky2);}
        .logo-wrap svg{filter:drop-shadow(0 0 6px rgba(14,165,233,.4));}
    </style>
</head>
<body>
<div class="upgrade-wrap mx-auto px-3 py-5">

    <div class="text-center mb-4">
        <div class="logo-wrap mb-3">
            <svg width="44" height="44" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#080D1A"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
        </div>
        <h1 class="fw-bold fs-2 text-white mb-1">Escolha seu plano</h1>
        <p style="color:rgba(226,232,240,.55);font-size:.95rem;">Acesso completo a todos os recursos. Cancele quando quiser.</p>
        @if($company)
            <span class="badge mt-1" style="background:rgba(14,165,233,.1);color:#38BDF8;border:1px solid rgba(14,165,233,.22);font-size:.8rem;">
                <i class="bi bi-building me-1"></i>{{ $company->name }}
            </span>
        @endif
    </div>

    @if(!is_null($trialDaysLeft) && $trialDaysLeft > 0)
        <div class="trial-banner d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-clock-history fs-4" style="color:#f59e0b;"></i>
            <div>
                <div class="fw-semibold" style="color:#fbbf24;">Seu trial expira em {{ $trialDaysLeft }} {{ $trialDaysLeft == 1 ? 'dia' : 'dias' }}</div>
                <div style="font-size:.84rem;color:rgba(226,232,240,.55);">Assine agora e mantenha todos os seus dados sem interrupção.</div>
            </div>
        </div>
    @elseif(session('error'))
        <div class="trial-expired-banner d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-circle-fill fs-4" style="color:#f87171;"></i>
            <div>
                <div class="fw-semibold" style="color:#fca5a5;">{{ session('error') }}</div>
                <div style="font-size:.84rem;color:rgba(226,232,240,.55);">Escolha um plano abaixo para recuperar o acesso imediatamente.</div>
            </div>
        </div>
    @endif

    <div class="text-center mb-4">
        <div class="billing-toggle">
            <button class="active" id="btn-monthly" onclick="setBilling('monthly')">Mensal</button>
            <button id="btn-annual" onclick="setBilling('annual')">
                Anual &nbsp;<span class="annual-badge">-20%</span>
            </button>
        </div>
    </div>

    <div class="row g-4 justify-content-center mb-4">

        {{-- FREE --}}
        <div class="col-md-4">
            <div class="plan-card {{ $currentPlan === 'free' && !$hasActiveSubscription ? 'current-plan' : '' }}">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="plan-label" style="color:rgba(226,232,240,.45);">Free</span>
                        @if($currentPlan === 'free' && !$hasActiveSubscription)
                            <span class="badge-current">Plano atual</span>
                        @endif
                    </div>
                    <div class="plan-price mb-1">R$ 0 <small>/mês</small></div>
                    <p class="plan-desc" style="color:rgba(226,232,240,.45);">Para começar sem custo.</p>
                    <ul class="list-unstyled feature-list mb-4">
                        <li><i class="bi bi-check-circle-fill"></i>Até 50 produtos</li>
                        <li><i class="bi bi-check-circle-fill"></i>Até 100 clientes</li>
                        <li><i class="bi bi-check-circle-fill"></i>2 usuários</li>
                        <li><i class="bi bi-check-circle-fill"></i>Relatórios básicos</li>
                        <li><i class="bi bi-x-circle"></i>PDV avançado</li>
                        <li><i class="bi bi-x-circle"></i>Exportação PDF/CSV</li>
                        <li><i class="bi bi-x-circle"></i>API REST</li>
                        <li><i class="bi bi-x-circle"></i>Suporte prioritário</li>
                    </ul>
                </div>
                <div class="mt-auto">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">Continuar grátis</a>
                </div>
            </div>
        </div>

        {{-- PRO --}}
        <div class="col-md-4">
            <div class="plan-card featured {{ in_array($currentPlan, ['pro','pro_launch']) && $hasActiveSubscription ? 'current-plan' : '' }}">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="plan-label" style="color:#38BDF8;">Pro</span>
                        <div class="d-flex gap-2">
                            @if(in_array($currentPlan, ['pro','pro_launch']) && $hasActiveSubscription)
                                <span class="badge-current">Plano atual</span>
                            @else
                                <span class="badge-popular">&#128293; Mais popular</span>
                            @endif
                        </div>
                    </div>
                    <div id="pro-price-monthly">
                        <div class="plan-price mb-0">R$ 39,90 <span class="old-price">R$ 59,90</span> <small>/mês</small></div>
                        <p class="plan-desc" style="color:rgba(56,189,248,.8);font-size:.8rem;">Oferta de lançamento</p>
                    </div>
                    <div id="pro-price-annual" style="display:none;">
                        <div class="plan-price mb-0">R$ 31,92 <small>/mês</small></div>
                        <p class="plan-desc" style="color:#4ade80;font-size:.8rem;">R$ 383,04/ano &mdash; economize R$ 96</p>
                    </div>
                    <ul class="list-unstyled feature-list mb-4">
                        <li><i class="bi bi-check-circle-fill sky"></i>Até 500 produtos</li>
                        <li><i class="bi bi-check-circle-fill sky"></i>Até 1.000 clientes</li>
                        <li><i class="bi bi-check-circle-fill sky"></i>10 usuários</li>
                        <li><i class="bi bi-check-circle-fill sky"></i>Todos os relatórios + PDF/CSV</li>
                        <li><i class="bi bi-check-circle-fill sky"></i>PDV completo</li>
                        <li><i class="bi bi-check-circle-fill sky"></i>Contas a pagar/receber</li>
                        <li><i class="bi bi-x-circle"></i>API REST</li>
                        <li><i class="bi bi-check-circle-fill sky"></i>Suporte por e-mail</li>
                    </ul>
                </div>
                <div class="mt-auto">
                    @if(in_array($currentPlan, ['pro','pro_launch']) && $hasActiveSubscription)
                        <a href="{{ route('subscription.billing-portal') }}" class="btn w-100 fw-semibold" style="background:rgba(14,165,233,.15);color:#38BDF8;border:1px solid rgba(14,165,233,.3);">
                            <i class="bi bi-gear me-1"></i>Gerenciar assinatura
                        </a>
                    @else
                        <form action="{{ route('subscription.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="pro_launch">
                            <input type="hidden" name="billing" id="pro-billing" value="monthly">
                            <button type="submit" id="pro-btn" class="btn w-100 fw-semibold" style="background:#0EA5E9;color:#fff;">
                                Assinar Pro &mdash; R$ 39,90/mês
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- BUSINESS --}}
        <div class="col-md-4">
            <div class="plan-card {{ $currentPlan === 'business' && $hasActiveSubscription ? 'current-plan' : '' }}">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="plan-label" style="color:rgba(226,232,240,.45);">Business</span>
                        @if($currentPlan === 'business' && $hasActiveSubscription)
                            <span class="badge-current">Plano atual</span>
                        @endif
                    </div>
                    <div id="biz-price-monthly">
                        <div class="plan-price mb-1">R$ 119,90 <small>/mês</small></div>
                        <p class="plan-desc" style="color:rgba(226,232,240,.45);">Para empresas sem limites.</p>
                    </div>
                    <div id="biz-price-annual" style="display:none;">
                        <div class="plan-price mb-0">R$ 95,92 <small>/mês</small></div>
                        <p class="plan-desc" style="color:#4ade80;font-size:.8rem;">R$ 1.151,04/ano &mdash; economize R$ 288</p>
                    </div>
                    <ul class="list-unstyled feature-list mb-4">
                        <li><i class="bi bi-check-circle-fill"></i>Produtos ilimitados</li>
                        <li><i class="bi bi-check-circle-fill"></i>Clientes ilimitados</li>
                        <li><i class="bi bi-check-circle-fill"></i>Usuários ilimitados</li>
                        <li><i class="bi bi-check-circle-fill"></i>Todos os recursos Pro</li>
                        <li><i class="bi bi-check-circle-fill"></i>API REST completa</li>
                        <li><i class="bi bi-check-circle-fill"></i>Webhooks (em breve)</li>
                        <li><i class="bi bi-check-circle-fill"></i>Suporte prioritário</li>
                        <li><i class="bi bi-check-circle-fill"></i>Onboarding assistido</li>
                    </ul>
                </div>
                <div class="mt-auto">
                    @if($currentPlan === 'business' && $hasActiveSubscription)
                        <a href="{{ route('subscription.billing-portal') }}" class="btn btn-outline-light w-100 fw-semibold">
                            <i class="bi bi-gear me-1"></i>Gerenciar assinatura
                        </a>
                    @else
                        <form action="{{ route('subscription.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="business">
                            <input type="hidden" name="billing" id="biz-billing" value="monthly">
                            <button type="submit" id="biz-btn" class="btn btn-outline-light w-100 fw-semibold">
                                Assinar Business &mdash; R$ 119,90/mês
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <div class="guarantee-strip mb-4">
        <div class="row g-3 justify-content-center text-center">
            <div class="col-6 col-md-3"><div class="guarantee-item justify-content-center"><i class="bi bi-shield-check"></i><span>14 dias de garantia</span></div></div>
            <div class="col-6 col-md-3"><div class="guarantee-item justify-content-center"><i class="bi bi-x-circle"></i><span>Sem contrato</span></div></div>
            <div class="col-6 col-md-3"><div class="guarantee-item justify-content-center"><i class="bi bi-lock"></i><span>Pagamento seguro</span></div></div>
            <div class="col-6 col-md-3"><div class="guarantee-item justify-content-center"><i class="bi bi-arrow-counterclockwise"></i><span>Cancele quando quiser</span></div></div>
        </div>
    </div>

    <div class="text-center" style="color:rgba(226,232,240,.35);font-size:.82rem;">
        Dúvidas? <a href="mailto:suporte@castilhosolucoesdigitais.com" style="color:#38BDF8;">suporte@castilhosolucoesdigitais.com</a>
        <span class="mx-2">·</span>
        <a href="{{ route('dashboard') }}" style="color:rgba(226,232,240,.35);">Voltar ao sistema</a>
        <span class="mx-2">·</span>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link btn-sm p-0" style="color:rgba(226,232,240,.35);font-size:.82rem;">Sair da conta</button>
        </form>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setBilling(type) {
    const isAnnual = type === 'annual';
    document.getElementById('btn-monthly').classList.toggle('active', !isAnnual);
    document.getElementById('btn-annual').classList.toggle('active', isAnnual);

    // preços Pro
    document.getElementById('pro-price-monthly').style.display = isAnnual ? 'none' : '';
    document.getElementById('pro-price-annual').style.display  = isAnnual ? '' : 'none';

    // preços Business
    document.getElementById('biz-price-monthly').style.display = isAnnual ? 'none' : '';
    document.getElementById('biz-price-annual').style.display  = isAnnual ? '' : 'none';

    // atualiza campos hidden + texto dos botões
    const proB = document.getElementById('pro-billing');
    const bizB = document.getElementById('biz-billing');
    const proBtn = document.getElementById('pro-btn');
    const bizBtn = document.getElementById('biz-btn');

    if (proB) proB.value = type;
    if (bizB) bizB.value = type;

    if (proBtn) proBtn.textContent = isAnnual
        ? 'Assinar Pro — R$ 31,92/mês'
        : 'Assinar Pro — R$ 39,90/mês';

    if (bizBtn) bizBtn.textContent = isAnnual
        ? 'Assinar Business — R$ 95,92/mês'
        : 'Assinar Business — R$ 119,90/mês';
}
</script>
</body>
</html>
