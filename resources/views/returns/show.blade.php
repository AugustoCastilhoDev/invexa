@extends('layouts.app')

@section('title', 'Devolução #' . $return->id)

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">Devolução #{{ $return->id }}</h4>
                <p class="text-soft mb-0">
                    Vinculada à
                    <a href="{{ route('sales.show', $return->sale_id) }}" class="text-info text-decoration-none">
                        Venda #{{ $return->sale_id }}
                    </a>
                    &mdash; {{ $return->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                </p>
            </div>
            <a href="{{ route('returns.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
        </div>
    </div>

    <div class="card-body">

        {{-- Resumo --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Cliente</p>
                        <p class="text-white fw-semibold mb-0">{{ $return->sale->customer_name ?? 'Não informado' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Motivo</p>
                        <p class="text-white fw-semibold mb-0">{{ $return->reason_label }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Registrado por</p>
                        <p class="text-white fw-semibold mb-0">{{ $return->user->name ?? 'Sistema' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Valor estornado</p>
                        <p class="text-danger fw-bold fs-5 mb-0">- R$ {{ number_format($return->total, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Itens devolvidos --}}
        <div class="card card-dark-bg border border-secondary mb-4">
            <div class="card-header card-header-dark">
                <span class="text-white fw-semibold">Itens Devolvidos</span>
            </div>
            <div class="table-responsive">
                <table class="table table-dark mb-0 align-middle">
                    <thead>
                        <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                   color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                            <th class="ps-4 py-3">Produto</th>
                            <th class="py-3">Quantidade</th>
                            <th class="py-3">Preço Unit.</th>
                            <th class="py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $item)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-4 py-3 text-white fw-semibold">{{ $item->product->name ?? 'Produto removido' }}</td>
                            <td class="py-3" style="color:#94a3b8;">{{ $item->quantity }} un.</td>
                            <td class="py-3" style="color:#94a3b8;">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td class="py-3 text-danger fw-bold">- R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="border-top:1px solid rgba(148,163,184,.2);">
                            <td colspan="3" class="ps-4 py-3 text-soft fw-semibold text-end">Total estornado</td>
                            <td class="py-3 text-danger fw-bold fs-6">- R$ {{ number_format($return->total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($return->notes)
        <div class="card card-dark-bg border border-secondary">
            <div class="card-body">
                <p class="text-soft mb-1" style="font-size:.78rem;">Observações</p>
                <p class="text-white mb-0">{{ $return->notes }}</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
