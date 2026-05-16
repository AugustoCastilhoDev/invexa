@extends('layouts.app')

@section('title', 'Detalhes da Categoria')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex align-items-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Detalhes: {{ $category->name }}</h4>
            <p class="text-soft mb-0">Informações detalhadas do grupo de produtos.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-light">Voltar</a>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="text-soft small text-uppercase fw-semibold d-block mb-1">Nome da Categoria</label>
                <div class="h5 text-white">{{ $category->name }}</div>
            </div>
            <div class="col-md-6">
                <label class="text-soft small text-uppercase fw-semibold d-block mb-1">Status</label>
                <div>
                    <span class="badge bg-{{ $category->active ? 'success' : 'secondary' }} fs-6">
                        {{ $category->active ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>
            </div>
            <div class="col-12">
                <label class="text-soft small text-uppercase fw-semibold d-block mb-1">Descrição</label>
                <div class="p-3 rounded bg-black-50 text-white border border-secondary-subtle">
                    {{ $category->description ?? 'Nenhuma descrição informada para esta categoria.' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection