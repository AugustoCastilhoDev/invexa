@extends('layouts.app')

@section('title', 'Ordem de Compra #' . $purchaseOrder->id)

@push('styles')
<style>
/*
 * Paleta padrão — purchase-orders/show
 * Verde #4ade80 | Vermelho #f87171 | Amarelo #fbbf24 | Cinza #94a3b8
 * Enviada  = Amarelo #fbbf24
 * Recebida = Verde   #4ade80
 * Pendente = Laranja #fb923c
 * Cancelada= Vermelho#f87171
 */
.po-show-card {
    background: rgba(13,20,35,.92);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .75rem;
    overflow: hidden;
}
.po-show-header {
    background: rgba(8,13,26,.6);
    border-bottom: 1px solid rgba(148,163,184,.12);
    padding: 1rem 1.25rem;
}
.po-info-card {
    background: rgba(15,23,42,.85);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .65rem;
    padding: .85rem 1.1rem;
}
.po-label {
    font-size: .62rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: rgba(148,163,184,.65); margin-bottom: .3rem;
}
.po-value    { font-size: .92rem; font-weight: 600; color: #f1f5f9; }
.po-value-lg { font-size: 1.25rem; font-weight: 700; color: #4ade80; }
.po-sub      { font-size: .75rem; color: rgba(148,163,184,.6); margin-top: .15rem; }
/* Tabela de itens */
.po-table thead th {
    font-size: .62rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: rgba(148,163,184,.75) !important;
    border-bottom: 1px solid rgba(148,163,184,.18) !important;
    background: rgba(8,13,26,.5) !important;
    padding: .65rem .75rem; white-space: nowrap;
}
.po-table tbody td {
    font-size: .82rem; color: #cbd5e1 !important;
    border-color: rgba(148,163,184,.06) !important;
    vertical-align: middle; padding: .55rem .75rem;
    background: transparent !important;
}
.po-table tfoot td {
    background: rgba(8,13,26,.5) !important;
    border-top: 1px solid rgba(148,163,184,.18) !important;
    color: #f1f5f9 !important; font-weight: 700;
    padding: .65rem .75rem;
}
.po-table tfoot .total-val { color: #4ade80 !important; font-size: 1rem; }
.po-table tbody tr:hover td { background: rgba(96,165,250,.04) !important; }
/* Notas */
.po-notes {
    background: rgba(15,23,42,.85);
    border-left: 3px solid rgba(148,163,184,.3);
    border-radius: 0 .45rem .45rem 0;
    padding: .75rem 1rem;
    font-size: .82rem; color: rgba(226,232,240,.75);
}
</style>
@endpush

@section('content')

@php
    /*
     * Enviada  (#fbbf24 amarelo) | Recebida (#4ade80 verde)
     * Pendente (#fb923c laranja) | Cancelada (#f87171 vermelho)
     */
    $statusMap = [
        'pending'   => ['Pendente',  '#fb923c', 'rgba(251,146,60,.12)',  'rgba(251,146,60,.30)'],
        'sent'      => ['Enviada',   '#fbbf24', 'rgba(251,191,36,.12)',  'rgba(251,191,36,.30)'],
        'received'  => ['Recebida',  '#4ade80', 'rgba(74,222,128,.12)',  'rgba(74,222,128,.30)'],
        'cancelled' => ['Cancelada', '#f87171', 'rgba(248,113,113,.12)', 'rgba(248,113,113,.30)'],
    ];
    [$sLabel, $sColor, $sBg, $sBorder] = $statusMap[$purchaseOrder->status]
        ?? [$purchaseOrder->status, '#94a3b8', 'rgba(148,163,184,.10)', 'rgba(148,163,184,.22)'];
@endphp

<div class="po-show-card">

    {{-- Header --}}
    <div class="po-show-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Ordem de Compra #{{ $purchaseOrder->id }}</h4>
            <div style="font-size:.78rem;color:rgba(148,163,184,.65);">
                Criada em {{ \Carbon\Carbon::parse($purchaseOrder->ordered_at)->format('d/m/Y') }}
                &middot;
                <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.68rem;font-weight:600;
                             padding:.2rem .6rem;border-radius:999px;vertical-align:middle;
                             background:{{ $sBg }};color:{{ $sColor }};border:1px solid {{ $sBorder }};">
                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $sColor }};flex-shrink:0;"></span>
                    {{ $sLabel }}
                </span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
            @if($purchaseOrder->status === 'pending')
                @if(auth()->user()->hasRole(['admin','gerente']))
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}"
                          onsubmit="return confirm('Confirmar recebimento e atualizar estoque?')">
                        @csrf
                        <button type="submit" class="btn btn-sm"
                                style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
                            <i class="bi bi-box-arrow-in-down me-1"></i>Receber
                        </button>
                    </form>
                    <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}"
                          onsubmit="return confirm('Excluir esta ordem?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="p-3 p-md-4">

        @if(session('success'))
            <div class="alert alert-dismissible fade show mb-3"
                 style="background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.25);color:#4ade80;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-dismissible fade show mb-3"
                 style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);color:#f87171;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Info cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="po-info-card h-100">
                    <div class="po-label">Fornecedor</div>
                    <div class="po-value">{{ $purchaseOrder->supplier->name }}</div>
                    @if($purchaseOrder->supplier->email)
                        <div class="po-sub"><i class="bi bi-envelope me-1"></i>{{ $purchaseOrder->supplier->email }}</div>
                    @endif
                    @if($purchaseOrder->supplier->phone)
                        <div class="po-sub"><i class="bi bi-telephone me-1"></i>{{ $purchaseOrder->supplier->phone }}</div>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="po-info-card h-100">
                    <div class="po-label">Data do Pedido</div>
                    <div class="po-value">{{ \Carbon\Carbon::parse($purchaseOrder->ordered_at)->format('d/m/Y') }}</div>
                    @if($purchaseOrder->received_at)
                        <div class="po-sub">Recebido em {{ \Carbon\Carbon::parse($purchaseOrder->received_at)->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="po-info-card h-100">
                    <div class="po-label">Total</div>
                    <div class="po-value-lg">R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        @if($purchaseOrder->notes)
        <div class="po-notes mb-4">
            <span style="font-size:.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(148,163,184,.65);">Observações</span><br>
            <span>{{ $purchaseOrder->notes }}</span>
        </div>
        @endif

        {{-- Itens --}}
        <div style="font-size:.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                    color:rgba(148,163,184,.65);margin-bottom:.6rem;">
            <i class="bi bi-list-ul me-1"></i>Itens
        </div>
        <div class="table-responsive"
             style="border-radius:.55rem;overflow:hidden;border:1px solid rgba(148,163,184,.08);">
            <table class="table mb-0 po-table">
                <thead>
                    <tr>
                        <th class="ps-3">Produto</th>
                        <th class="text-end">Qtd</th>
                        <th class="text-end">Preço Unit.</th>
                        <th class="text-end pe-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="ps-3">{{ $item->product->name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-end pe-3">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="ps-3">Total</td>
                        <td class="text-end pe-3 total-val">
                            R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
