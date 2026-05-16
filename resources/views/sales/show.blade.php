@extends('layouts.app')

@section('title', 'Detalhes da Venda')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">Detalhes da Venda #{{ $sale->id }}</h4>
                <p class="text-soft mb-0">Informações completas sobre esta transação.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning btn-sm">Editar</a>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
            </div>
        </div>
    </div>

    <div class="card-body">

        {{-- Cards de resumo --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Cliente</p>
                        <p class="text-white fw-semibold mb-0">{{ $sale->customer_name ?? 'Sem nome' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Data</p>
                        <p class="text-white fw-semibold mb-0">
                            {{ $sale->sale_date ? $sale->sale_date->timezone(config('app.timezone'))->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Status</p>
                        @php
                            $badge = match ($sale->status) {
                                'concluida' => 'success',
                                'pendente'  => 'warning',
                                'cancelada' => 'danger',
                                default     => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucfirst($sale->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Total</p>
                        <p class="text-white fw-semibold mb-0 fs-5">R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela de itens --}}
        <div class="card card-dark-bg border border-secondary mb-4">
            <div class="card-header card-header-dark">
                <span class="text-white fw-semibold">Itens da Venda</span>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                   color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                            <th class="ps-4 py-3">Produto</th>
                            <th class="py-3">Quantidade</th>
                            <th class="py-3">Preço Unitário</th>
                            <th class="py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-4 py-3 text-white fw-semibold">
                                    {{ $item->product->name ?? 'Produto removido' }}
                                </td>
                                <td class="py-3" style="color:#94a3b8;">{{ $item->quantity }} un.</td>
                                <td class="py-3" style="color:#94a3b8;">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td class="py-3 text-white fw-semibold">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="border-top:1px solid rgba(148,163,184,.2);">
                            <td colspan="3" class="ps-4 py-3 text-soft fw-semibold text-end">Total</td>
                            <td class="py-3 text-white fw-bold fs-6">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Observações --}}
        @if ($sale->notes)
            <div class="card card-dark-bg border border-secondary">
                <div class="card-body">
                    <p class="text-soft mb-1" style="font-size:.78rem;">Observações</p>
                    <p class="text-white mb-0">{{ $sale->notes }}</p>
                </div>
            </div>
        @endif

        {{-- Ações --}}
        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning">Editar Venda</a>

            <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                  onsubmit="return confirm('Tem certeza que deseja excluir esta venda?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir Venda</button>
            </form>
        </div>

    </div>
</div>
@endsection
