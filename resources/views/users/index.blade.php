@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Usuários</h4>
            <p class="text-soft mb-0">Gerencie os usuários vinculados à empresa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Voltar ao Dashboard</a>
            <a href="{{ route('users.create') }}" class="btn btn-primary">Novo Usuário</a>
        </div>
    </div>
    <div class="card-body">

        {{-- Alertas --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">
            <div class="col-12 col-md-5">
                <input
                    type="text"
                    name="search"
                    class="form-control bg-dark text-white border-secondary"
                    placeholder="Buscar por nome ou e-mail..."
                    value="{{ request('search') }}"
                >
            </div>
            <div class="col-6 col-md-3">
                <select name="role" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos os perfis</option>
                    <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="gerente"  {{ request('role') === 'gerente'  ? 'selected' : '' }}>Gerente</option>
                    <option value="vendedor" {{ request('role') === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos</option>
                    <option value="ativo"   {{ request('status') === 'ativo'   ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-light flex-fill">Filtrar</button>
                @if(request()->hasAny(['search','role','status']))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Limpar</a>
                @endif
            </div>
        </form>

        {{-- Tabela --}}
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold text-white">{{ $user->name }}</td>
                            <td class="text-soft">{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role_badge }}">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }}">
                                    {{ $user->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                    <form action="{{ route('users.toggle-active', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            {{ $user->active ? 'Desativar' : 'Ativar' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-soft">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if ($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 px-1">
                <small class="text-soft">
                    Exibindo {{ $users->firstItem() }}–{{ $users->lastItem() }}
                    de {{ $users->total() }} usuários
                </small>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
</div>
@endsection