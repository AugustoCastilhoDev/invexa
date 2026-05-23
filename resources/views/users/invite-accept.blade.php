<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceitar Convite — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { min-height: 100vh; background: radial-gradient(ellipse at 20% 20%, rgba(14,165,233,.15) 0%, transparent 60%), radial-gradient(ellipse at 80% 80%, rgba(99,102,241,.12) 0%, transparent 60%), #08101d; display: flex; align-items: center; justify-content: center; }
        .card-invite { background: rgba(15,23,42,.92); border: 1px solid rgba(148,163,184,.14); border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 440px; box-shadow: 0 24px 64px rgba(0,0,0,.5); }
        .brand { font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, #38bdf8, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: .25rem; }
        label { color: #94a3b8; font-size: .85rem; margin-bottom: .35rem; }
        .form-control { background: rgba(30,41,59,.8); border: 1px solid rgba(148,163,184,.2); color: #e2e8f0; border-radius: 8px; }
        .form-control:focus { background: rgba(30,41,59,.95); border-color: rgba(99,102,241,.6); color: #f1f5f9; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
        .btn-submit { background: linear-gradient(135deg, #0ea5e9, #6366f1); border: none; border-radius: 8px; padding: .75rem; font-weight: 600; font-size: .95rem; color: #fff; width: 100%; transition: opacity .2s; }
        .btn-submit:hover { opacity: .9; color: #fff; }
        .info-pill { background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.25); border-radius: 8px; padding: .6rem 1rem; font-size: .83rem; color: #a5b4fc; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
<div class="card-invite">
    <div class="text-center mb-4">
        <div class="brand">{{ config('app.name') }}</div>
        <p class="text-secondary" style="font-size:.9rem;">Defina sua senha para ativar sua conta</p>
    </div>

    <div class="info-pill">
        <i class="bi bi-person-circle me-1"></i> <strong style="color:#c7d2fe;">{{ $user->name }}</strong>
        &nbsp;·&nbsp;
        <i class="bi bi-envelope me-1"></i> {{ $user->email }}
        &nbsp;·&nbsp;
        <i class="bi bi-shield-check me-1"></i> {{ ucfirst($user->role) }}
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2" style="font-size:.85rem;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('invite.accept', $token) }}">
        @csrf
        <div class="mb-3">
            <label>Nova senha</label>
            <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required autofocus>
        </div>
        <div class="mb-4">
            <label>Confirmar senha</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha" required>
        </div>
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle me-2"></i>Ativar minha conta
        </button>
    </form>
</div>
</body>
</html>
