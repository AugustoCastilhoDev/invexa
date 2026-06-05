@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
body {
    background: radial-gradient(circle at top left, rgba(96, 165, 250, 0.10), transparent 20%),
                radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.12), transparent 18%),
                #08101d;
    min-height: 100vh;
}
.login-card {
    background: rgba(15, 23, 42, .88);
    border: 1px solid rgba(148, 163, 184, .14);
    border-radius: 1rem;
}
.login-card .form-control {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(148, 163, 184, .2);
    color: #e2e8f0;
}
.login-card .form-control:focus {
    background: rgba(255,255,255,.09);
    border-color: rgba(96, 165, 250, .5);
    box-shadow: 0 0 0 .2rem rgba(96, 165, 250, .15);
    color: #e2e8f0;
}
.login-card .form-control::placeholder {
    color: rgba(148, 163, 184, .5);
}
.login-card .form-label {
    color: rgba(226, 232, 240, .8);
    font-size: .85rem;
}
.login-card .form-check-label {
    color: rgba(226, 232, 240, .7);
    font-size: .85rem;
}
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
    margin: 0 auto 1rem;
}
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="w-100" style="max-width: 420px; padding: 0 1rem;">

        {{-- Logo e nome --}}
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <h1 class="h3 fw-bold mb-1" style="
            background: linear-gradient(90deg, #8B5CF6, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: .08em;
            ">INVEXA</h1>
            <p class="mb-0" style="color: rgba(148, 163, 184, .7); font-size: .875rem;">
                Sistema de Estoque e Vendas
            </p>
        </div>

        {{-- Card de login --}}
        <div class="login-card shadow-lg p-4">

            @if ($errors->any())
                <div class="alert alert-danger border-0 mb-4" style="background: rgba(239,68,68,.12); color: #f87171;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="seu@email.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check mb-0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                        >
                        <label class="form-check-label" for="remember">
                            Lembrar-me
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                </button>
            </form>
        </div>

        {{-- Link para registro --}}
        <p class="text-center mt-3 mb-0" style="color: rgba(148,163,184,.6); font-size:.85rem;">
            Não tem uma conta?
            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold"
               style="color: #8B5CF6;">Criar conta</a>
        </p>

    </div>
</div>
@endsection