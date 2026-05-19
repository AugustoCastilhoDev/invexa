@extends('layouts.app')

@section('title', 'Ordens de Compra')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Ordens de Compra</h4>
            <p class="text-soft mb-0">Controle de pedidos aos fornecedores.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
            @if(auth()->user()->hasRole(['admin','gerente']))
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Nova Ordem
                </a>
            @endif
        </div>
    </div>

    <div class="card-body">

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

        {{-- Cards de resumo --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card text-white border-0 shadow-sm" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
                    <div class="card-body py-3">
                        <div class="text-white-50 small text-uppercase fw-semibold mb-1">Total de OCs</div>
                        <h3 class="mb-0">{{ $totalOrders }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-white border-0 shadow-sm" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                    <div class="card-body py-3">
                        <div class="text-white-50 small text-uppercase fw-semibold mb-1">Aguardando Recebimento</div>
                        <h3 class="mb-0">{{ $pendingOrders }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-white border-0 shadow-sm" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                    <div class="card-body py-3">
                        <div class="text-white-50 small text-uppercase fw-semibold mb-1">Valor Total (OCs ativas)</div>
                        <h3 class="mb-0">R$ {{ number_format($totalValue, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-5">
                <select name="supplier" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos os fornecedores</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" @selected(request('supplier') == $sup->id)>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <select name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos os status</option>
                    @foreach(\App\Models\PurchaseOrder::STATUS_LABELS as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="table-responsive rounded shadow-sm">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-3 py-3">Número</th>
                        <th class="py-3">Fornecedor</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Previsão</th>
                        <th class="py-3">Total</th>
                        <th class="py-3">Criado por</th>
                        <th class="py-3 text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-3 py-3">
                                <span class="fw-semibold text-white" style="font-family:monospace;">{{ $order->number }}</span>
                            </td>
                            <td class="py-3" style="font-size:.875rem;">{{ $order->supplier->name ?? '&mdash;' }}</td>
                            <td class="py-3">
                                @php $color = $order->status_color; @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-25 text-{{ $color }} border border-{{ $color }} border-opacity-25"
                                      style="font-size:.72rem;">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="py-3 text-soft" style="font-size:.85rem;">
                                {{ $order->expected_date ? $order->expected_date->format('d/m/Y') : '&mdash;' }}
                            </td>
                            <td class="py-3 fw-semibold text-white">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td class="py-3 text-soft" style="font-size:.82rem;">{{ $order->user->name ?? '&mdash;' }}</td>
                            <td class="py-3 text-end pe-3">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('purchase-orders.show', $order) }}" class="btn btn-sm btn-outline-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($order->canReceive() && auth()->user()->hasRole(['admin','gerente']))
                                        <a href="{{ route('purchase-orders.show', $order) }}" class="btn btn-sm btn-outline-success" title="Receber">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-soft">
                                <i class="bi bi-cart-x fs-2 d-block mb-2 opacity-25"></i>
                                Nenhuma ordem de compra encontrada.
                                @if(auth()->user()->hasRole(['admin','gerente']))
                                    <div class="mt-2">
                                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-primary">Criar primeira ordem</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
