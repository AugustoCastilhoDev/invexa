<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período de teste encerrado — Invexa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-upgrade { max-width: 680px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(15,23,42,.12); }
        .card-header-upgrade { background: linear-gradient(135deg, #1d4ed8, #2563eb); padding: 40px; text-align: center; color: #fff; }
        .card-header-upgrade h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
        .card-header-upgrade p { margin: 8px 0 0; opacity: .8; font-size: .95rem; }
        .plan-card { border: 2px solid #e2e8f0; border-radius: 12px; padding: 24px; transition: border-color .2s; }
        .plan-card.featured { border-color: #2563eb; position: relative; }
        .badge-popular { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #2563eb; color: #fff; font-size: .75rem; padding: 3px 14px; border-radius: 20px; font-weight: 600; }
        .plan-price { font-size: 2rem; font-weight: 800; color: #1e293b; }
        .plan-price span { font-size: 1rem; font-weight: 400; color: #64748b; }
        .feature-list li { font-size: .9rem; color: #475569; padding: 4px 0; }
    </style>
</head>
<body>
<div class="card-upgrade bg-white mx-3">
    <div class="card-header-upgrade">
        <div style="font-size:3rem;">&#128274;</div>
        <h1>Seu período gratuito encerrou</h1>
        <p>Escolha um plano para continuar usando o Invexa</p>
    </div>
    <div class="p-4 p-md-5">

        @if(session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        @if($company)
            <p class="text-center text-muted mb-4">
                Empresa: <strong>{{ $company->name }}</strong>
            </p>
        @endif

        <div class="row g-3 mb-4">
            {{-- Plano Pro --}}
            <div class="col-md-6">
                <div class="plan-card featured">
                    <span class="badge-popular">Mais popular</span>
                    <div class="text-center mb-3">
                        <div class="fw-bold fs-5 mb-1">Pro</div>
                        <div class="plan-price">R$ 79 <span>/mês</span></div>
                    </div>
                    <ul class="list-unstyled feature-list">
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Até 5 usuários</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>500 produtos</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>2.000 clientes</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>200 fornecedores</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Todos os relatórios</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i>Suporte prioritário</li>
                    </ul>
                    <a href="#" class="btn btn-primary w-100 mt-3">Assinar Pro</a>
                </div>
            </div>
            {{-- Plano Business --}}
            <div class="col-md-6">
                <div class="plan-card">
                    <div class="text-center mb-3">
                        <div class="fw-bold fs-5 mb-1">Business</div>
                        <div class="plan-price">R$ 149 <span>/mês</span></div>
                    </div>
                    <ul class="list-unstyled feature-list">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Usuários ilimitados</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Produtos ilimitados</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Clientes ilimitados</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Fornecedores ilimitados</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>API de integração</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Gerente de conta dedicado</li>
                    </ul>
                    <a href="#" class="btn btn-outline-primary w-100 mt-3">Assinar Business</a>
                </div>
            </div>
        </div>

        <div class="text-center">
            <p class="text-muted small mb-2">Dúvidas? Entre em contato:</p>
            <a href="mailto:suporte@castilhosolucoesdigitais.com" class="text-primary small">
                <i class="bi bi-envelope me-1"></i>suporte@castilhosolucoesdigitais.com
            </a>
        </div>

        <hr class="my-4">

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box-arrow-right me-1"></i>Sair da conta
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
