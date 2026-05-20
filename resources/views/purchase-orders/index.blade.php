@extends('layouts.app')

@section('title', 'Ordens de Compra')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Ordens de Compra</h1>
        <p class="text-soft mb-0">Gerencie as ordens de compra junto aos fornecedores.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Voltar ao Dashboard</a>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nova Ordem
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-2 mb-4">
    <div class="col-12 col-md-5">
        <input type="text" name="search" class="form-control" placeholder="Buscar por fornecedor..." value="{{ request('search') }}">
    </div>
    <div class="col-12 col-md-3">
        <select name="status" class="form-select">
            <option value="">Todos os status</option>
            <option value="pending"   @selected(request('status') === 'pending')>Pendente</option>
            <option value="received"  @selected(request('status') === 'received')>Recebida</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelada</option>
        </select>
    </div>
    <div class="col-12 col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
    </div>
</form>

<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle" style="background:rgba(15,23,42,.88);">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-4 py-3">#</th>
                        <th class="py-3">Fornecedor</th>
                        <th class="py-3">Data</th>
                        <th class="py-3">Total</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-4 py-3" style="color:#94a3b8;font-size:.85rem;">#{{ $order->id }}</td>
                            <td class="py-3 fw-semibold text-white">{{ $order->supplier?->name ?? '&mdash;' }}</td>
                            <td class="py-3" style="color:#94a3b8;">{{ \Carbon\Carbon::parse($order->ordered_at)->format('d/m/Y') }}</td>
                            <td class="py-3 fw-semibold text-white">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td class="py-3">
                                @php
                                    $statusMap = [
                                        'pending'   => ['label' => 'Pendente',  'color' => '#fbbf24', 'bg' => 'rgba(251,191,36,.1)',  'border' => 'rgba(251,191,36,.25)'],
                                        'received'  => ['label' => 'Recebida',  'color' => '#4ade80', 'bg' => 'rgba(74,222,128,.1)',  'border' => 'rgba(74,222,128,.25)'],
                                        'cancelled' => ['label' => 'Cancelada', 'color' => '#f87171', 'bg' => 'rgba(248,113,113,.1)', 'border' => 'rgba(248,113,113,.25)'],
                                    ];
                                    $s = $statusMap[$order->status] ?? ['label' => $order->status, 'color' => '#94a3b8', 'bg' => 'rgba(148,163,184,.1)', 'border' => 'rgba(148,163,184,.2)'];
                                @endphp
                                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:600;
                                             padding:.28rem .65rem;border-radius:999px;
                                             background:{{ $s['bg'] }};color:{{ $s['color'] }};border:1px solid {{ $s['border'] }};">
                                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $s['color'] }};"></span>
                                    {{ $s['label'] }}
                                </span>
                            </td>
                            <td class="py-3 text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('purchase-orders.show', $order) }}" class="btn btn-sm btn-outline-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($order->status === 'pending')
                                        <a href="{{ route('purchase-orders.edit', $order) }}" class="btn btn-sm btn-outline-primary" title="Editar">
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
                            <td colspan="6" class="text-center text-soft py-5">
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
        <div class="p-3">
            {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
