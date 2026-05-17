@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Produtos</h1>
        <p class="text-soft mb-0">Gerencie o catálogo de produtos e o controle de estoque.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Voltar ao Dashboard</a>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Produto
        </a>
    </div>
</div>

@php
    $company = auth()->user()->company;
    $limits  = $company ? $company->limits() : ['products' => 50];
@endphp
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#93c5fd;border-radius:.6rem;">
    <i class="bi bi-box-seam fs-5"></i>
    <div>
        Plano <strong>{{ $company?->plan_label ?? 'Gratuito' }}</strong> —
        {{ $totalProducts }} de {{ $limits['products'] }} produto(s) utilizados.
        @if($totalProducts >= $limits['products'])
            <span class="text-warning ms-2">
                <i class="bi bi-exclamation-triangle"></i> Limite atingido.
            </span>
        @endif
    </div>
</div>

<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm"
                     style="background: linear-gradient(135deg, #1d4ed8, #2563eb);">
                    <div class="card-body py-3">
                        <div class="text-soft small text-uppercase fw-semibold mb-1">Total de Produtos</div>
                        <h3 class="mb-0">{{ $totalProducts }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm"
                     style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
                    <div class="card-body py-3">
                        <div class="text-soft small text-uppercase fw-semibold mb-1">Categorias</div>
                        <h3 class="mb-0">{{ $categoriesCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm"
                     style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                    <div class="card-body py-3">
                        <div class="text-soft small text-uppercase fw-semibold mb-1">Estoque Baixo</div>
                        <h3 class="mb-0">{{ $lowStockCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="row g-2">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control"
                       placeholder="Buscar por nome..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-4">
                <select name="category" class="form-select">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle"
                   style="background: rgba(15,23,42,.88);">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-4 py-3">Produto</th>
                        <th class="py-3">SKU</th>
                        <th class="py-3">Categoria</th>
                        <th class="py-3">Preço</th>
                        <th class="py-3">Estoque</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-4 py-3">
                                <div class="fw-semibold text-white">{{ $product->name }}</div>
                                @if($product->description)
                                    <div class="text-soft small"
                                         style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        {{ $product->description }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3" style="color:#94a3b8;font-size:.875rem;font-family:monospace;">
                                {{ $product->sku }}
                            </td>
                            <td class="py-3" style="color:#94a3b8;font-size:.875rem;">
                                {{ optional($product->category)->name ?? '—' }}
                            </td>
                            <td class="py-3 fw-semibold text-white">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                @if($product->cost > 0)
                                    <div class="text-soft" style="font-size:.7rem;">
                                        Margem: {{ $product->margin }}%
                                    </div>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($product->isLowStock())
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;
                                                 font-weight:600;padding:.28rem .65rem;border-radius:999px;
                                                 background:rgba(239,68,68,.1);color:#f87171;
                                                 border:1px solid rgba(239,68,68,.2);">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        {{ $product->quantity }} {{ $product->unit }}
                                    </span>
                                @else
                                    <span style="color:#4ade80;font-weight:600;">
                                        {{ $product->quantity }} {{ $product->unit }}
                                    </span>
                                @endif
                                <div class="text-soft" style="font-size:.7rem;">
                                    Mín: {{ $product->min_quantity }}
                                </div>
                            </td>
                            <td class="py-3">
                                @if($product->active)
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;
                                                 font-weight:600;padding:.28rem .65rem;border-radius:999px;
                                                 background:rgba(34,197,94,.12);color:#4ade80;
                                                 border:1px solid rgba(34,197,94,.25);">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;"></span>
                                        Ativo
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;
                                                 font-weight:600;padding:.28rem .65rem;border-radius:999px;
                                                 background:rgba(148,163,184,.1);color:#94a3b8;
                                                 border:1px solid rgba(148,163,184,.2);">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#94a3b8;"></span>
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn btn-sm btn-outline-light" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Excluir o produto {{ addslashes($product->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-soft py-5">
                                <i class="bi bi-box-seam fs-2 d-block mb-2 opacity-25"></i>
                                Nenhum produto encontrado.
                                <div class="mt-2">
                                    <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                                        Cadastrar primeiro produto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection