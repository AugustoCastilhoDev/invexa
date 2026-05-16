@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div style="max-width:640px; margin: 0 auto;">
    <div class="card card-dark-bg shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center py-3 px-4">
            <div>
                <h5 class="mb-0 text-white">Editar Usuário</h5>
                <small class="text-soft">{{ $user->name }}</small>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert mb-4" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171;border-radius:.5rem;">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label text-soft fw-semibold">Nome completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label text-soft fw-semibold">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-soft fw-semibold">Perfil de acesso</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror"
                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="admin"    @selected(old('role', $user->role) === 'admin')>Administrador</option>
                        <option value="gerente"  @selected(old('role', $user->role) === 'gerente')>Gerente</option>
                        <option value="vendedor" @selected(old('role', $user->role) === 'vendedor')>Vendedor</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <small class="text-soft">Você não pode alterar seu próprio perfil.</small>
                    @endif
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr style="border-color:rgba(148,163,184,.12);" class="my-4">

                <p class="text-soft small mb-3">Deixe em branco para manter a senha atual.</p>

                <div class="mb-3">
                    <label class="form-label text-soft fw-semibold">Nova senha</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Mínimo 8 caracteres com letras e números">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-soft fw-semibold">Confirmar nova senha</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection