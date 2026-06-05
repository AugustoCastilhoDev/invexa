@extends('layouts.app')

@section('title', 'OC ' . $purchaseOrder->number)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Ordem de Compra <span style="font-family:monospace;">{{ $purchaseOrder->number }}</span></h1>
        <p class="text-soft mb-0">{{ $purchaseOrder->supplier?->name ?? '—' }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if($purchaseOrder->canReceive() && auth()->user()->isGerente())
            <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success btn-sm"
                        onclick="return confirm('Confirmar o recebimento desta ordem de compra?')">
                    <i class="bi bi-box-arrow-in-down me-1"></i>Registrar Recebimento
                </button>
            </form>
        @endif
        @if($purchaseOrder->canCancel() && auth()->user()->isGerente())
            <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST"
                  onsubmit="return confirm('Cancelar esta ordem de compra?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
            </form>
        @endif
        @if($purchaseOrder->status !== 'recebida')
        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        @endif
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Dados da OC --}}
    <div class="col-12 col-lg-4">
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-info-circle me-1"></i>Informações
                </span>
            </div>
            <div class="card-body">
                @php $color = $purchaseOrder->status_color; @endphp
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Status</dt>
                    <dd class="col-7 py-2 border-bottom border-secondary">
                        <span class="badge bg-{{ $color }} bg-opacity-25 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                            {{ $purchaseOrder->status_label }}
                        </span>
                    </dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Fornecedor</dt>
                    <dd class="col-7 py-2 border-bottom border-secondary">
                        @if($purchaseOrder->supplier)
                            <a href="{{ route('suppliers.show', $purchaseOrder->supplier) }}" class="text-info text-decoration-none">{{ $purchaseOrder->supplier->name }}</a>
                        @else
                            <span class="text-soft">—</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Criado por</dt>
                    <dd class="col-7 text-white py-2 border-bottom border-secondary">{{ $purchaseOrder->user?->name ?? '—' }}</dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Emissão</dt>
                    <dd class="col-7 text-white py-2 border-bottom border-secondary">{{ $purchaseOrder->order_date ? \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') : $purchaseOrder->created_at->format('d/m/Y') }}</dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Previsão</dt>
                    <dd class="col-7 text-white py-2 border-bottom border-secondary">
                        @if($purchaseOrder->expected_date)
                            {{ \Carbon\Carbon::parse($purchaseOrder->expected_date)->format('d/m/Y') }}
                        @else
                            <span class="text-soft">—</span>
                        @endif
                    </dd>

                    @if($purchaseOrder->received_at)
                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Recebido em</dt>
                    <dd class="col-7 text-white py-2 border-bottom border-secondary">{{ \Carbon\Carbon::parse($purchaseOrder->received_at)->format('d/m/Y') }}</dd>
                    @endif

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Total</dt>
                    <dd class="col-7 fw-bold text-white py-2">R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}</dd>

                    @if($purchaseOrder->notes)
                    <dt class="col-5 text-soft fw-normal py-2 border-top border-secondary">Observações</dt>
                    <dd class="col-7 text-soft py-2 border-top border-secondary">{{ $purchaseOrder->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Itens --}}
    <div class="col-12 col-lg-8">
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-list-ul me-1"></i>Itens do Pedido
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark mb-0 align-middle">
                        <thead>
                            <tr style="font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                       color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                <th class="ps-3 py-3">Produto</th>
                                <th class="py-3 text-center">Qtd Pedida</th>
                                <th class="py-3 text-center">Recebido</th>
                                <th class="py-3 text-center">Pendente</th>
                                <th class="py-3 text-end">Custo Unit.</th>
                                <th class="py-3 text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $item)
                                @php
                                    $qtyReceived = (int) ($item->quantity_received ?? 0);
                                    $qtyPending  = max(0, (int) $item->quantity - $qtyReceived);
                                @endphp
                                <tr style="border-color:rgba(148,163,184,.07);">
                                    <td class="ps-3 py-3">
                                        <div class="fw-semibold text-white">{{ $item->product->name }}</div>
                                        <div class="text-soft" style="font-size:.75rem;font-family:monospace;">{{ $item->product->sku }}</div>
                                    </td>
                                    <td class="py-3 text-center text-white">{{ $item->quantity }}</td>
                                    <td class="py-3 text-center">
                                        <span class="{{ $qtyReceived > 0 ? 'text-success' : 'text-soft' }} fw-semibold">
                                            {{ $qtyReceived }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($qtyPending > 0)
                                            <span class="text-warning fw-semibold">{{ $qtyPending }}</span>
                                        @else
                                            <span class="text-success"><i class="bi bi-check-circle-fill"></i></span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-end text-soft">R$ {{ number_format($item->unit_cost, 2, ',', '.') }}</td>
                                    <td class="py-3 text-end pe-3 fw-semibold text-white">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top:1px solid rgba(148,163,184,.2);">
                                <td colspan="5" class="text-end pe-3 py-3 text-soft fw-semibold">Total:</td>
                                <td class="py-3 text-end pe-3 fw-bold text-white">R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
