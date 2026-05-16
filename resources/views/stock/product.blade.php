@extends('layouts.app')

@section('title', 'Histórico de Estoque — ' . $product->name)

@section('content')

{{-- Info do produto --}}
<div class="card card-dark-bg shadow-sm border-0 mb-4">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h4 class="mb-1 text-white">{{ $product->name }}</h4>
                <p class="text-soft mb-0">SKU: {{ $product->sku }} &mdash; Histórico de movimentações</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('stock.create') }}?product_id={{ $product->id }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Nova Entrada
                </a>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Estoque atual</p>
                        <p class="fw-bold fs-5 mb-0 {{ $product->isLowStock() ? 'text-danger' : 'text-success' }}">
                            {{ $product->quantity }} {{ $product->unit }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Mínimo configurado</p>
                        <p class="text-white fw-semibold fs-5 mb-0">{{ $product->min_quantity }} {{ $product->unit }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Preço de venda</p>
                        <p class="text-white fw-semibold fs-5 mb-0">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Categoria</p>
                        <p class="text-white fw-semibold mb-0">{{ $product->category->name ?? 'Sem categoria' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Histórico --}}
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <span class="text-white fw-semibold">Histórico de Movimentações</span>
    </div>
    <div class="table-responsive">
        @if($movements->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-clock-history fs-1 text-soft"></i>
                <p class="text-soft mt-3">Nenhuma movimentação registrada para este produto.</p>
            </div>
        @else
        <table class="table table-dark table-hover mb-0 align-middle">
            <thead>
                <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                           color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                    <th class="ps-4 py-3">Data</th>
                    <th class="py-3">Tipo</th>
                    <th class="py-3">Motivo</th>
                    <th class="py-3">Antes</th>
                    <th class="py-3">Movimento</th>
                    <th class="py-3">Depois</th>
                    <th class="py-3">Usuário</th>
                    <th class="py-3">Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $mov)
                <tr style="border-color:rgba(148,163,184,.07);">
                    <td class="ps-4 py-3" style="color:#94a3b8;font-size:.82rem;">
                        {{ $mov->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                    </td>
                    <td class="py-3">
                        <span class="badge bg-{{ $mov->type_badge }}">{{ $mov->type_label }}</span>
                    </td>
                    <td class="py-3" style="color:#94a3b8;">{{ $mov->reason_label }}</td>
                    <td class="py-3" style="color:#94a3b8;">{{ $mov->quantity_before }}</td>
                    <td class="py-3 fw-semibold">
                        @if($mov->quantity > 0)
                            <span class="text-success">+{{ $mov->quantity }}</span>
                        @elseif($mov->quantity < 0)
                            <span class="text-danger">{{ $mov->quantity }}</span>
                        @else
                            <span class="text-soft">0</span>
                        @endif
                    </td>
                    <td class="py-3 text-white fw-semibold">{{ $mov->quantity_after }}</td>
                    <td class="py-3" style="color:#94a3b8;font-size:.82rem;">{{ $mov->user->name ?? 'Sistema' }}</td>
                    <td class="py-3" style="color:#94a3b8;font-size:.82rem;">{{ Str::limit($mov->notes, 50) ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($movements->hasPages())
        <div class="card-body pt-0">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
