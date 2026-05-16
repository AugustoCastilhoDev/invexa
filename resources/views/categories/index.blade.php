@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Categorias</h4>
            <p class="text-soft mb-0">Organize os grupos de produtos do sistema com controle de status.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Voltar ao Dashboard</a>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">Nova Categoria</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1d4ed8, #2563eb);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Categorias</div>
                        <div class="mb-2">
                            <h3 class="mb-1">{{ $categories->total() }}</h3>
                            <div class="text-white-75 small">Total de grupos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #16a34a, #22c55e);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Ativas</div>
                        <div class="mb-2">
                            <h3 class="mb-1">{{ $activeCategories ?? 0 }}</h3>
                            <div class="text-white-75 small">Grupos disponíveis</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Inativas</div>
                        <div class="mb-2">
                            <h3 class="mb-1">{{ $inactiveCategories ?? 0 }}</h3>
                            <div class="text-white-75 small">Grupos arquivados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive shadow-sm rounded">
            <table class="table table-dark table-hover mb-0 align-middle table-dark-custom">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <div class="fw-semibold text-white">{{ $category->name }}</div>
                                <div class="text-soft small">Categoria de produtos</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $category->active ? 'success' : 'secondary' }}">
                                    {{ $category->active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-light">Ver</a>
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Excluir esta categoria?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-soft">Nenhuma categoria encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $categories->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection