@extends('layouts.app')

@section('title', 'Relatório de Devoluções')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 text-white fw-semibold"><i class="bi bi-arrow-return-left me-2 text-warning"></i>Relatório de Devoluções</h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">Devoluções registradas no período selecionado</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('reports.returns.csv', request()->query()) }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
        </a>
        <a href="{{ route('reports.returns.pdf', request()->query()) }}" target="_blank" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="card card-dark-bg border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label text-soft">Período</label>
                <select name="period" class="form-select" onchange="toggleCustom(this.value)">
                    @foreach(['week'=>'Esta semana','month'=>'Este mês','quarter'=>'Este trimestre','year'=>'Este ano','custom'=>'Personalizado'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ $period===$val?'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2" id="fromGroup" style="{{ $period==='custom'?'':'display:none;' }}">
                <label class="form-label text-soft">De</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', $from->format('Y-m-d')) }}">
            </div>
            <div class="col-6 col-md-2" id="toGroup" style="{{ $period==='custom'?'':'display:none;' }}">
                <label class="form-label text-soft">Até</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', $to->format('Y-m-d')) }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label text-soft">Motivo</label>
                <select name="reason" class="form-select">
                    <option value="">Todos os motivos</option>
                    @foreach($reasons as $r)
                        <option value="{{ $r }}" {{ request('reason')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
        </div>
    </div>
</form>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border-0 text-center py-3">
            <div class="text-soft" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Total Devoluções</div>
            <div class="fs-3 fw-bold text-white">{{ $totalReturns }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border-0 text-center py-3">
            <div class="text-soft" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Itens Devolvidos</div>
            <div class="fs-3 fw-bold text-warning">{{ $totalItems }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border-0 text-center py-3">
            <div class="text-soft" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Valor Devolvido</div>
            <div class="fs-3 fw-bold text-danger">R$ {{ number_format($totalValue, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border-0 text-center py-3">
            <div class="text-soft" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Ticket Médio</div>
            <div class="fs-3 fw-bold" style="color:#38bdf8;">R$ {{ $totalReturns > 0 ? number_format($totalValue / $totalReturns, 2, ',', '.') : '0,00' }}</div>
        </div>
    </div>
</div>

{{-- Tabela --}}
<div class="card card-dark-bg border-0">
    <div class="card-header card-header-dark border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-white fw-semibold"><i class="bi bi-table me-2 text-warning"></i>Devoluções</h6>
        <span class="badge" style="background:rgba(234,179,8,.15);color:#fbbf24;font-size:.75rem;">{{ $totalReturns }} registro(s)</span>
    </div>
    <div class="card-body p-0">
        @if($returns->isEmpty())
            <div class="text-center py-5 text-soft">
                <i class="bi bi-inbox" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-2 mb-0">Nenhuma devolução encontrada no período.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Venda Orig.</th>
                        <th>Cliente</th>
                        <th>Motivo</th>
                        <th>Itens</th>
                        <th class="text-end">Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $r)
                    @php
                        $statusMap = [
                            'pendente'  => ['warning', 'Pendente'],
                            'aprovada'  => ['success', 'Aprovada'],
                            'rejeitada' => ['danger',  'Rejeitada'],
                            'concluida' => ['info',    'Concluída'],
                        ];
                        [$sc, $sl] = $statusMap[$r->status] ?? ['secondary', ucfirst($r->status)];
                    @endphp
                    <tr>
                        <td class="text-soft">#{{ $r->id }}</td>
                        <td>{{ $r->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($r->sale)
                                <a href="{{ route('sales.show', $r->sale_id) }}" class="text-primary">#{{ $r->sale_id }}</a>
                            @else
                                <span class="text-soft">—</span>
                            @endif
                        </td>
                        <td>{{ $r->sale?->customer?->name ?? 'Consumidor' }}</td>
                        <td>{{ ucfirst($r->reason ?? '—') }}</td>
                        <td>{{ $r->items->sum('quantity') }}</td>
                        <td class="text-end text-danger fw-semibold">R$ {{ number_format($r->items->sum(fn($i) => $i->quantity * $i->unit_price), 2, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $sc }} bg-opacity-15 text-{{ $sc }}" style="font-size:.72rem;">{{ $sl }}</span></td>
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
