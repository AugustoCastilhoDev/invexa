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
            <div class="d-flex gap-2 flex-wrap">
                @if($sale->status === 'concluida')
                    <a href="{{ route('returns.create', ['sale_id' => $sale->id]) }}"
                       class="btn btn-warning btn-sm">
                        <i class="bi bi-arrow-return-left me-1"></i>Registrar Devolução
                    </a>
                @endif

                @if(auth()->user()->hasRole(['admin','gerente']))
                    @if($sale->status !== 'cancelada')
                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        <form action="{{ route('sales.cancel', $sale) }}" method="POST"
                              onsubmit="return confirm('Cancelar esta venda e estornar o estoque?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Cancelar Venda
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                          onsubmit="return confirm('Mover esta venda para a lixeira?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Lixeira
                        </button>
                    </form>
                @endif

                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
            </div>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($sale->status === 'cancelada')
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <div>
                    <strong>Venda cancelada.</strong>
                    O estoque dos produtos foi estornado automaticamente no momento do cancelamento.
                </div>
            </div>
        @endif

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
                            {{ $sale->sale_date?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '-' }}
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
                        <p class="text-soft mb-1" style="font-size:.78rem;">Total da Venda</p>
                        <p class="text-white fw-semibold mb-0 fs-5">R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @php $saleReturns = $sale->saleReturns ?? collect(); @endphp
        @if($saleReturns->isNotEmpty())
        <div class="card card-dark-bg border border-warning mb-4">
            <div class="card-header card-header-dark" style="border-bottom:1px solid rgba(234,179,8,.25);">
                <span class="text-warning fw-semibold">
                    <i class="bi bi-arrow-return-left me-1"></i>Devoluções desta venda
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-dark mb-0 align-middle">
                    <thead>
                        <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                   color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Motivo</th>
                            <th class="py-3">Itens</th>
                            <th class="py-3">Valor</th>
                            <th class="py-3">Data</th>
                            <th class="py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($saleReturns as $ret)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-4 py-3" style="color:#94a3b8;">#{{ $ret->id }}</td>
                            <td class="py-3" style="color:#94a3b8;">{{ $ret->reason_label }}</td>
                            <td class="py-3" style="color:#94a3b8;">{{ ($ret->items ?? collect())->count() }} item(ns)</td>
                            <td class="py-3 text-danger fw-bold">- R$ {{ number_format($ret->total, 2, ',', '.') }}</td>
                            <td class="py-3" style="color:#94a3b8;font-size:.82rem;">
                                {{ $ret->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3">
                                <a href="{{ route('returns.show', $ret) }}" class="btn btn-outline-warning btn-sm">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="border-top:1px solid rgba(234,179,8,.2);">
                            <td colspan="3" class="ps-4 py-3 text-soft fw-semibold text-end">Total devolvido</td>
                            <td class="py-3 text-danger fw-bold">- R$ {{ number_format($sale->total_returned, 2, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="ps-4 py-2 text-soft fw-semibold text-end">Líquido da venda</td>
                            <td class="py-2 text-success fw-bold">R$ {{ number_format($sale->net_total, 2, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

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
                        @forelse($sale->items ?? collect() as $item)
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-4 py-3 text-white fw-semibold">
                                    {{ $item->product->name ?? 'Produto removido' }}
                                </td>
                                <td class="py-3" style="color:#94a3b8;">{{ $item->quantity }} un.</td>
                                <td class="py-3" style="color:#94a3b8;">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td class="py-3 text-white fw-semibold">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-soft">Nenhum item encontrado nesta venda.</td>
                            </tr>
                        @endforelse
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

        @if ($sale->notes)
            <div class="card card-dark-bg border border-secondary mb-4">
                <div class="card-body">
                    <p class="text-soft mb-1" style="font-size:.78rem;">Observações</p>
                    <p class="text-white mb-0">{{ $sale->notes }}</p>
                </div>
            </div>
        @endif

        @if(auth()->user()->hasRole(['admin','gerente']))
        <div class="d-flex gap-2 mt-4 flex-wrap">
            @if($sale->status !== 'cancelada')
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>Editar Venda
                </a>
                <form action="{{ route('sales.cancel', $sale) }}" method="POST"
                      onsubmit="return confirm('Cancelar esta venda e estornar o estoque?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-warning">
                        <i class="bi bi-x-circle me-1"></i>Cancelar Venda
                    </button>
                </form>
            @endif

            <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                  onsubmit="return confirm('Mover esta venda para a lixeira?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Mover para Lixeira
                </button>
            </form>

            @if(auth()->user()->hasRole(['admin']))
                <form action="{{ route('sales.force-destroy', $sale->id) }}" method="POST"
                      onsubmit="return confirm('ATENÇÃO: Excluir permanentemente esta venda? Esta ação não pode ser desfeita.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3-fill me-1"></i>Excluir Permanente
                    </button>
                </form>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection
