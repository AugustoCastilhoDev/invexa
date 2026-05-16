@extends('layouts.app')

@section('title', 'Nova Categoria')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <h4 class="mb-1 text-white">Nova Categoria</h4>
        <p class="text-soft mb-0">Preencha os campos para criar um novo grupo de produtos.</p>
    </div>
    <div class="card-body">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label text-soft fw-semibold">Nome da Categoria</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ex: Bebidas, Limpeza..." required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label text-soft fw-semibold">Descrição (Opcional)</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Breve descrição sobre a categoria..."></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="active" id="active" checked>
                    <label class="form-check-label text-white" for="active">Categoria Ativa</label>
                </div>
                <small class="text-soft">Categorias inativas não aparecerão no cadastro de produtos.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Salvar Categoria</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection