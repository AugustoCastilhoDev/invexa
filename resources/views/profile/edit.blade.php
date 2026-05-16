@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <div class="card dashboard-card card-dark-bg shadow-sm border-0">
            <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-white">
                        <i class="bi bi-person-circle me-2 text-primary"></i>Meu Perfil
                    </h4>
                    <p class="text-soft mb-0">Edite seus dados pessoais e de acesso.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <div class="card-body p-4">

                {{-- Avatar + info --}}
                <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded" style="background: rgba(255,255,255,.04); border: 1px solid rgba(148,163,184,.1);">
                    <div style="
                        width: 3.5rem; height: 3.5rem; border-radius: 50%;
                        background: linear-gradient(135deg, #4f46e5, #7c3aed);
                        display: flex; align-items: center; justify-content: center;
                        font-size: 1.4rem; font-weight: 700; color: #fff; flex-shrink: 0;
                    ">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold text-white">{{ $user->name }}</div>
                        <div class="text-soft small">{{ $user->email }}</div>
                        <span class="badge mt-1 bg-{{ $user->role_badge }}">{{ $user->role_label }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- ── Dados pessoais ── --}}
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem; letter-spacing:.07em;">
                        <i class="bi bi-person me-1"></i>Dados pessoais
                    </h6>

                    <div class="mb-3">
                        <label for="name" class="form-label text-soft small">Nome completo</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label text-soft small">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr style="border-color: rgba(148,163,184,.12);" class="my-4">

                    {{-- ── Troca de senha ── --}}
                    <h6 class="text-soft text-uppercase fw-semibold mb-1" style="font-size:.72rem; letter-spacing:.07em;">
                        <i class="bi bi-lock me-1"></i>Trocar senha
                    </h6>
                    <p class="text-soft small mb-3">Deixe os campos em branco para manter a senha atual.</p>

                    <div class="mb-3">
                        <label for="current_password" class="form-label text-soft small">Senha atual</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            autocomplete="current-password"
                        >
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-soft small">Nova senha</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label text-soft small">Confirmar nova senha</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Salvar alterações
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection