<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar Conta — Invexa</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23080D1A'/%3E%3Cpath d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/%3E%3Ccircle cx='24' cy='10' r='2.2' fill='%2338BDF8'/%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --abyss: #080D1A;
            --navy:  #0D1929;
            --sky:   #0EA5E9;
            --elec:  #38BDF8;
            --ice:   #F0F9FF;
        }
        body {
            background: var(--abyss);
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(14,165,233,.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 80%, rgba(56,189,248,.05) 0%, transparent 60%);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }
        .register-container { width: 100%; max-width: 480px; }
        .card-auth {
            background: rgba(13,25,41,.95);
            border: 1px solid rgba(14,165,233,.15);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4), 0 0 0 1px rgba(14,165,233,.05);
        }
        .btn-sky {
            background: linear-gradient(135deg, var(--sky), var(--elec));
            border: none;
            color: #fff;
            font-weight: 700;
            letter-spacing: .02em;
            transition: opacity .2s;
        }
        .btn-sky:hover { opacity: .88; color: #fff; }
        .form-control, .form-select {
            background: rgba(8,13,26,.7) !important;
            border: 1px solid rgba(14,165,233,.2) !important;
            color: #e2e8f0 !important;
            border-radius: 8px !important;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(8,13,26,.9) !important;
            border-color: var(--sky) !important;
            box-shadow: 0 0 0 .2rem rgba(14,165,233,.2) !important;
            color: #e2e8f0 !important;
        }
        .form-control::placeholder { color: rgba(226,232,240,.35); }
        .form-label { color: #94a3b8; font-weight: 500; font-size: .875rem; }
        .invalid-feedback { color: #fca5a5; }
        .text-soft { color: rgba(226,232,240,.55); }
        a { color: var(--elec); text-decoration: none; }
        a:hover { color: var(--ice); }
        .section-divider { border-color: rgba(14,165,233,.1); margin: 1.25rem 0; }
        .section-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--sky);
            margin-bottom: .75rem;
        }
    </style>
</head>
<body>
    <div class="register-container">

        {{-- Logo Invexa --}}
        <div class="text-center mb-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <svg width="36" height="36" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="7" fill="#0D1929"/>
                    <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                    <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
                </svg>
                <span style="font-size:1.6rem; font-weight:800; color:var(--ice); letter-spacing:-.02em;">Invexa</span>
            </div>
            <p class="text-soft small mb-0">Gestão inteligente de estoque e vendas</p>
        </div>

        <div class="card card-auth">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-center" style="color:var(--ice);">Criar sua conta</h5>

                @if ($errors->any())
                    <div class="alert alert-dismissible fade show mb-4"
                         style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
                                color:#f87171;border-radius:.5rem;">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <ul class="mb-0 mt-1 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <p class="section-label"><i class="bi bi-building me-1"></i>Dados da Empresa</p>

                    <div class="mb-3">
                        <label for="company_name" class="form-label"><i class="bi bi-building me-1"></i>Nome da Empresa</label>
                        <input type="text"
                               class="form-control @error('company_name') is-invalid @enderror"
                               id="company_name" name="company_name"
                               placeholder="Razão social ou nome fantasia"
                               value="{{ old('company_name') }}" required autofocus>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-person me-1"></i>Dados do Administrador</p>

                    <div class="mb-3">
                        <label for="name" class="form-label"><i class="bi bi-person me-1"></i>Nome Completo</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name"
                               placeholder="Seu nome completo"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="bi bi-envelope me-1"></i>E-mail</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email"
                               placeholder="seu@email.com"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="bi bi-lock me-1"></i>Senha</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password"
                               placeholder="Mínimo 8 caracteres" required>
                        <small class="text-soft" style="font-size:.75rem;">Use letras e números para criar uma senha segura.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label"><i class="bi bi-lock-check me-1"></i>Confirmar Senha</label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="Repita sua senha" required>
                    </div>

                    <button type="submit" class="btn btn-sky w-100 mb-3 py-2">
                        <i class="bi bi-person-plus me-1"></i> Criar Conta
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-soft small mb-0">
                        Já tem uma conta?
                        <a href="{{ route('login') }}">Faça login aqui</a>
                    </p>
                </div>
            </div>
        </div>

        <p class="text-center text-soft small mt-4 mb-0">
            &copy; {{ date('Y') }} Invexa &middot;
            <a href="https://www.instagram.com/castilho_digital/" target="_blank">Castilho Soluções Digitais</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
