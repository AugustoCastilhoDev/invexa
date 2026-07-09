@extends('layouts.app')

@section('title', 'Autenticação em Dois Fatores')

@section('content')
<div class="container py-4" style="max-width:600px;">

    <h4 class="mb-1 fw-bold">🔐 Autenticação em Dois Fatores (2FA)</h4>
    <p class="text-muted mb-4">Adicione uma camada extra de segurança à sua conta usando um aplicativo autenticador.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($isEnabled)
        {{-- 2FA ATIVO --}}
        <div class="card border-success mb-4">
            <div class="card-body">
                <h6 class="text-success fw-bold mb-1">✅ 2FA está ativo</h6>
                <p class="text-muted small mb-0">Sua conta está protegida por autenticação em dois fatores.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold text-danger mb-3">Desativar 2FA</h6>
                <p class="text-muted small">Para desativar, confirme sua senha atual.</p>
                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Senha atual</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-danger">Desativar 2FA</button>
                </form>
            </div>
        </div>

    @else
        {{-- SETUP DO 2FA --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Passo 1 — Escaneie o QR Code</h6>
                <p class="text-muted small">Abra o <strong>Google Authenticator</strong> ou <strong>Authy</strong> e escaneie o código abaixo:</p>

                <div class="text-center my-3">
                    {!! QrCode::size(200)->generate($qrCodeUrl) !!}
                </div>

                <p class="text-muted small mt-3">Ou insira a chave manualmente:</p>
                <code class="d-block bg-light p-2 rounded text-center fs-6 letter-spacing-2">{{ $secret }}</code>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Passo 2 — Confirme o código</h6>
                <p class="text-muted small">Digite o código de 6 dígitos exibido no aplicativo:</p>

                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <div class="mb-3">
                        <input
                            type="text"
                            name="code"
                            class="form-control form-control-lg text-center @error('code') is-invalid @enderror"
                            placeholder="000000"
                            maxlength="6"
                            autocomplete="off"
                            autofocus
                        >
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ativar 2FA</button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
