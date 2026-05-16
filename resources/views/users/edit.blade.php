@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar usuário</h1>
            <p class="text-muted mb-0">Atualize os dados do usuário selecionado.</p>
        </div>

        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            Voltar
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $user->name) }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Perfil</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                            Administrador
                        </option>
                        <option value="gerente" {{ old('role', $user->role) === 'gerente' ? 'selected' : '' }}>
                            Gerente
                        </option>
                        <option value="vendedor" {{ old('role', $user->role) === 'vendedor' ? 'selected' : '' }}>
                            Vendedor
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nova senha</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Deixe em branco para manter a senha atual"
                    >
                    <div class="form-text">
                        Preencha somente se quiser alterar a senha.
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="active"
                        id="active"
                        value="1"
                        {{ old('active', $user->active) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="active">
                        Usuário ativo
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection