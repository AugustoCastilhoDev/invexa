@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Novo usuário</h1>
        <p class="text-muted mb-0">Cadastre um novo usuário para a empresa.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Perfil</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="admin">Administrador</option>
                        <option value="gerente">Gerente</option>
                        <option value="vendedor">Vendedor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="active" id="active" class="form-check-input" value="1" checked>
                    <label for="active" class="form-check-label">Usuário ativo</label>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection