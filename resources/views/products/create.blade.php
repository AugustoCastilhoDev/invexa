@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="page-shell">
    <div class="page-header d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-3">
        <div>
            <h1 class="section-title h3 mb-1">Novo Produto</h1>
            <p class="section-subtitle">Preencha os campos para cadastrar um novo item no estoque.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('products.index') }}" class="btn btn-outline-light">Voltar</a>
        </div>
    </div>

    <div class="page-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-2">Corrija os erros abaixo:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            @include('products._form', [
                'product' => $product ?? null,
                'categories' => $categories
            ])
        </form>
    </div>
</div>
@endsection