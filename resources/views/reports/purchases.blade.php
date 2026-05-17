@extends('layouts.app')

@section('title', 'Relatório de Compras')

@push('styles')
<style>
    .kpi-card { border-radius:.75rem; padding:1.25rem 1.5rem; }
    .kpi-label { font-size:.72rem; text-transform:uppercase; font-weight:700; letter-spacing:.08em; opacity:.75; }
    .kpi-value { font-size:1.75rem; font-weight:700; line-height:1.2; margin-top:.25rem; }
    .kpi-sub   { font-size:.78rem; opacity:.65; margin-top:.2rem; }
</style>
@endpush

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white"><i class="bi bi-graph-up me-2"></i>Relatório de Compras</h4>
            <p class="text-soft mb-0">Análise de ordens de compra por período, fornecedor e status.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.purchases.pdf', request()->query()) }}"
               class="btn btn-outline-danger btn-sm">
                <i class="bi bi-filetype-pdf me-1"></i>Exportar PDF
            </a>
            <a href="{{ route('reports.purchases.csv', request()->query()) }}"
               class="btn btn-outline-success btn-sm">
                <i class="bi bi-filetype-csv me-1"></i>Exportar CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-light btn-sm">Vendas</a>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('reports.purchases') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <label class="form-label text-soft" style="font-size:.78rem;">Período</label>
                <select name="period" id="periodSelect" class="form-select bg-dark text-white border-secondary">
                    @foreach(['7' => 'Últimos 7 dias','30' => 'Últimos 30 dias','90' => 'Últimos 90 dias','365' => 'Último ano','custom' => 'Período personalizado'] as $val => $label)
                        <option value="{{ $val }}" @selected($period === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2" id="customFrom" style="{{ $period === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label text-soft" style="font-size:.78rem;">De</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', $from->format('Y-m-d')) }}">
            </div>
            <div class="col-6 col-md-2" id="customTo" style="{{ $period === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label text-soft" style="font-size:.78rem;">Até</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', $to->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label text-soft" style="font-size:.78rem;">Fornecedor</label>
                <select name="supplier_id" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos os fornecedores</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" @selected($supplierId == $sup->id)>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label text-soft" style="font-size:.78rem;">Status</label>
                <select name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos</option>
                    @foreach(\App\Models\PurchaseOrder::STATUS_LABELS as $val => $label)
                        <option value="{{ $val }}" @selected($status === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('reports.purchases') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
            </div>
        </form>

        <p class="text-soft mb-4" style="font-size:.82rem;">
            <i class="bi bi-calendar-range me-1"></i>
            Exibindo: <strong class="text-white">{{ $from->format('d/m/Y') }}</strong>
            até <strong class="text-white">{{ $to->format('d/m/Y') }}</strong>
            @if($orders->count() > 0)
                &mdash; <strong class="text-white">{{ $orders->count() }}</strong> OC(s) encontrada(s)
            @endif
        </p>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
                    <div class="kpi-label">Total de OCs</div>
                    <div class="kpi-value">{{ $totalOrders }}</div>
                    <div class="kpi-sub">no período</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#0f766e,#0d9488);">
                    <div class="kpi-label">Valor Total</div>
                    <div class="kpi-value">R$ {{ number_format($totalValue, 2, ',', '.') }}</div>
                    <div class="kpi-sub">OCs ativas + recebidas</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                    <div class="kpi-label">Recebido</div>
                    <div class="kpi-value">R$ {{ number_format($receivedValue, 2, ',', '.') }}</div>
                    <div class="kpi-sub">OCs concluídas</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#b45309,#d97706);">
                    <div class="kpi-label">Pendente</div>
                    <div class="kpi-value">R$ {{ number_format($pendingValue, 2, ',', '.') }}</div>
                    <div class="kpi-sub">aguardando recebimento</div>
                </div>
            </div>
        </div>

        @if($orders->count() === 0)
            <div class="text-center py-5 text-soft">
                <i class="bi bi-cart-x fs-2 d-block mb-2 opacity-25"></i>
                Nenhuma ordem de compra encontrada no período selecionado.
            </div>
        @else

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card card-dark-bg shadow-sm h-100">
                    <div class="card-header card-header-dark border-bottom">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-building me-1"></i>Por Fornecedor
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-dark mb-0 align-middle">
                            <thead>
                                <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                           color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                    <th class="ps-3 py-2">Fornecedor</th>
                                    <th class="py-2 text-center">OCs</th>
                                    <th class="py-2 text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bySupplier as $row)
                                    <tr style="border-color:rgba(148,163,184,.07);">
                                        <td class="ps-3 py-2 text-white" style="font-size:.85rem;">{{ $row['name'] }}</td>
                                        <td class="py-2 text-center text-soft">{{ $row['count'] }}</td>
                                        <td class="py-2 text-end pe-3 fw-semibold text-white">R$ {{ number_format($row['total'], 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-3 text-soft">Sem dados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="card card-dark-bg shadow-sm h-100">
                    <div class="card-header card-header-dark border-bottom">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-box-seam me-1"></i>Produtos Mais Comprados
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-dark mb-0 align-middle">
                            <thead>
                                <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                           color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                    <th class="ps-3 py-2">Produto</th>
                                    <th class="py-2">Categoria</th>
                                    <th class="py-2 text-center">Qtd</th>
                                    <th class="py-2 text-center">OCs</th>
                                    <th class="py-2 text-end pe-3">Custo Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topItems as $item)
                                    <tr style="border-color:rgba(148,163,184,.07);">
                                        <td class="ps-3 py-2 text-white" style="font-size:.85rem;">{{ $item->product_name }}</td>
                                        <td class="py-2 text-soft" style="font-size:.8rem;">{{ $item->category_name ?? '&mdash;' }}</td>
                                        <td class="py-2 text-center text-white">{{ $item->total_qty }}</td>
                                        <td class="py-2 text-center text-soft">{{ $item->total_orders }}</td>
                                        <td class="py-2 text-end pe-3 fw-semibold text-white">R$ {{ number_format($item->total_cost, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-soft">Sem dados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-list-ul me-1"></i>Ordens de Compra no Período
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                       color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                <th class="ps-3 py-3">Número</th>
                                <th class="py-3">Fornecedor</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Emissão</th>
                                <th class="py-3">Recebimento</th>
                                <th class="py-3 text-end pe-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr style="border-color:rgba(148,163,184,.07);cursor:pointer;"
                                    onclick="window.location='{{ route('purchase-orders.show', $order) }}'">
                                    <td class="ps-3 py-3">
                                        <span class="fw-semibold text-white" style="font-family:monospace;">{{ $order->number }}</span>
                                    </td>
                                    <td class="py-3" style="font-size:.875rem;">{{ optional($order->supplier)->name }}</td>
                                    <td class="py-3">
                                        @php $color = $order->status_color; @endphp
                                        <span class="badge bg-{{ $color }} bg-opacity-25 text-{{ $color }} border border-{{ $color }} border-opacity-25"
                                              style="font-size:.72rem;">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-soft" style="font-size:.85rem;">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 text-soft" style="font-size:.85rem;">
                                        {{ $order->received_at ? $order->received_at->format('d/m/Y') : '&mdash;' }}
                                    </td>
                                    <td class="py-3 text-end pe-3 fw-semibold text-white">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top:1px solid rgba(148,163,184,.2);">
                                <td colspan="5" class="text-end pe-3 py-3 text-soft fw-semibold">Total do período:</td>
                                <td class="py-3 text-end pe-3 fw-bold text-white">R$ {{ number_format($totalValue, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>

@push('scripts')
<script>
    const sel = document.getElementById('periodSelect');
    const fromEl = document.getElementById('customFrom');
    const toEl   = document.getElementById('customTo');
    function toggleCustom() {
        const show = sel.value === 'custom';
        fromEl.style.display = show ? '' : 'none';
        toEl.style.display   = show ? '' : 'none';
    }
    sel.addEventListener('change', toggleCustom);
    toggleCustom();
</script>
@endpush
@endsection
