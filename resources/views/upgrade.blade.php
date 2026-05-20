<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha seu plano — Invexa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand-abyss: #080D1A;
            --brand-sky:   #0EA5E9;
        }
        body {
            background: radial-gradient(circle at top left, rgba(14,165,233,.07), transparent 22%),
                        var(--brand-abyss);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
        }
        .upgrade-wrap  { max-width: 820px; width: 100%; }
        .plan-card     { background: rgba(13,25,41,.88); border: 1px solid rgba(14,165,233,.15); border-radius: 14px; padding: 28px; transition: border-color .2s; }
        .plan-card.featured { border-color: #0EA5E9; box-shadow: 0 0 24px rgba(14,165,233,.18); }
        .badge-popular { background: #f59e0b; color: #1a1a1a; font-size: .72rem; font-weight: 700; padding: .25rem .75rem; border-radius: 20px; letter-spacing: .03em; }
        .plan-price    { font-size: 2.2rem; font-weight: 800; color: #fff; }
        .plan-price small { font-size: 1rem; font-weight: 400; color: rgba(226,232,240,.55); }
        .feature-list li { font-size: .88rem; color: rgba(226,232,240,.8); padding: 4px 0; }
        .logo-wrap svg  { filter: drop-shadow(0 0 6px rgba(14,165,233,.4)); }
    </style>
</head>
<body>
<div class="upgrade-wrap mx-auto px-3 py-5">

    {{-- Logo + Header --}}
    <div class="text-center mb-5">
        <div class="logo-wrap mb-3">
            <svg width="42" height="42" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#080D1A"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
        </div>
        <h1 class="fw-bold fs-3 text-white">Escolha seu plano</h1>
        <p style="color:rgba(226,232,240,.6);">Continue usando o Invexa com todos os recursos.</p>

        @if($company)
            <span class="badge" style="background:rgba(14,165,233,.12); color:#38BDF8; border:1px solid rgba(14,165,233,.25); font-size:.8rem;">
                {{ $company->name }}
            </span>
        @endif
    </div>

    @if(session('error'))
        <div class="alert alert-warning text-center mb-4">{{ session('error') }}</div>
    @endif

    <div class="row g-4 justify-content-center">

        {{-- FREE --}}
        <div class="col-md-4">
            <div class="plan-card h-100 d-flex flex-column">
                <div class="text-uppercase fw-semibold mb-3" style="font-size:.72rem; letter-spacing:.1em; color:rgba(226,232,240,.5);">Free</div>
                <div class="plan-price mb-1">R$ 0 <small>/mês</small></div>
                <p class="mb-3" style="font-size:.85rem; color:rgba(226,232,240,.55);">Para começar sem custo.</p>
                <ul class="list-unstyled feature-list flex-grow-1">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Até 50 produtos</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Até 100 clientes</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>2 usuários</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Relatórios básicos</li>
                    <li><i class="bi bi-x-circle me-2" style="color:rgba(226,232,240,.3);"></i>PDV avançado</li>
                    <li><i class="bi bi-x-circle me-2" style="color:rgba(226,232,240,.3);"></i>Suporte prioritário</li>
                </ul>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mt-3">Continuar grátis</a>
            </div>
        </div>

        {{-- PRO --}}
        <div class="col-md-4">
            <div class="plan-card featured h-100 d-flex flex-column position-relative">
                <div class="text-center mb-3">
                    <span class="badge-popular">&#128293; Mais popular</span>
                </div>
                <div class="text-uppercase fw-semibold mb-3" style="font-size:.72rem; letter-spacing:.1em; color:#38BDF8;">Pro</div>
                <div class="plan-price mb-1">
                    R$ 39,90
                    <span class="text-decoration-line-through ms-2" style="font-size:1rem; color:rgba(226,232,240,.35);">R$ 59,90</span>
                    <small>/mês</small>
                </div>
                <p class="mb-3" style="font-size:.85rem; color:rgba(56,189,248,.8);">Oferta de lançamento</p>
                <ul class="list-unstyled feature-list flex-grow-1">
                    <li><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Até 500 produtos</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Até 1.000 clientes</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>10 usuários</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Todos os relatórios + PDF/CSV</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>PDV completo</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Suporte por e-mail</li>
                </ul>
                <form action="{{ route('subscription.checkout') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="plan" value="pro_launch">
                    <button type="submit" class="btn w-100 fw-semibold" style="background:#0EA5E9; color:#fff;">
                        Assinar Pro — R$ 39,90/mês
                    </button>
                </form>
            </div>
        </div>

        {{-- BUSINESS --}}
        <div class="col-md-4">
            <div class="plan-card h-100 d-flex flex-column">
                <div class="text-uppercase fw-semibold mb-3" style="font-size:.72rem; letter-spacing:.1em; color:rgba(226,232,240,.5);">Business</div>
                <div class="plan-price mb-1">R$ 119,90 <small>/mês</small></div>
                <p class="mb-3" style="font-size:.85rem; color:rgba(226,232,240,.55);">Para empresas sem limites.</p>
                <ul class="list-unstyled feature-list flex-grow-1">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Produtos ilimitados</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Clientes ilimitados</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Usuários ilimitados</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Todos os recursos Pro</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>API REST (em breve)</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Suporte prioritário</li>
                </ul>
                <form action="{{ route('subscription.checkout') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="plan" value="business">
                    <button type="submit" class="btn btn-outline-light w-100 fw-semibold">
                        Assinar Business — R$ 119,90/mês
                    </button>
                </form>
            </div>
        </div>

    </div>{{-- /row --}}

    <div class="text-center mt-5" style="color:rgba(226,232,240,.4); font-size:.82rem;">
        Dúvidas? <a href="mailto:suporte@castilhosolucoesdigitais.com" style="color:#38BDF8;">suporte@castilhosolucoesdigitais.com</a>
        <span class="mx-2">·</span>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link btn-sm p-0" style="color:rgba(226,232,240,.4); font-size:.82rem;">Sair da conta</button>
        </form>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
