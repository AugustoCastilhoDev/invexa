@extends('layouts.app')

@section('title', 'Ordem de Compra #' . $purchaseOrder->id)

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Ordem de Compra #{{ $purchaseOrder->id }}</h4>
            <p class="text-soft mb-0">
                Criada em {{ \Carbon\Carbon::parse($purchaseOrder->ordered_at)->format('d/m/Y') }}
                &middot;
                @if($purchaseOrder->status === 'pending')
                    <span class="badge bg-warning text-dark">Pendente</span>
                @elseif($purchaseOrder->status === 'received')
                    <span class="badge bg-success">Recebida</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($purchaseOrder->status) }}</span>
                @endif
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
            @if($purchaseOrder->status === 'pending')
                @if(auth()->user()->hasRole(['admin','gerente']))
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}"
                          onsubmit="return confirm('Confirmar recebimento e atualizar estoque?')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-box-arrow-in-down me-1"></i>Receber
                        </button>
                    </form>
                    <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}"
                          onsubmit="return confirm('Excluir esta ordem?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card dashboard-card border-secondary">
                    <div class="card-body py-3">
                        <div class="text-soft small mb-1">Fornecedor</div>
                        <div class="text-white fw-semibold">{{ $purchaseOrder->supplier->name }}</div>
                        @if($purchaseOrder->supplier->email)
                            <div class="text-soft small"><i class="bi bi-envelope me-1"></i>{{ $purchaseOrder->supplier->email }}</div>
                        @endif
                        @if($purchaseOrder->supplier->phone)
                            <div class="text-soft small"><i class="bi bi-telephone me-1"></i>{{ $purchaseOrder->supplier->phone }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card border-secondary">
                    <div class="card-body py-3">
                        <div class="text-soft small mb-1">Data do Pedido</div>
                        <div class="text-white fw-semibold">{{ \Carbon\Carbon::parse($purchaseOrder->ordered_at)->format('d/m/Y') }}</div>
                        @if($purchaseOrder->received_at)
                            <div class="text-soft small">Recebido em {{ \Carbon\Carbon::parse($purchaseOrder->received_at)->format('d/m/Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card border-secondary">
                    <div class="card-body py-3">
                        <div class="text-soft small mb-1">Total</div>
                        <div class="text-white fw-semibold fs-5">R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($purchaseOrder->notes)
            <div class="mb-4 p-3 rounded" style="background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.12);">
                <div class="text-soft small mb-1">Observações</div>
                <div class="text-white">{{ $purchaseOrder->notes }}</div>
            </div>
        @endif

        <h6 class="text-white mb-3">Itens</h6>
        <div class="table-responsive rounded">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-3 py-3">Produto</th>
                        <th class="py-3 text-end">Qtd</th>
                        <th class="py-3 text-end">Preço Unit.</th>
                        <th class="py-3 text-end pe-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                    <tr style="border-color:rgba(148,163,184,.07);">
                        <td class="ps-3 py-2 text-white">{{ $item->product->name }}</td>
                        <td class="py-2 text-end text-soft">{{ $item->quantity }}</td>
                        <td class="py-2 text-end text-soft">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="py-2 text-end pe-3 text-white">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:1px solid rgba(148,163,184,.2);">
                        <td colspan="3" class="ps-3 py-3 text-white fw-semibold">Total</td>
                        <td class="py-3 text-end pe-3 text-white fw-bold fs-6">R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
