<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrar - Invexa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: radial-gradient(circle at top left, rgba(96, 165, 250, 0.10), transparent 20%),
                        radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.12), transparent 18%),
                        #08101d;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-container { width: 100%; max-width: 480px; }
        .card-auth {
            background: rgba(15,23,42,.95);
            border: 1px solid rgba(148,163,184,.14);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover { background: linear-gradient(135deg, #15803d, #16a34a); }
        .form-control, .form-select {
            background: rgba(30,41,59,.8) !important;
            border: 1px solid rgba(148,163,184,.2) !important;
            color: #e2e8f0 !important;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(30,41,59,.9) !important;
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 .2rem rgba(96,165,250,.25) !important;
            color: #e2e8f0 !important;
        }
        .form-control::placeholder { color: rgba(226,232,240,.5); }
        .form-label { color: #cbd5e1; font-weight: 500; }
        .invalid-feedback { color: #fca5a5; }
        .text-soft { color: rgba(226,232,240,.7); }

        /* ── Brand: idêntico ao login ── */
        .brand-icon {
            width: 3.5rem;
            height: 3.5rem;
            background: linear-gradient(135deg, #7C3AED, #3B82F6);
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            margin: 0 auto .75rem;
        }
        .brand-name {
            font-weight: 700;
            font-size: 1.6rem;
            background: linear-gradient(90deg, #8B5CF6, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        a { color: #8B5CF6; text-decoration: none; }
        a:hover { color: #a78bfa; }
        .section-divider {
            border-color: rgba(148,163,184,.15);
            margin: 1.25rem 0;
        }
        .section-label {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(148,163,184,.6);
            margin-bottom: .75rem;
        }
    </style>
</head>
<body>
    <div class="register-container">

        {{-- Logo e nome — idêntico ao login --}}
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            {{-- DEPOIS --}}
            <div class="brand-name" style="letter-spacing: .08em;">INVEXA</div>
            <p class="text-soft small mt-1 mb-0">Sistema profissional de gestão de estoque e vendas</p>
        </div>

        <div class="card card-auth">
            <div class="card-body p-4">
                <h4 class="mb-4 text-white text-center">Criar Conta</h4>

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
                        <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    {{-- Dados da empresa --}}
                    <p class="section-label">
                        <i class="bi bi-building me-1"></i> Dados da Empresa
                    </p>

                    <div class="mb-3">
                        <label for="company_name" class="form-label">
                            <i class="bi bi-building"></i> Nome da Empresa
                        </label>
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

                    {{-- Dados do usuário --}}
                    <p class="section-label">
                        <i class="bi bi-person me-1"></i> Dados do Administrador
                    </p>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i> Nome Completo
                        </label>
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
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> E-mail
                        </label>
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
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Senha
                        </label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password"
                               placeholder="Mínimo 8 caracteres" required>
                        <small class="text-soft" style="font-size:.78rem;">
                            Use letras e números para criar uma senha segura.
                        </small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock-check"></i> Confirmar Senha
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="Repita sua senha" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-person-plus me-1"></i> Criar Conta
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-soft small mb-0">
                        Já tem uma conta?
                        <a href="{{ route('login') }}" class="fw-semibold">Faça login aqui</a>
                    </p>
                </div>
            </div>
        </div>

        <p class="text-center text-soft small mt-4 mb-0">
            © {{ date('Y') }} Invexa ·
            <a href="https://www.instagram.com/castilho_digital/" target="_blank">
                Castilho Soluções Digitais
            </a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>