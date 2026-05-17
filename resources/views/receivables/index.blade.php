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
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="category" class="form-select form-select-sm">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $val => $label)
                        <option value="{{ $val }}" {{ request('category') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="from" class="form-control form-control-sm"
                       value="{{ request('from') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="to" class="form-control form-control-sm"
                       value="{{ request('to') }}">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabela --}}
<div class="card card-dark-bg border border-secondary">
    <div class="table-responsive">
        <table class="table table-dark table-hover mb-0 align-middle">
            <thead>
                <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                           color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                    <th class="ps-4 py-3">Descrição</th>
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
                    $isOverdue = $rec->status === 'vencida';
                    $rowBg     = $isOverdue ? 'background:rgba(239,68,68,.13);' : '';
                    $textMain  = $isOverdue ? '#fca5a5' : '#f1f5f9';
                    $textSub   = $isOverdue ? '#f87171' : '#94a3b8';
                @endphp
                <tr style="border-color:rgba(148,163,184,.07); {{ $rowBg }}">

                    {{-- Descrição --}}
                    <td class="ps-4 py-3">
                        <span style="color:{{ $textMain }};font-weight:600;">{{ $rec->description }}</span>
                        @if($rec->sale_id)
                            <br><small style="color:{{ $textSub }};font-size:.72rem;">
                                <i class="bi bi-basket3 me-1"></i>Venda #{{ $rec->sale_id }}
                            </small>
                        @endif
                    </td>

                    {{-- Categoria --}}
                    <td class="py-3">
                        <span class="badge"
                              style="background:{{ $isOverdue ? 'rgba(239,68,68,.25)' : 'rgba(100,116,139,.35)' }};
                                     color:{{ $isOverdue ? '#fca5a5' : '#cbd5e1' }};">
                            {{ $rec->category_label }}
                        </span>
                    </td>

                    {{-- Cliente --}}
                    <td class="py-3" style="color:{{ $textSub }};font-size:.85rem;">
                        {{ $rec->customer->name ?? '-' }}
                    </td>

                    {{-- Vencimento --}}
                    <td class="py-3" style="font-size:.85rem;">
                        <span style="color:{{ $isOverdue ? '#f87171' : '#94a3b8' }};
                                     font-weight:{{ $isOverdue ? '700' : '400' }};">
                            {{ $rec->due_date->format('d/m/Y') }}
                            @if($isOverdue)<i class="bi bi-exclamation-triangle-fill ms-1"></i>@endif
                        </span>
                    </td>

                    {{-- Valor --}}
                    <td class="py-3" style="color:{{ $textMain }};font-weight:600;">
                        R$ {{ number_format($rec->amount, 2, ',', '.') }}
                    </td>

                    {{-- Recebido --}}
                    <td class="py-3" style="font-size:.85rem;">
                        @if($rec->amount_received > 0)
                            <span class="text-success">R$ {{ number_format($rec->amount_received, 2, ',', '.') }}</span>
                        @else
                            <span style="color:{{ $textSub }}">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="py-3">
                        <span class="badge bg-{{ $rec->status_color }}">{{ $rec->status_label }}</span>
                    </td>

                    {{-- Ações --}}
                    <td class="py-3 text-end pe-4">
                        <a href="{{ route('receivables.show', $rec) }}"
                           class="btn btn-sm"
                           style="{{ $isOverdue
                               ? 'border:1px solid rgba(239,68,68,.5);color:#fca5a5;'
                               : 'border:1px solid rgba(34,197,94,.4);color:#4ade80;' }}">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-soft">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                        Nenhuma conta encontrada.
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
@endsection
