@extends('layouts.app')

@section('title', 'Receber OC ' . $purchaseOrder->number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-white">Registrar Recebimento</h1>
        <p class="text-soft mb-0">OC <strong class="text-white" style="font-family:monospace;">{{ $purchaseOrder->number }}</strong>
            &mdash; {{ $purchaseOrder->supplier->name }}</p>
    </div>
    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-light btn-sm">Voltar</a>
</div>

<div class="alert d-flex gap-3 align-items-center mb-4"
     style="background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.2);color:#fde047;border-radius:.6rem;">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div>Informe apenas as quantidades <strong>recebidas nesta entrega</strong>. O sistema atualizará o estoque automaticamente.</div>
</div>

<form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}">
    @csrf
    <div class="card card-dark-bg shadow-sm">
        <div class="card-header card-header-dark border-bottom">
            <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                <i class="bi bi-box-arrow-in-down me-1"></i>Itens a Receber
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
                            <th class="py-3 text-center">Já Recebido</th>
                            <th class="py-3 text-center">Pendente</th>
                            <th class="py-3 text-center" style="min-width:140px;">Receber Agora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-3 py-3">
                                    <div class="fw-semibold text-white">{{ $item->product->name }}</div>
                                    <div class="text-soft" style="font-size:.75rem;">
                                        Estoque atual: <strong class="text-white">{{ $item->product->quantity }} {{ $item->product->unit }}</strong>
                                    </div>
                                </td>
                                <td class="py-3 text-center text-white">{{ $item->quantity }}</td>
                                <td class="py-3 text-center text-success fw-semibold">{{ $item->quantity_received }}</td>
                                <td class="py-3 text-center text-warning fw-semibold">{{ $item->pending }}</td>
                                <td class="py-3 text-center">
                                    @if($item->pending > 0)
                                        <input type="number"
                                               name="items[{{ $item->id }}][quantity_received]"
                                               class="form-control form-control-sm text-center"
                                               min="0" max="{{ $item->pending }}"
                                               value="{{ $item->pending }}"
                                               style="width:90px;margin:0 auto;">
                                    @else
                                        <input type="hidden" name="items[{{ $item->id }}][quantity_received]" value="0">
                                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Completo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Confirmar Recebimento e Atualizar Estoque
        </button>
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection
