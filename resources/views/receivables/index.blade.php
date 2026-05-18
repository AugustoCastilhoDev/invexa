@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1"><i class="bi bi-cash-coin me-2 text-success"></i>Contas a Receber</h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">Gerencie seus recebimentos e cobranças.</p>
    </div>
    <a href="{{ route('receivables.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Nova Conta
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.3);color:#4ade80;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-3">
                <p class="text-soft mb-1" style="font-size:.75rem;">A RECEBER</p>
                <p class="text-warning fw-bold fs-6 mb-0">R$ {{ number_format($totalPending, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border border-danger h-100">
            <div class="card-body py-3">
                <p class="text-soft mb-1" style="font-size:.75rem;">VENCIDAS</p>
                <p class="text-danger fw-bold fs-6 mb-0">R$ {{ number_format($totalOverdue, 2, ',', '.') }}</p>
                @if($countOverdue > 0)
                    <small class="text-danger">{{ $countOverdue }} conta(s) em atraso</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border border-success h-100">
            <div class="card-body py-3">
                <p class="text-soft mb-1" style="font-size:.75rem;">RECEBIDO (PERÍODO)</p>
                <p class="text-success fw-bold fs-6 mb-0">R$ {{ number_format($totalReceived, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-3">
                <p class="text-soft mb-1" style="font-size:.75rem;">TOTAL REGISTRADO</p>
                <p class="text-white fw-bold fs-6 mb-0">{{ $receivables->total() }} conta(s)</p>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card card-dark-bg border border-secondary mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('receivables.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Buscar descrição ou cliente..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos os status</option>
                    @foreach($statuses as $val => $label)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="category" class="form-select form-select-sm">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $val => $label)
                        <option value="{{ $val }}" {{ request('category') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Baixa em Lote --}}
<form method="POST" action="{{ route('receivables.bulk-receive') }}" id="bulkForm">
    @csrf
    @foreach(request()->except(['_token']) as $key => $value)
        @if(!in_array($key, ['ids','received_at','payment_method']))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    {{-- Barra de ações em lote --}}
    <div id="bulkBar" class="card card-dark-bg border mb-3 d-none" style="border-color:rgba(34,197,94,.3);">
        <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center gap-3">
            <span id="bulkCount" class="text-success fw-semibold" style="font-size:.85rem;">0 selecionada(s)</span>
            <div class="d-flex align-items-center gap-2">
                <label class="text-soft" style="font-size:.78rem;">Data receb.:</label>
                <input type="date" name="received_at" class="form-control form-control-sm" style="width:140px;" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-soft" style="font-size:.78rem;">Forma:</label>
                <select name="payment_method" class="form-select form-select-sm" style="width:160px;" required>
                    @foreach(\App\Models\Receivable::PAYMENT_METHODS as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-sm ms-auto" onclick="return confirm('Confirmar baixa dos recebimentos selecionados?')">
                <i class="bi bi-check2-all me-1"></i>Confirmar Recebimento em Lote
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAll">Desmarcar tudo</button>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card card-dark-bg border border-secondary">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-3 py-3" style="width:36px;">
                            <input type="checkbox" id="checkAll" class="form-check-input" title="Selecionar todos">
                        </th>
                        <th class="py-3">Descrição</th>
                        <th class="py-3">Categoria</th>
                        <th class="py-3">Cliente</th>
                        <th class="py-3">Vencimento</th>
                        <th class="py-3">Valor</th>
                        <th class="py-3">Recebido</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receivables as $rec)
                    @php
                        $isOverdue         = $rec->status === 'vencida';
                        $isPendingOrOverdue = in_array($rec->status, ['pendente','vencida']);
                        $rowBg             = $isOverdue ? 'background:rgba(239,68,68,.07);' : '';
                        $textMain          = $isOverdue ? '#fca5a5' : '#f1f5f9';
                        $textSub           = $isOverdue ? '#f87171' : '#94a3b8';
                    @endphp
                    <tr style="border-color:rgba(148,163,184,.07); {{ $rowBg }}">
                        <td class="ps-3 py-3">
                            @if($isPendingOrOverdue)
                                <input type="checkbox" name="ids[]" value="{{ $rec->id }}"
                                       class="form-check-input row-check"
                                       style="border-color:rgba(148,163,184,.4);">
                            @endif
                        </td>
                        <td class="ps-2 py-3">
                            <span style="color:{{ $textMain }};font-weight:600;">{{ $rec->description }}</span>
                            @if($rec->sale_id)
                                <br><small style="color:{{ $textSub }};font-size:.72rem;"><i class="bi bi-basket3 me-1"></i>Venda #{{ $rec->sale_id }}</small>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="badge" style="background:{{ $isOverdue ? 'rgba(239,68,68,.25)' : 'rgba(100,116,139,.35)' }};color:{{ $isOverdue ? '#fca5a5' : '#cbd5e1' }};">
                                {{ $rec->category_label }}
                            </span>
                        </td>
                        <td class="py-3" style="color:{{ $textSub }};font-size:.85rem;">{{ $rec->customer->name ?? '-' }}</td>
                        <td class="py-3" style="font-size:.85rem;">
                            <span style="color:{{ $isOverdue ? '#f87171' : '#94a3b8' }};font-weight:{{ $isOverdue ? '700' : '400' }};">
                                {{ $rec->due_date->format('d/m/Y') }}
                                @if($isOverdue)<i class="bi bi-exclamation-triangle-fill ms-1"></i>@endif
                            </span>
                        </td>
                        <td class="py-3" style="color:{{ $textMain }};font-weight:600;">R$ {{ number_format($rec->amount, 2, ',', '.') }}</td>
                        <td class="py-3" style="font-size:.85rem;">
                            @if($rec->amount_received > 0)
                                <span class="text-success">R$ {{ number_format($rec->amount_received, 2, ',', '.') }}</span>
                            @else
                                <span style="color:{{ $textSub }}">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="badge bg-{{ $rec->status_color }}">{{ $rec->status_label }}</span>
                        </td>
                        <td class="py-3 text-end pe-4">
                            <a href="{{ route('receivables.show', $rec) }}" class="btn btn-sm"
                               style="{{ $isOverdue ? 'border:1px solid rgba(239,68,68,.5);color:#fca5a5;' : 'border:1px solid rgba(34,197,94,.4);color:#4ade80;' }}">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-soft">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>Nenhuma conta encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($receivables->hasPages())
        <div class="card-footer" style="background:rgba(15,23,42,.6);border-top:1px solid rgba(148,163,184,.1);">
            {{ $receivables->links() }}
        </div>
        @endif
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll  = document.getElementById('checkAll');
    const bulkBar   = document.getElementById('bulkBar');
    const bulkCount = document.getElementById('bulkCount');
    const deselect  = document.getElementById('deselectAll');

    function updateBar() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        bulkBar.classList.toggle('d-none', checked === 0);
        bulkCount.textContent = checked + ' selecionada(s)';
    }

    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
        updateBar();
    });

    document.querySelectorAll('.row-check').forEach(cb =>
        cb.addEventListener('change', updateBar)
    );

    deselect.addEventListener('click', function () {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
        checkAll.checked = false;
        updateBar();
    });
});
</script>
@endsection
