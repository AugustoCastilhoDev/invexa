@extends('layouts.app')

@section('title', 'Relatório de Estoque')

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
            <h4 class="mb-1 text-white"><i class="bi bi-boxes me-2 text-success"></i>Relatório de Estoque</h4>
            <p class="text-soft mb-0">Produtos, saldos e alertas de estoque baixo.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.stock.pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.stock.csv', request()->query()) }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('reports.stock') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <label class="form-label text-soft" style="font-size:.78rem;">Filtrar</label>
                <select name="filter" class="form-select bg-dark text-white border-secondary" onchange="this.form.submit()">
                    <option value="all" @selected(request('filter','all')==='all')>Todos os produtos ativos</option>
                    <option value="low" @selected(request('filter')==='low')>Apenas estoque baixo</option>
                </select>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
                    <div class="kpi-label">Produtos Ativos</div>
                    <div class="kpi-value">{{ $totalActive }}</div>
                    <div class="kpi-sub">em estoque</div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
                    <div class="kpi-label">Estoque Baixo</div>
                    <div class="kpi-value">{{ $totalLow }}</div>
                    <div class="kpi-sub">abaixo do mínimo</div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#0f766e,#0d9488);">
                    <div class="kpi-label">Valor Total em Estoque</div>
                    <div class="kpi-value">R$ {{ number_format($totalValue, 2, ',', '.') }}</div>
                    <div class="kpi-sub">custo × quantidade</div>
                </div>
            </div>
        </div>

        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-table me-1"></i>Posição de Estoque
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                       color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                <th class="ps-3 py-2">Produto</th>
                                <th class="py-2">Categoria</th>
                                <th class="py-2 text-center">Qtd.</th>
                                <th class="py-2 text-center">Mínimo</th>
                                <th class="py-2 text-end">Custo Unit.</th>
                                <th class="py-2 text-end pe-3">Venda Unit.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $p)
                            @php $low = $p->min_quantity > 0 && $p->quantity <= $p->min_quantity; @endphp
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-3 py-2 text-white" style="font-size:.85rem;">
                                    {{ $p->name }}
                                    @if($low)
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 ms-1" style="font-size:.68rem;">Baixo</span>
                                    @endif
                                </td>
                                <td class="py-2 text-soft" style="font-size:.82rem;">{{ optional($p->category)->name ?? '—' }}</td>
                                <td class="py-2 text-center fw-semibold {{ $low ? 'text-danger' : 'text-white' }}">{{ $p->quantity }}</td>
                                <td class="py-2 text-center text-soft">{{ $p->min_quantity ?? '—' }}</td>
                                <td class="py-2 text-end text-soft" style="font-size:.82rem;">R$ {{ number_format($p->cost_price ?? 0, 2, ',', '.') }}</td>
                                <td class="py-2 text-end fw-semibold text-white pe-3">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-soft">
                                    <i class="bi bi-inbox d-block fs-4 opacity-25 mb-1"></i>
                                    Nenhum produto encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
