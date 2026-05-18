@extends('layouts.app')

@section('title', 'Relatório Financeiro')

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
            <h4 class="mb-1 text-white"><i class="bi bi-graph-up-arrow me-2"></i>Relatório Financeiro</h4>
            <p class="text-soft mb-0">Receitas, despesas e saldo líquido por período.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.financial.pdf', request()->only(['period','from','to'])) }}" target="_blank"
               class="btn btn-sm btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.financial.csv', request()->only(['period','from','to'])) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('reports.financial') }}" class="row g-3 mb-4">
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

        {{-- Alerta de vencidos --}}
        @if($receivablesOverdue > 0 || $billsOverdue > 0)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" style="font-size:.85rem;">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span>
                @if($receivablesOverdue > 0)
                    Receitas vencidas: <strong>R$ {{ number_format($receivablesOverdue, 2, ',', '.') }}</strong>.
                @endif
                @if($billsOverdue > 0)
                    Despesas vencidas: <strong>R$ {{ number_format($billsOverdue, 2, ',', '.') }}</strong>.
                @endif
            </span>
        </div>
        @endif

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                    <div class="kpi-label">Receitas Recebidas</div>
                    <div class="kpi-value">R$ {{ number_format($receivablesPaid, 2, ',', '.') }}</div>
                    <div class="kpi-sub">Pendentes: R$ {{ number_format($receivablesPending, 2, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
                    <div class="kpi-label">Despesas Pagas</div>
                    <div class="kpi-value">R$ {{ number_format($billsPaid, 2, ',', '.') }}</div>
                    <div class="kpi-sub">Pendentes: R$ {{ number_format($billsPending, 2, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:{{ $netBalance >= 0 ? 'linear-gradient(135deg,#0f766e,#0d9488)' : 'linear-gradient(135deg,#b45309,#d97706)' }};">
                    <div class="kpi-label">Saldo Líquido</div>
                    <div class="kpi-value">R$ {{ number_format($netBalance, 2, ',', '.') }}</div>
                    <div class="kpi-sub">Receitas − Despesas pagas</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:{{ $projectedBalance >= 0 ? 'linear-gradient(135deg,#1d4ed8,#2563eb)' : 'linear-gradient(135deg,#7c3aed,#8b5cf6)' }};">
                    <div class="kpi-label">Saldo Projetado</div>
                    <div class="kpi-value">R$ {{ number_format($projectedBalance, 2, ',', '.') }}</div>
                    <div class="kpi-sub">Incluindo pendentes</div>
                </div>
            </div>
        </div>

        {{-- Tabelas --}}
        <div class="row g-4">

            {{-- Contas a Receber --}}
            <div class="col-12 col-lg-6">
                <div class="card card-dark-bg shadow-sm h-100">
                    <div class="card-header card-header-dark border-bottom">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-arrow-down-circle me-1 text-success"></i>Contas a Receber no Período
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead>
                                    <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                               color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                        <th class="ps-3 py-2">Descrição</th>
                                        <th class="py-2">Vencimento</th>
                                        <th class="py-2 text-end">Valor</th>
                                        <th class="py-2 text-center pe-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivables as $r)
                                    <tr style="border-color:rgba(148,163,184,.07);">
                                        <td class="ps-3 py-2 text-white" style="font-size:.85rem;">{{ $r->description }}</td>
                                        <td class="py-2 text-soft" style="font-size:.82rem;">
                                            {{ \Carbon\Carbon::parse($r->due_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2 text-end fw-semibold text-white">R$ {{ number_format($r->amount, 2, ',', '.') }}</td>
                                        <td class="py-2 text-center pe-3">
                                            @php $sc = ['recebido'=>'success','pendente'=>'warning','cancelado'=>'danger']; @endphp
                                            <span class="badge bg-{{ $sc[$r->status] ?? 'secondary' }} bg-opacity-25
                                                         text-{{ $sc[$r->status] ?? 'secondary' }}
                                                         border border-{{ $sc[$r->status] ?? 'secondary' }} border-opacity-25"
                                                  style="font-size:.72rem;">
                                                {{ ucfirst($r->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-soft">
                                            <i class="bi bi-inbox d-block fs-4 opacity-25 mb-1"></i>
                                            Nenhum lançamento no período.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contas a Pagar --}}
            <div class="col-12 col-lg-6">
                <div class="card card-dark-bg shadow-sm h-100">
                    <div class="card-header card-header-dark border-bottom">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-arrow-up-circle me-1 text-danger"></i>Contas a Pagar no Período
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead>
                                    <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                               color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                        <th class="ps-3 py-2">Descrição</th>
                                        <th class="py-2">Vencimento</th>
                                        <th class="py-2 text-end">Valor</th>
                                        <th class="py-2 text-center pe-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bills as $b)
                                    <tr style="border-color:rgba(148,163,184,.07);">
                                        <td class="ps-3 py-2 text-white" style="font-size:.85rem;">{{ $b->description }}</td>
                                        <td class="py-2 text-soft" style="font-size:.82rem;">
                                            {{ \Carbon\Carbon::parse($b->due_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2 text-end fw-semibold text-white">R$ {{ number_format($b->amount, 2, ',', '.') }}</td>
                                        <td class="py-2 text-center pe-3">
                                            @php $sc = ['pago'=>'success','pendente'=>'warning','cancelado'=>'danger']; @endphp
                                            <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }} bg-opacity-25
                                                         text-{{ $sc[$b->status] ?? 'secondary' }}
                                                         border border-{{ $sc[$b->status] ?? 'secondary' }} border-opacity-25"
                                                  style="font-size:.72rem;">
                                                {{ ucfirst($b->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-soft">
                                            <i class="bi bi-inbox d-block fs-4 opacity-25 mb-1"></i>
                                            Nenhum lançamento no período.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /row tabelas --}}
    </div>{{-- /card-body --}}
</div>{{-- /card --}}

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
