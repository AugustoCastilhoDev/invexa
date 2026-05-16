@extends('layouts.app')

@section('title', 'Devoluções')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">

    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h4 class="mb-1 text-white">Devoluções</h4>
                <p class="text-soft mb-0">Histórico de devoluções e estornos financeiros.</p>
            </div>
            <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-return-left me-1"></i>Nova Devolução
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('returns.index') }}" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label text-soft" style="font-size:.78rem;">De</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label text-soft" style="font-size:.78rem;">Até</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Limpar</a>
                </div>
            </div>
        </form>

        {{-- Cards resumo --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card card-dark-bg border border-secondary">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Total de devoluções</p>
                        <p class="text-white fw-bold fs-5 mb-0">{{ $countReturns }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-dark-bg border border-secondary">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Valor total devolvido</p>
                        <p class="text-danger fw-bold fs-5 mb-0">R$ {{ number_format($totalReturned, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="table-responsive">
            @if($returns->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-arrow-return-left fs-1 text-soft"></i>
                    <p class="text-soft mt-3">Nenhuma devolução registrada.</p>
                </div>
            @else
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-4 py-3">#</th>
                        <th class="py-3">Venda</th>
                        <th class="py-3">Cliente</th>
                        <th class="py-3">Motivo</th>
                        <th class="py-3">Itens</th>
                        <th class="py-3">Valor Devolvido</th>
                        <th class="py-3">Registrado por</th>
                        <th class="py-3">Data</th>
                        <th class="py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $ret)
                    <tr style="border-color:rgba(148,163,184,.07);">
                        <td class="ps-4 py-3" style="color:#94a3b8;">#{{ $ret->id }}</td>
                        <td class="py-3">
                            <a href="{{ route('sales.show', $ret->sale_id) }}"
                               class="text-white text-decoration-none fw-semibold">
                                Venda #{{ $ret->sale_id }}
                            </a>
                        </td>
                        <td class="py-3" style="color:#94a3b8;">
                            {{ $ret->sale->customer_name ?? 'Não informado' }}
                        </td>
                        <td class="py-3" style="color:#94a3b8;">{{ $ret->reason_label }}</td>
                        <td class="py-3" style="color:#94a3b8;">{{ $ret->items->count() }} item(ns)</td>
                        <td class="py-3 fw-bold text-danger">- R$ {{ number_format($ret->total, 2, ',', '.') }}</td>
                        <td class="py-3" style="color:#94a3b8;font-size:.82rem;">{{ $ret->user->name ?? 'Sistema' }}</td>
                        <td class="py-3" style="color:#94a3b8;font-size:.82rem;">
                            {{ $ret->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3">
                            <a href="{{ route('returns.show', $ret) }}" class="btn btn-outline-light btn-sm">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        @if($returns->hasPages())
            <div class="mt-3">{{ $returns->links() }}</div>
        @endif
    </div>
</div>
@endsection
