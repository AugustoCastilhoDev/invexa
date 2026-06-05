@extends('layouts.app')

@section('title', 'Conta: ' . Str::limit($bill->description, 40))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">{{ $bill->description }}</h1>
        <p class="text-soft mb-0">Conta a Pagar &mdash; {{ $bill->category_label }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if($bill->status === 'pendente')
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#markAsPaidModal">
                <i class="bi bi-check-circle me-1"></i>Marcar como Paga
            </button>
        @endif
        @if($bill->status !== 'paga')
            <a href="{{ route('bills.edit', $bill) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
        @endif
        <form action="{{ route('bills.destroy', $bill) }}" method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i>Excluir
            </button>
        </form>
        <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Detalhes da Conta --}}
    <div class="col-12 col-lg-5">
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-info-circle me-1"></i>Informações
                </span>
            </div>
            <div class="card-body">
                @php $color = $bill->status_color; @endphp
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Status</dt>
                    <dd class="col-7 py-2 border-bottom border-secondary">
                        <span class="badge bg-{{ $color }} bg-opacity-25 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                            {{ $bill->status_label }}
                        </span>
                        @if($bill->isOverdue())
                            <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 ms-1">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Vencida
                            </span>
                        @endif
                    </dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Categoria</dt>
                    <dd class="col-7 text-white py-2 border-bottom border-secondary">{{ $bill->category_label }}</dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Valor</dt>
                    <dd class="col-7 fw-bold text-white py-2 border-bottom border-secondary">R$ {{ number_format($bill->amount, 2, ',', '.') }}</dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Vencimento</dt>
                    <dd class="col-7 py-2 border-bottom border-secondary
                        {{ $bill->isOverdue() ? 'text-danger fw-semibold' : 'text-white' }}">
                        {{ $bill->due_date->format('d/m/Y') }}
                    </dd>

                    @if($bill->status === 'paga')
                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Pago em</dt>
                    <dd class="col-7 text-success py-2 border-bottom border-secondary">
                        {{ $bill->paid_at ? \Carbon\Carbon::parse($bill->paid_at)->format('d/m/Y') : '—' }}
                    </dd>

                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Valor Pago</dt>
                    <dd class="col-7 text-success fw-semibold py-2 border-bottom border-secondary">
                        R$ {{ number_format($bill->amount_paid ?? 0, 2, ',', '.') }}
                    </dd>

                    @php $diff = ($bill->amount_paid ?? 0) - $bill->amount; @endphp
                    @if(abs($diff) > 0.01)
                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Diferença</dt>
                    <dd class="col-7 py-2 border-bottom border-secondary {{ $diff > 0 ? 'text-success' : 'text-warning' }}">
                        {{ $diff > 0 ? '+' : '' }}R$ {{ number_format(abs($diff), 2, ',', '.') }}
                        <small class="text-soft ms-1">{{ $diff > 0 ? '(excedente)' : '(déficit)' }}</small>
                    </dd>
                    @endif
                    @endif

                    @if($bill->supplier)
                    <dt class="col-5 text-soft fw-normal py-2 border-bottom border-secondary">Fornecedor</dt>
                    <dd class="col-7 py-2 border-bottom border-secondary">
                        <a href="{{ route('suppliers.show', $bill->supplier) }}" class="text-info text-decoration-none">
                            {{ $bill->supplier->name }}
                        </a>
                    </dd>
                    @endif

                    @if($bill->notes)
                    <dt class="col-5 text-soft fw-normal py-2">Observações</dt>
                    <dd class="col-7 text-soft py-2">{{ $bill->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Card de pagamento / status visual --}}
    <div class="col-12 col-lg-7">
        @if($bill->status === 'pendente')
        <div class="card card-dark-bg shadow-sm border border-warning border-opacity-25">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-clock-history text-warning" style="font-size:3rem;"></i>
                </div>
                <h5 class="text-white mb-1">Pagamento Pendente</h5>
                <p class="text-soft mb-1">
                    Vencimento: <strong class="{{ $bill->isOverdue() ? 'text-danger' : 'text-white' }}">{{ $bill->due_date->format('d/m/Y') }}</strong>
                </p>
                @if($bill->isOverdue())
                    <p class="text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-1"></i>Esta conta está vencida há {{ $bill->due_date->diffInDays(now()) }} dia(s).</p>
                @else
                    <p class="text-soft mb-3">Vence em {{ now()->diffInDays($bill->due_date) }} dia(s).</p>
                @endif
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markAsPaidModal">
                    <i class="bi bi-check-circle me-1"></i>Registrar Pagamento
                </button>
            </div>
        </div>
        @elseif($bill->status === 'paga')
        <div class="card card-dark-bg shadow-sm border border-success border-opacity-25">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
                </div>
                <h5 class="text-white mb-1">Conta Paga</h5>
                <p class="text-soft mb-0">
                    Pago em <strong class="text-success">{{ $bill->paid_at ? \Carbon\Carbon::parse($bill->paid_at)->format('d/m/Y') : '—' }}</strong>
                </p>
                <p class="text-soft">Valor pago: <strong class="text-success">R$ {{ number_format($bill->amount_paid ?? 0, 2, ',', '.') }}</strong></p>
            </div>
        </div>
        @elseif($bill->status === 'cancelada')
        <div class="card card-dark-bg shadow-sm border border-secondary border-opacity-25">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-x-circle text-secondary" style="font-size:3rem;"></i>
                </div>
                <h5 class="text-white mb-1">Conta Cancelada</h5>
                <p class="text-soft mb-0">Esta conta foi cancelada.</p>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal: Marcar como Paga --}}
@if($bill->status === 'pendente')
<div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white" id="markAsPaidModalLabel">
                    <i class="bi bi-check-circle me-2 text-success"></i>Registrar Pagamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bills.pay', $bill) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-soft fw-semibold" style="font-size:.8rem;">Valor Pago (R$) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-soft">R$</span>
                            <input type="number" name="amount_paid" step="0.01" min="0.01"
                                   class="form-control form-control-dark"
                                   value="{{ number_format($bill->amount, 2, '.', '') }}"
                                   required>
                        </div>
                        <small class="text-soft">Valor original: R$ {{ number_format($bill->amount, 2, ',', '.') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-soft fw-semibold" style="font-size:.8rem;">Data do Pagamento <span class="text-danger">*</span></label>
                        <input type="date" name="paid_at"
                               class="form-control form-control-dark"
                               value="{{ now()->format('Y-m-d') }}"
                               required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
