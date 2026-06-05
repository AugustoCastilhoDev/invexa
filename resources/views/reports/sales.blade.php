@extends('layouts.app')

@section('title', 'Relatório de Vendas')

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
            <h4 class="mb-1 text-white"><i class="bi bi-cart-check me-2 text-primary"></i>Relatório de Vendas</h4>
            <p class="text-soft mb-0">Histórico e totais de vendas por período.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.sales.pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.sales.csv', request()->query()) }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <label class="form-label text-soft" style="font-size:.78rem;">Período</label>
                <select name="period" id="periodSelect" class="form-select bg-dark text-white border-secondary" onchange="this.form.submit()">
                    @foreach(['week' => 'Esta semana', 'month' => 'Este mês', 'quarter' => 'Este trimestre', 'year' => 'Este ano', 'custom' => 'Período personalizado'] as $val => $label)
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
            @if($period === 'custom')
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
            @endif
        </form>

        <p class="text-soft mb-4" style="font-size:.82rem;">
            <i class="bi bi-calendar-range me-1"></i>
            Exibindo: <strong class="text-white">{{ $from->format('d/m/Y') }}</strong>
            até <strong class="text-white">{{ $to->format('d/m/Y') }}</strong>
        </p>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
                    <div class="kpi-label">Total de Vendas</div>
                    <div class="kpi-value">{{ $totalSales }}</div>
                    <div class="kpi-sub">no período</div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                    <div class="kpi-label">Receita Total</div>
                    <div class="kpi-value">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
                    <div class="kpi-sub">excluindo canceladas</div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
                    <div class="kpi-label">Canceladas</div>
                    <div class="kpi-value">{{ $totalCanceled }}</div>
                    <div class="kpi-sub">no período</div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-table me-1"></i>Vendas no Período
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                       color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                <th class="ps-3 py-2">#</th>
                                <th class="py-2">Cliente</th>
                                <th class="py-2">Data</th>
                                <th class="py-2 text-end">Total</th>
                                <th class="py-2 text-center pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $s)
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-3 py-2 text-soft" style="font-size:.82rem;">#{{ $s->id }}</td>
                                <td class="py-2 text-white" style="font-size:.85rem;">{{ optional($s->customer)->name ?? 'Consumidor' }}</td>
                                <td class="py-2 text-soft" style="font-size:.82rem;">{{ $s->created_at->format('d/m/Y') }}</td>
                                <td class="py-2 text-end fw-semibold text-white">R$ {{ number_format($s->total, 2, ',', '.') }}</td>
                                <td class="py-2 text-center pe-3">
                                    @php $sc = ['concluida'=>'success','pendente'=>'warning','cancelada'=>'danger']; @endphp
                                    <span class="badge bg-{{ $sc[$s->status] ?? 'secondary' }} bg-opacity-25
                                                 text-{{ $sc[$s->status] ?? 'secondary' }}
                                                 border border-{{ $sc[$s->status] ?? 'secondary' }} border-opacity-25"
                                          style="font-size:.72rem;">
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-soft">
                                    <i class="bi bi-inbox d-block fs-4 opacity-25 mb-1"></i>
                                    Nenhuma venda no período.
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
