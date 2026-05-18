@extends('layouts.app')

@section('title', 'Relatório Financeiro')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Relatório Financeiro</h4>
    </div>

    {{-- Filtros de período --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('reports.financial') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Período</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="week"    {{ $period === 'week'    ? 'selected' : '' }}>Esta semana</option>
                        <option value="month"   {{ $period === 'month'   ? 'selected' : '' }}>Este mês</option>
                        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Este trimestre</option>
                        <option value="year"    {{ $period === 'year'    ? 'selected' : '' }}>Este ano</option>
                        <option value="custom"  {{ $period === 'custom'  ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>
                @if($period === 'custom')
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">De</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Até</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
                @endif
                <div class="col-md-3 ms-auto text-end">
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
                    </small>
                </div>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted fw-semibold mb-1">Receitas Recebidas</div>
                    <div class="h4 fw-bold text-success">R$ {{ number_format($receivablesPaid, 2, ',', '.') }}</div>
                    <div class="small text-muted">Pendentes: R$ {{ number_format($receivablesPending, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted fw-semibold mb-1">Despesas Pagas</div>
                    <div class="h4 fw-bold text-danger">R$ {{ number_format($billsPaid, 2, ',', '.') }}</div>
                    <div class="small text-muted">Pendentes: R$ {{ number_format($billsPending, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted fw-semibold mb-1">Saldo Líquido (Realizado)</div>
                    <div class="h4 fw-bold {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        R$ {{ number_format($netBalance, 2, ',', '.') }}
                    </div>
                    <div class="small text-muted">Receitas - Despesas pagas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted fw-semibold mb-1">Saldo Projetado</div>
                    <div class="h4 fw-bold {{ $projectedBalance >= 0 ? 'text-primary' : 'text-warning' }}">
                        R$ {{ number_format($projectedBalance, 2, ',', '.') }}
                    </div>
                    <div class="small text-muted">Incluindo pendentes</div>
                </div>
            </div>
        </div>
    </div>

    @if($receivablesOverdue > 0 || $billsOverdue > 0)
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Atenção:</strong>
        @if($receivablesOverdue > 0) Há <strong>R$ {{ number_format($receivablesOverdue, 2, ',', '.') }}</strong> em receitas vencidas. @endif
        @if($billsOverdue > 0) Há <strong>R$ {{ number_format($billsOverdue, 2, ',', '.') }}</strong> em despesas vencidas. @endif
    </div>
    @endif

    {{-- Tabelas lado a lado --}}
    <div class="row g-4">
        {{-- Receitas --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-semibold mb-0"><span class="text-success me-2">●</span>Contas a Receber no Período</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Descrição</th>
                                    <th>Vencimento</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receivables as $r)
                                <tr>
                                    <td class="ps-4">{{ $r->description }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->due_date)->format('d/m/Y') }}</td>
                                    <td class="text-end">R$ {{ number_format($r->amount, 2, ',', '.') }}</td>
                                    <td class="text-center pe-4">
                                        @php
                                            $sc = ['recebido' => 'success', 'pendente' => 'warning', 'vencido' => 'danger'];
                                        @endphp
                                        <span class="badge bg-{{ $sc[$r->status] ?? 'secondary' }}">{{ ucfirst($r->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Nenhum lançamento no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Despesas --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-semibold mb-0"><span class="text-danger me-2">●</span>Contas a Pagar no Período</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Descrição</th>
                                    <th>Vencimento</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bills as $b)
                                <tr>
                                    <td class="ps-4">{{ $b->description }}</td>
                                    <td>{{ \Carbon\Carbon::parse($b->due_date)->format('d/m/Y') }}</td>
                                    <td class="text-end">R$ {{ number_format($b->amount, 2, ',', '.') }}</td>
                                    <td class="text-center pe-4">
                                        @php
                                            $sc = ['pago' => 'success', 'pendente' => 'warning', 'vencido' => 'danger'];
                                        @endphp
                                        <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Nenhum lançamento no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
