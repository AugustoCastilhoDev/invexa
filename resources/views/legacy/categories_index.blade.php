@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card card-inventory">
        <div class="inventory-header d-flex justify-content-between align-items-center">
            <h3 class="m-0">Gestão de Categorias</h3>
            <a href="{{ route('categories.create') }}" class="btn btn-add-category">
                <i class="fas fa-plus-circle"></i> Nova Categoria
            </a>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-inventory mt-3">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="30%">Nome da Categoria</th>
                            <th width="40%">Descrição</th>
                            <th width="20%" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>#{{ $category->id }}</td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->description ?? 'Nenhuma descrição informada.' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-info">Editar</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir esta categoria?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Nenhuma categoria encontrada no sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection