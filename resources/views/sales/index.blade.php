@extends('layouts.app')

@section('title', 'Vendas')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Vendas</h4>
            <p class="text-soft mb-0">Acompanhe pedidos, status e receita com eficiência.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Voltar ao Dashboard</a>
            <a href="{{ route('sales.create') }}" class="btn btn-primary">Nova Venda</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1d4ed8, #2563eb);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Vendas</div>
                        <h3 class="mb-1">{{ $salesCount ?? $sales->total() }}</h3>
                        <div class="text-white-75 small">Total de pedidos</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #16a34a, #22c55e);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Concluídas</div>
                        <h3 class="mb-1">{{ $completedSales ?? 0 }}</h3>
                        <div class="text-white-75 small">Pedidos entregues</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b, #eab308);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Pendentes</div>
                        <h3 class="mb-1">{{ $pendingSales ?? 0 }}</h3>
                        <div class="text-white-75 small">Aguardando confirmação</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d97706, #f59e0b);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Receita</div>
                        <h3 class="mb-1">R$ {{ number_format($salesRevenue ?? 0, 2, ',', '.') }}</h3>
                        <div class="text-white-75 small">Receita total</div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('sales.index') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <label for="search" class="form-label text-white">Buscar cliente</label>
                <input type="text" id="search" name="search" class="form-control bg-dark text-white border-secondary"
                       value="{{ request('search') }}" placeholder="Digite o nome do cliente">
            </div>
            <div class="col-12 col-md-2">
                <label for="status" class="form-label text-white">Status</label>
                <select id="status" name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos</option>
                    <option value="concluida" @selected(request('status') === 'concluida')>Concluída</option>
                    <option value="pendente"  @selected(request('status') === 'pendente')>Pendente</option>
                    <option value="cancelada" @selected(request('status') === 'cancelada')>Cancelada</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label for="from" class="form-label text-white">De</label>
                <input type="date" id="from" name="from" class="form-control bg-dark text-white border-secondary" value="{{ request('from') }}">
            </div>
            <div class="col-12 col-md-2">
                <label for="to" class="form-label text-white">Até</label>
                <input type="date" id="to" name="to" class="form-control bg-dark text-white border-secondary" value="{{ request('to') }}">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
            </div>
        </form>

        <div class="table-responsive shadow-sm rounded">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-3 py-3">Cliente</th>
                        <th class="py-3">Data</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Produtos</th>
                        <th class="py-3">Total</th>
                        <th class="py-3 text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-3 py-3">
                                <div class="fw-semibold text-white">{{ $sale->customer_name ?? 'Sem nome' }}</div>
                                <div class="text-soft small">Venda #{{ $sale->id }}</div>
                            </td>
                            <td class="py-3" style="color:#94a3b8;font-size:.875rem;">
                                {{ $sale->sale_date ? $sale->sale_date->timezone(config('app.timezone'))->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3">
                                @php
                                    $badge = match ($sale->status) {
                                        'concluida' => 'success',
                                        'pendente'  => 'warning',
                                        'cancelada' => 'danger',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($sale->status) }}</span>
                            </td>
                            <td class="py-3">
                                @forelse ($sale->items as $item)
                                    <div class="text-white" style="font-size:.82rem;">
                                        <span class="text-soft">{{ $item->quantity }}x</span>
                                        {{ $item->product->name ?? 'Produto removido' }}
                                    </div>
                                @empty
                                    <span class="text-soft">—</span>
                                @endforelse
                            </td>
                            <td class="py-3 fw-semibold text-white">
                                R$ {{ number_format($sale->total, 2, ',', '.') }}
                            </td>
                            <td class="py-3 text-end pe-3">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-light">Ver</a>
                                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                                          onsubmit="return confirm('Excluir esta venda?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-soft">Nenhuma venda encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $sales->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
