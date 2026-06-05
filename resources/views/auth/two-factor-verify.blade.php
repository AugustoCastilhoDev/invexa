<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA — Invexa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6fb; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.08); border-radius: 16px; }
    </style>
</head>
<body>
    <div class="card p-4" style="width:100%;max-width:420px;">
        <div class="text-center mb-4">
            <span style="font-size:2.5rem;">🔐</span>
            <h5 class="fw-bold mt-2 mb-0">Verificação em dois fatores</h5>
            <p class="text-muted small mt-1">Abra seu aplicativo autenticador e insira o código de 6 dígitos.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('two-factor.validate') }}">
            @csrf
            <div class="mb-3">
                <input
                    type="text"
                    name="code"
                    class="form-control form-control-lg text-center"
                    placeholder="000000"
                    maxlength="6"
                    autocomplete="one-time-code"
                    inputmode="numeric"
                    autofocus
                >
            </div>
            <button type="submit" class="btn btn-primary w-100">Verificar</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted small">← Voltar ao login</a>
        </div>
    </div>
</body>
</html>
