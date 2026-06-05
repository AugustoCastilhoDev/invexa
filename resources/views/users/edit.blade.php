@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <div class="card dashboard-card card-dark-bg shadow-sm border-0">
            <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-white">
                        <i class="bi bi-person-gear me-2 text-primary"></i>Editar Usuário
                    </h4>
                    <p class="text-soft mb-0">Atualize os dados do usuário selecionado.</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <div class="card-body p-4">

                {{-- Avatar + info do usuário --}}
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

                {{-- Erros de validação --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif

                <form action="{{ route('users.update', $user) }}" method="POST">
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
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-soft small">E-mail</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label text-soft small">Perfil de acesso</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin"    {{ old('role', $user->role) === 'admin'    ? 'selected' : '' }}>Administrador</option>
                            <option value="gerente"  {{ old('role', $user->role) === 'gerente'  ? 'selected' : '' }}>Gerente</option>
                            <option value="vendedor" {{ old('role', $user->role) === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr style="border-color: rgba(148,163,184,.12);" class="my-4">

                    {{-- ── Senha ── --}}
                    <h6 class="text-soft text-uppercase fw-semibold mb-1" style="font-size:.72rem; letter-spacing:.07em;">
                        <i class="bi bi-lock me-1"></i>Senha
                    </h6>
                    <p class="text-soft small mb-3">Deixe em branco para manter a senha atual.</p>

                    <div class="mb-4">
                        <label for="password" class="form-label text-soft small">Nova senha</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr style="border-color: rgba(148,163,184,.12);" class="my-4">

                    {{-- ── Status ── --}}
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem; letter-spacing:.07em;">
                        <i class="bi bi-toggle-on me-1"></i>Status
                    </h6>

                    <div class="form-check form-switch mb-4">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="active"
                            id="active"
                            value="1"
                            {{ old('active', $user->active) ? 'checked' : '' }}
                            style="width: 2.5em; height: 1.25em;"
                        >
                        <label class="form-check-label text-soft ms-2" for="active">
                            Usuário ativo
                        </label>
                    </div>

                    {{-- ── Ações ── --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-light">
                            Cancelar
                        </a>
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