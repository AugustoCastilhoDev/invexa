@extends('layouts.app')

@section('title', 'Ordens de Compra')

@push('styles')
<style>
/*
 * Paleta padrão — purchase-orders/index
 * Verde  #4ade80 | Vermelho #f87171 | Azul #60a5fa | Amarelo #fbbf24 | Cinza #94a3b8
 */
.po-page-bg {
    background: radial-gradient(circle at top left, rgba(96,165,250,.08), transparent 20%),
                radial-gradient(circle at bottom right, rgba(34,197,94,.09), transparent 18%),
                #08101d;
    min-height: 100vh;
}
/* Inputs / selects no padrão dark */
.po-filters .form-control,
.po-filters .form-select {
    background: rgba(13,20,35,.92) !important;
    border: 1px solid rgba(148,163,184,.22) !important;
    color: #e2e8f0 !important;
    font-size: .82rem;
    border-radius: .45rem;
}
.po-filters .form-control::placeholder { color: rgba(148,163,184,.5); }
.po-filters .form-control:focus,
.po-filters .form-select:focus {
    border-color: rgba(96,165,250,.45) !important;
    box-shadow: 0 0 0 .2rem rgba(96,165,250,.12) !important;
    background: rgba(13,20,35,.98) !important;
}
.po-filters option { background: #0d1424; color: #e2e8f0; }
/* Card container */
.po-card {
    background: rgba(13,20,35,.92);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .75rem;
    overflow: hidden;
}
/* Table */
.po-table thead th {
    font-size: .62rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: rgba(148,163,184,.75) !important;
    border-bottom: 1px solid rgba(148,163,184,.18) !important;
    background: rgba(8,13,26,.6) !important;
    padding: .65rem .75rem; white-space: nowrap;
}
.po-table tbody td {
    font-size: .82rem; color: #cbd5e1 !important;
    border-color: rgba(148,163,184,.06) !important;
    vertical-align: middle; padding: .55rem .75rem;
    background: transparent !important;
}
.po-table tbody tr { border-color: rgba(148,163,184,.06) !important; }
.po-table tbody tr:hover td { background: rgba(96,165,250,.04) !important; }
.po-table tbody tr:last-child td { border-bottom: 0 !important; }
/* Empty state */
.po-empty { padding: 3rem 1rem; text-align: center; color: rgba(148,163,184,.5); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Ordens de Compra</h1>
        <p class="mb-0" style="font-size:.78rem;color:rgba(148,163,184,.65);">Gerencie as ordens de compra junto aos fornecedores.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">Voltar ao Dashboard</a>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Nova Ordem
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-2 mb-4 po-filters">
    <div class="col-12 col-md-5">
        <input type="text" name="search" class="form-control"
               placeholder="Buscar por fornecedor..." value="{{ request('search') }}">
    </div>
    <div class="col-12 col-md-3">
        <select name="status" class="form-select">
            <option value="">Todos os status</option>
            <option value="pending"   @selected(request('status') === 'pending')>Pendente</option>
            <option value="sent"      @selected(request('status') === 'sent')>Enviada</option>
            <option value="received"  @selected(request('status') === 'received')>Recebida</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelada</option>
        </select>
    </div>
    <div class="col-12 col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filtrar</button>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">Limpar</a>
    </div>
</form>

<div class="po-card">
    <div class="table-responsive">
        <table class="table mb-0 po-table">
            <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>Fornecedor</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="ps-4" style="color:#94a3b8;">#{{ $order->id }}</td>
                    <td class="fw-semibold text-white">{{ $order->supplier?->name ?? '&mdash;' }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->ordered_at)->format('d/m/Y') }}</td>
                    <td class="fw-semibold" style="color:#4ade80;">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                    <td>
                        @php
                            $statusMap = [
                                'pending'   => ['Pendente',  '#fbbf24', 'rgba(251,191,36,.12)',  'rgba(251,191,36,.28)'],
                                'sent'      => ['Enviada',   '#60a5fa', 'rgba(96,165,250,.12)',  'rgba(96,165,250,.28)'],
                                'received'  => ['Recebida',  '#4ade80', 'rgba(74,222,128,.12)',  'rgba(74,222,128,.28)'],
                                'cancelled' => ['Cancelada', '#f87171', 'rgba(248,113,113,.12)', 'rgba(248,113,113,.28)'],
                            ];
                            [$sLabel, $sColor, $sBg, $sBorder] = $statusMap[$order->status]
                                ?? [$order->status, '#94a3b8', 'rgba(148,163,184,.10)', 'rgba(148,163,184,.22)'];
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.68rem;font-weight:600;
                                     padding:.25rem .6rem;border-radius:999px;
                                     background:{{ $sBg }};color:{{ $sColor }};border:1px solid {{ $sBorder }};">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $sColor }};flex-shrink:0;"></span>
                            {{ $sLabel }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('purchase-orders.show', $order) }}"
                               class="btn btn-sm btn-outline-light" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($order->status === 'pending')
                                <a href="{{ route('purchase-orders.edit', $order) }}"
                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('purchase-orders.receive', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como Recebida"
                                            onclick="return confirm('Confirmar recebimento e atualizar estoque?')">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                </form>
                                <form action="{{ route('purchase-orders.destroy', $order) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"
                                            onclick="return confirm('Excluir esta ordem?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="po-empty">
                        <i class="bi bi-cart-x fs-2 d-block mb-2 opacity-25"></i>
                        Nenhuma ordem de compra encontrada.
                        <div class="mt-2">
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-primary">Criar primeira ordem</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="p-3">
        {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
