@extends('layouts.app')

@section('title', 'Relatório de Devoluções')

@push('styles')
<style>
.rep-card {
    background: rgba(13,20,35,.92);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .75rem;
    overflow: hidden;
}
.rep-card-header {
    background: rgba(8,13,26,.6);
    border-bottom: 1px solid rgba(148,163,184,.12);
    padding: .85rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.rep-kpi {
    background: rgba(15,23,42,.85);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .65rem;
    padding: 1rem;
    text-align: center;
}
.rep-kpi-label {
    font-size: .62rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: rgba(148,163,184,.65); margin-bottom: .35rem;
}
.rep-filters {
    background: rgba(13,20,35,.92);
    border: 1px solid rgba(148,163,184,.10);
    border-radius: .75rem;
    padding: 1rem 1.25rem;
}
.rep-filters .form-control,
.rep-filters .form-select {
    background: rgba(8,13,26,.8) !important;
    border: 1px solid rgba(148,163,184,.22) !important;
    color: #e2e8f0 !important;
    font-size: .82rem;
    border-radius: .45rem;
}
.rep-filters .form-control::placeholder { color: rgba(148,163,184,.5); }
.rep-filters .form-control:focus,
.rep-filters .form-select:focus {
    border-color: rgba(96,165,250,.45) !important;
    box-shadow: 0 0 0 .2rem rgba(96,165,250,.12) !important;
}
.rep-filters option,
.rep-filters .form-select option { background: #0d1424; color: #e2e8f0; }
.rep-filters label { font-size: .75rem; color: rgba(148,163,184,.75); margin-bottom: .3rem; }
.rep-table thead th {
    font-size: .62rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: rgba(148,163,184,.75) !important;
    border-bottom: 1px solid rgba(148,163,184,.18) !important;
    background: rgba(8,13,26,.6) !important;
    padding: .65rem .75rem; white-space: nowrap;
}
.rep-table tbody td {
    font-size: .82rem; color: #cbd5e1 !important;
    border-color: rgba(148,163,184,.06) !important;
    vertical-align: middle; padding: .55rem .75rem;
    background: transparent !important;
}
.rep-table tbody tr:hover td { background: rgba(96,165,250,.04) !important; }
.rep-table tbody tr:last-child td { border-bottom: 0 !important; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 text-white fw-semibold">
            <i class="bi bi-arrow-return-left me-2" style="color:#fbbf24;"></i>Relatório de Devoluções
        </h4>
        <p class="mb-0" style="font-size:.78rem;color:rgba(148,163,184,.65);">Devoluções registradas no período selecionado</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('reports.returns.csv', request()->query()) }}"
           class="btn btn-sm" style="background:rgba(74,222,128,.12);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
        </a>
        <a href="{{ route('reports.returns.pdf', request()->query()) }}" target="_blank"
           class="btn btn-sm" style="background:rgba(248,113,113,.12);color:#f87171;border:1px solid rgba(248,113,113,.3);">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="rep-filters mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
            <label>Período</label>
            <select name="period" class="form-select" onchange="toggleCustom(this.value)">
                @foreach(['week'=>'Esta semana','month'=>'Este mês','quarter'=>'Este trimestre','year'=>'Este ano','custom'=>'Personalizado'] as $val=>$lbl)
                    <option value="{{ $val }}" {{ $period===$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2" id="fromGroup" style="{{ $period==='custom'?'':'display:none;' }}">
            <label>De</label>
            <input type="date" name="from" class="form-control" value="{{ request('from', $from->format('Y-m-d')) }}">
        </div>
        <div class="col-6 col-md-2" id="toGroup" style="{{ $period==='custom'?'':'display:none;' }}">
            <label>Até</label>
            <input type="date" name="to" class="form-control" value="{{ request('to', $to->format('Y-m-d')) }}">
        </div>
        <div class="col-12 col-md-3">
            <label>Motivo</label>
            <select name="reason" class="form-select">
                <option value="">Todos os motivos</option>
                @foreach($reasons as $r)
                    <option value="{{ $r }}" {{ request('reason')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel me-1"></i>Filtrar
            </button>
        </div>
    </div>
</form>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="rep-kpi">
            <div class="rep-kpi-label">Total Devoluções</div>
            <div class="fs-3 fw-bold text-white">{{ $totalReturns }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="rep-kpi">
            <div class="rep-kpi-label">Itens Devolvidos</div>
            <div class="fs-3 fw-bold" style="color:#fbbf24;">{{ $totalItems }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="rep-kpi">
            <div class="rep-kpi-label">Valor Devolvido</div>
            <div class="fs-3 fw-bold" style="color:#f87171;">R$ {{ number_format($totalValue, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="rep-kpi">
            <div class="rep-kpi-label">Ticket Médio</div>
            <div class="fs-3 fw-bold" style="color:#38bdf8;">
                R$ {{ $totalReturns > 0 ? number_format($totalValue / $totalReturns, 2, ',', '.') : '0,00' }}
            </div>
        </div>
    </div>
</div>

{{-- Tabela --}}
<div class="rep-card">
    <div class="rep-card-header">
        <h6 class="mb-0 text-white fw-semibold">
            <i class="bi bi-table me-2" style="color:#fbbf24;"></i>Devoluções
        </h6>
        <span style="background:rgba(251,191,36,.12);color:#fbbf24;border:1px solid rgba(251,191,36,.25);
                     font-size:.68rem;font-weight:600;padding:.2rem .6rem;border-radius:999px;">
            {{ $totalReturns }} registro(s)
        </span>
    </div>
    <div>
        @if($returns->isEmpty())
            <div class="text-center py-5" style="color:rgba(148,163,184,.5);">
                <i class="bi bi-inbox" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                Nenhuma devolução encontrada no período.
            </div>
        @else
        <div class="table-responsive">
            <table class="table mb-0 rep-table">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Data</th>
                        <th>Venda Orig.</th>
                        <th>Cliente</th>
                        <th>Motivo</th>
                        <th>Itens</th>
                        <th class="text-end">Valor</th>
                        <th class="pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $r)
                    @php
                        $statusMap = [
                            'pendente'  => ['#fbbf24', 'rgba(251,191,36,.12)',  'rgba(251,191,36,.30)',  'Pendente'],
                            'aprovada'  => ['#4ade80', 'rgba(74,222,128,.12)',  'rgba(74,222,128,.30)',  'Aprovada'],
                            'rejeitada' => ['#f87171', 'rgba(248,113,113,.12)', 'rgba(248,113,113,.30)', 'Rejeitada'],
                            'concluida' => ['#38bdf8', 'rgba(56,189,248,.12)',  'rgba(56,189,248,.30)',  'Concluída'],
                        ];
                        [$sc, $sbg, $sbd, $sl] = $statusMap[$r->status] ?? ['#94a3b8', 'rgba(148,163,184,.10)', 'rgba(148,163,184,.22)', ucfirst($r->status)];
                        $valor = $r->items->sum(fn($i) => $i->quantity * $i->price);
                    @endphp
                    <tr>
                        <td class="ps-3" style="color:#94a3b8;">#{{ $r->id }}</td>
                        <td>{{ $r->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($r->sale)
                                <a href="{{ route('sales.show', $r->sale_id) }}" style="color:#60a5fa;">#{{ $r->sale_id }}</a>
                            @else
                                <span style="color:rgba(148,163,184,.5);">—</span>
                            @endif
                        </td>
                        <td>{{ $r->sale?->customer?->name ?? 'Consumidor' }}</td>
                        <td>{{ ucfirst($r->reason ?? '—') }}</td>
                        <td>{{ $r->items->sum('quantity') }}</td>
                        <td class="text-end" style="color:#f87171;font-weight:600;">
                            R$ {{ number_format($valor, 2, ',', '.') }}
                        </td>
                        <td class="pe-3">
                            <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.68rem;font-weight:600;
                                         padding:.2rem .55rem;border-radius:999px;
                                         background:{{ $sbg }};color:{{ $sc }};border:1px solid {{ $sbd }};">
                                <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc }};flex-shrink:0;"></span>
                                {{ $sl }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCustom(v) {
    const show = v === 'custom';
    document.getElementById('fromGroup').style.display = show ? '' : 'none';
    document.getElementById('toGroup').style.display   = show ? '' : 'none';
}
</script>
@endpush
