@extends('layouts.app')

@section('title', 'Detalhes do Produto')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">Detalhes do Produto</h4>
                <p class="text-soft mb-0">Informações completas do produto cadastrado.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Editar</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
            </div>
        </div>
    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- Coluna esquerda --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body">
                        <h6 class="text-soft fw-semibold mb-3 text-uppercase" style="font-size:.75rem; letter-spacing:.08em;">Identificação</h6>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Nome</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->name }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Categoria</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->category->name ?? '—' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">SKU</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->sku ?? '—' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Código de Barras</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->barcode ?? '—' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Unidade</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->unit ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-soft mb-1" style="font-size:.8rem;">Status</p>
                            @if($product->active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">Inativo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna direita --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body">
                        <h6 class="text-soft fw-semibold mb-3 text-uppercase" style="font-size:.75rem; letter-spacing:.08em;">Estoque & Preços</h6>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Preço de Venda</p>
                            <p class="text-white fw-semibold mb-0">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Custo</p>
                            <p class="text-white fw-semibold mb-0">R$ {{ number_format($product->cost ?? 0, 2, ',', '.') }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Quantidade em Estoque</p>
                            <p class="fw-semibold mb-0 {{ $product->quantity <= $product->min_quantity ? 'text-danger' : 'text-white' }}">
                                {{ $product->quantity }} un.
                                @if($product->quantity <= $product->min_quantity)
                                    <span class="badge bg-danger ms-1">Estoque baixo</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <p class="text-soft mb-1" style="font-size:.8rem;">Estoque Mínimo</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->min_quantity }} un.</p>
                        </div>

                        <div>
                            <p class="text-soft mb-1" style="font-size:.8rem;">Descrição</p>
                            <p class="text-white fw-semibold mb-0">{{ $product->description ?? 'Sem descrição' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ações --}}
        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Editar Produto</a>

            <form action="{{ route('products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir Produto</button>
            </form>
        </div>

    </div>
</div>
@endsection
