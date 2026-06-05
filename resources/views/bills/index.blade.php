@extends('layouts.app')

@section('title', 'Contas a Pagar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1"><i class="bi bi-credit-card-2-front me-2 text-danger"></i>Contas a Pagar</h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">Gerencie seus compromissos financeiros.</p>
    </div>
    <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">
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
                <p class="text-soft mb-1" style="font-size:.75rem;">A PAGAR</p>
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
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-3">
                <p class="text-soft mb-1" style="font-size:.75rem;">PAGO (PERÍODO)</p>
                <p class="text-success fw-bold fs-6 mb-0">R$ {{ number_format($totalPaid, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-3">
                <p class="text-soft mb-1" style="font-size:.75rem;">TOTAL REGISTRADO</p>
                <p class="text-white fw-bold fs-6 mb-0">{{ $bills->total() }} conta(s)</p>
            </div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card card-dark-bg border border-secondary mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('bills.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Buscar descrição ou fornecedor..."
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
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Baixa em Lote --}}
<form method="POST" action="{{ route('bills.bulk-pay') }}" id="bulkForm">
    @csrf
    @foreach(request()->except(['_token']) as $key => $value)
        @if(!in_array($key, ['ids','paid_at','payment_method']))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    {{-- Barra de ações em lote (visível quando algo selecionado) --}}
    <div id="bulkBar" class="card card-dark-bg border mb-3 d-none" style="border-color:rgba(251,191,36,.3);">
        <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center gap-3">
            <span id="bulkCount" class="text-warning fw-semibold" style="font-size:.85rem;">0 selecionada(s)</span>
            <div class="d-flex align-items-center gap-2">
                <label class="text-soft" style="font-size:.78rem;">Data pgto:</label>
                <input type="date" name="paid_at" class="form-control form-control-sm" style="width:140px;" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-soft" style="font-size:.78rem;">Forma:</label>
                <select name="payment_method" class="form-select form-select-sm" style="width:160px;" required>
                    @foreach(\App\Models\Bill::PAYMENT_METHODS as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-warning btn-sm ms-auto" onclick="return confirm('Confirmar baixa das contas selecionadas?')">
                <i class="bi bi-check2-all me-1"></i>Dar Baixa em Lote
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
                        <th class="py-3">Fornecedor</th>
                        <th class="py-3">Vencimento</th>
                        <th class="py-3">Valor</th>
                        <th class="py-3">Pago</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    @php $isPendingOrOverdue = in_array($bill->status, ['pendente','vencida']); @endphp
                    <tr style="border-color:rgba(148,163,184,.07);
                               {{ $bill->status === 'vencida' ? 'background:rgba(239,68,68,.07);' : '' }}">
                        <td class="ps-3 py-3">
                            @if($isPendingOrOverdue)
                                <input type="checkbox" name="ids[]" value="{{ $bill->id }}"
                                       class="form-check-input row-check"
                                       style="border-color:rgba(148,163,184,.4);">
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="text-white fw-semibold">{{ $bill->description }}</span>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-secondary bg-opacity-50">{{ $bill->category_label }}</span>
                        </td>
                        <td class="py-3" style="color:#94a3b8;font-size:.85rem;">{{ $bill->supplier->name ?? '-' }}</td>
                        <td class="py-3" style="font-size:.85rem;">
                            @php $isOverdue = $bill->status === 'vencida'; @endphp
                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-soft' }}">
                                {{ $bill->due_date->format('d/m/Y') }}
                                @if($isOverdue)<i class="bi bi-exclamation-triangle-fill ms-1"></i>@endif
                            </span>
                        </td>
                        <td class="py-3 text-white fw-semibold">R$ {{ number_format($bill->amount, 2, ',', '.') }}</td>
                        <td class="py-3" style="font-size:.85rem;">
                            @if($bill->amount_paid > 0)
                                <span class="text-success">R$ {{ number_format($bill->amount_paid, 2, ',', '.') }}</span>
                            @else
                                <span class="text-soft">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="badge bg-{{ $bill->status_color }}">{{ $bill->status_label }}</span>
                        </td>
                        <td class="py-3 text-end pe-4">
                            <a href="{{ route('bills.show', $bill) }}" class="btn btn-outline-primary btn-sm">Ver</a>
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
        @if($bills->hasPages())
        <div class="card-footer" style="background:rgba(15,23,42,.6);border-top:1px solid rgba(148,163,184,.1);">
            {{ $bills->links() }}
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
