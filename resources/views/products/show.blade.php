@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">
            {{ $product->name }}
            @if($product->isLowStock())
                <span class="badge ms-2"
                      style="font-size:.6rem;background:rgba(239,68,68,.15);color:#f87171;
                             border:1px solid rgba(239,68,68,.3);vertical-align:middle;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Estoque Baixo
                </span>
            @endif
        </h1>
        <p class="text-soft mb-0">Detalhes do produto</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-outline-light">Voltar</a>
        @if($product->isLowStock())
            <a href="{{ route('purchase-orders.create', ['product_id' => $product->id, 'supplier_id' => $product->supplier_id]) }}"
               class="btn btn-warning text-dark fw-semibold">
                <i class="bi bi-cart-plus me-1"></i>Sugerir OC
            </a>
        @endif
        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
    </div>
</div>

{{-- Banner de alerta no topo do show --}}
@if($product->isLowStock())
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#f87171;border-radius:.6rem;">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>
        <strong>Atenção:</strong> estoque atual
        (<strong>{{ $product->quantity }} {{ $product->unit ?? 'un' }}</strong>)
        está abaixo do mínimo definido
        (<strong>{{ $product->min_quantity }} {{ $product->unit ?? 'un' }}</strong>).
        @if($product->supplier)
            Considere criar uma
            <a href="{{ route('purchase-orders.create', ['product_id' => $product->id, 'supplier_id' => $product->supplier_id]) }}"
               style="color:#fca5a5;font-weight:600;">Ordem de Compra</a>
            com o fornecedor <strong>{{ $product->supplier->name }}</strong>.
        @else
            Vincule um fornecedor a este produto para facilitar reposição.
        @endif
    </div>
</div>
@endif

<div class="row g-4">

    {{-- Informações principais --}}
    <div class="col-12 col-lg-8">
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-box-seam me-1"></i>Informações do Produto
                </span>
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.9rem;">
                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">SKU</dt>
                    <dd class="col-8 text-white py-2 border-bottom border-secondary" style="font-family:monospace;">{{ $product->sku }}</dd>

                    @if($product->barcode)
                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">Cód. Barras</dt>
                    <dd class="col-8 text-white py-2 border-bottom border-secondary" style="font-family:monospace;">{{ $product->barcode }}</dd>
                    @endif

                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">Categoria</dt>
                    <dd class="col-8 py-2 border-bottom border-secondary">
                        @if($product->category)
                            <span class="badge bg-secondary">{{ $product->category->name }}</span>
                        @else
                            <span class="text-soft">&mdash;</span>
                        @endif
                    </dd>

                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">Fornecedor</dt>
                    <dd class="col-8 py-2 border-bottom border-secondary">
                        @if($product->supplier)
                            <a href="{{ route('suppliers.show', $product->supplier) }}" class="text-info text-decoration-none">
                                <i class="bi bi-truck me-1"></i>{{ $product->supplier->name }}
                            </a>
                        @else
                            <span class="text-soft">&mdash;</span>
                        @endif
                    </dd>

                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">Preço de Venda</dt>
                    <dd class="col-8 text-white fw-semibold py-2 border-bottom border-secondary">R$ {{ number_format($product->price, 2, ',', '.') }}</dd>

                    @if($product->cost > 0)
                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">Custo</dt>
                    <dd class="col-8 py-2 border-bottom border-secondary">
                        <span class="text-white">R$ {{ number_format($product->cost, 2, ',', '.') }}</span>
                        <span class="text-soft ms-2" style="font-size:.8rem;">Margem: {{ $product->margin }}%</span>
                    </dd>
                    @endif

                    <dt class="col-4 text-soft fw-normal py-2 border-bottom border-secondary">Unidade</dt>
                    <dd class="col-8 text-white py-2 border-bottom border-secondary">{{ $product->unit ?? 'Un' }}</dd>

                    @if($product->description)
                    <dt class="col-4 text-soft fw-normal py-2">Descrição</dt>
                    <dd class="col-8 text-soft py-2">{{ $product->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Estoque + status --}}
    <div class="col-12 col-lg-4">
        <div class="card card-dark-bg shadow-sm mb-3"
             style="{{ $product->isLowStock() ? 'border-color:rgba(239,68,68,.35)!important;' : '' }}">
            <div class="card-header card-header-dark border-bottom"
                 style="{{ $product->isLowStock() ? 'border-color:rgba(239,68,68,.3)!important;' : '' }}">
                <span class="text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;
                      color:{{ $product->isLowStock() ? '#f87171' : 'rgba(226,232,240,.72)' }};">
                    <i class="bi bi-archive me-1"></i>Estoque
                    @if($product->isLowStock())
                        <i class="bi bi-exclamation-triangle-fill ms-1"></i>
                    @endif
                </span>
            </div>
            <div class="card-body text-center">
                <div style="font-size:2.5rem;font-weight:700;
                            color:{{ $product->isLowStock() ? '#f87171' : '#4ade80' }};">
                    {{ $product->quantity }}
                </div>
                <div class="text-soft">{{ $product->unit ?? 'Und' }} em estoque</div>
                <div class="mt-2" style="font-size:.82rem;">
                    <span class="text-soft">Mínimo: </span>
                    <strong class="text-white">{{ $product->min_quantity }}</strong>
                </div>
                @if($product->min_quantity > 0)
                <div class="mt-3">
                    @php
                        $pct = $product->min_quantity > 0
                            ? min(100, round(($product->quantity / $product->min_quantity) * 100))
                            : 100;
                        $barColor = $product->isLowStock() ? '#ef4444' : '#22c55e';
                    @endphp
                    <div style="height:6px;background:rgba(148,163,184,.15);border-radius:999px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:{{ $barColor }};border-radius:999px;
                                    transition:width .4s ease;"></div>
                    </div>
                    <div class="text-soft mt-1" style="font-size:.7rem;">{{ $pct }}% do estoque mínimo</div>
                </div>
                @endif
                @if($product->isLowStock())
                    <div class="mt-3">
                        <a href="{{ route('purchase-orders.create', ['product_id' => $product->id, 'supplier_id' => $product->supplier_id]) }}"
                           class="btn btn-sm btn-warning text-dark fw-semibold w-100">
                            <i class="bi bi-cart-plus me-1"></i>Sugerir Ordem de Compra
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-info-circle me-1"></i>Status
                </span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-soft">Situação</span>
                    @if($product->active)
                        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;
                                     font-weight:600;padding:.28rem .7rem;border-radius:999px;
                                     background:rgba(34,197,94,.12);color:#4ade80;
                                     border:1px solid rgba(34,197,94,.25);">
                            <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;"></span>Ativo
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;
                                     font-weight:600;padding:.28rem .7rem;border-radius:999px;
                                     background:rgba(148,163,184,.1);color:#94a3b8;
                                     border:1px solid rgba(148,163,184,.2);">
                            <span style="width:6px;height:6px;border-radius:50%;background:#94a3b8;"></span>Inativo
                        </span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="text-soft" style="font-size:.8rem;">Cadastrado</span>
                    <span class="text-white" style="font-size:.8rem;">{{ $product->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="text-soft" style="font-size:.8rem;">Atualizado</span>
                    <span class="text-white" style="font-size:.8rem;">{{ $product->updated_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
