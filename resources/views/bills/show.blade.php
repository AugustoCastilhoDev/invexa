@extends('layouts.app')

@section('title', 'Conta #' . $bill->id)

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h4 class="mb-1 text-white">Conta #{{ $bill->id }}</h4>
                <p class="text-soft mb-0">{{ $bill->description }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(!in_array($bill->status, ['paga', 'cancelada']))
                    <a href="{{ route('bills.edit', $bill) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form action="{{ route('bills.cancel', $bill) }}" method="POST"
                          onsubmit="return confirm('Cancelar esta conta?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                    </form>
                @endif
                @if(auth()->user()->hasRole(['admin']))
                <form action="{{ route('bills.destroy', $bill) }}" method="POST"
                      onsubmit="return confirm('Excluir esta conta permanentemente?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i>Excluir
                    </button>
                </form>
                @endif
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
            </div>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Valor Total</p>
                        <p class="text-white fw-bold fs-5 mb-0">R$ {{ number_format($bill->amount, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Valor Pago</p>
                        <p class="text-success fw-bold fs-5 mb-0">R$ {{ number_format($bill->amount_paid, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border {{ $bill->balance > 0 ? 'border-warning' : 'border-success' }} h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Saldo Restante</p>
                        <p class="{{ $bill->balance > 0 ? 'text-warning' : 'text-success' }} fw-bold fs-5 mb-0">
                            R$ {{ number_format($bill->balance, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Status</p>
                        <span class="badge bg-{{ $bill->status_color }} fs-6">
                            {{ $bill->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detalhes --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body">
                        <p class="text-soft fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;">Detalhes</p>
                        <dl class="row mb-0" style="font-size:.9rem;">
                            <dt class="col-5 text-soft">Categoria</dt>
                            <dd class="col-7 text-white">{{ $bill->category_label }}</dd>

                            <dt class="col-5 text-soft">Vencimento</dt>
                            <dd class="col-7 {{ $bill->status === 'vencida' ? 'text-danger fw-bold' : 'text-white' }}">
                                {{ $bill->due_date->format('d/m/Y') }}
                                @if($bill->status === 'vencida')
                                    <i class="bi bi-exclamation-triangle-fill ms-1"></i>
                                @endif
                            </dd>

                            @if($bill->paid_at)
                            <dt class="col-5 text-soft">Data Pagamento</dt>
                            <dd class="col-7 text-white">{{ $bill->paid_at->format('d/m/Y') }}</dd>
                            @endif

                            @if($bill->payment_method)
                            <dt class="col-5 text-soft">Forma de Pag.</dt>
                            <dd class="col-7 text-white">{{ $bill->payment_method_label }}</dd>
                            @endif

                            <dt class="col-5 text-soft">Fornecedor</dt>
                            <dd class="col-7 text-white">{{ $bill->supplier->name ?? '—' }}</dd>

                            @if($bill->notes)
                            <dt class="col-5 text-soft">Observações</dt>
                            <dd class="col-7 text-white">{{ $bill->notes }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Formulário de pagamento --}}
            @if(!in_array($bill->status, ['paga', 'cancelada']))
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-success h-100">
                    <div class="card-body">
                        <p class="text-success fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;">
                            <i class="bi bi-cash-stack me-1"></i>Registrar Pagamento
                        </p>
                        @if($errors->any())
                            <div class="alert alert-danger py-2">
                                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <form action="{{ route('bills.pay', $bill) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-soft small">Valor Pago (R$) <span class="text-danger">*</span></label>
                                <input type="number" name="amount_paid" class="form-control" required
                                       step="0.01" min="0.01"
                                       max="{{ $bill->balance }}"
                                       value="{{ old('amount_paid', number_format($bill->balance, 2, '.', '')) }}"
                                       placeholder="0,00">
                                <div class="form-text text-soft">Saldo restante: R$ {{ number_format($bill->balance, 2, ',', '.') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-soft small">Data do Pagamento <span class="text-danger">*</span></label>
                                <input type="date" name="paid_at" class="form-control" required
                                       value="{{ old('paid_at', today()->format('Y-m-d')) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-soft small">Forma de Pagamento <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach(\App\Models\Bill::PAYMENT_METHODS as $val => $label)
                                        <option value="{{ $val }}" {{ old('payment_method') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-circle me-1"></i>Confirmar Pagamento
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-{{ $bill->status_color }} h-100 d-flex align-items-center justify-content-center text-center">
                    <div class="card-body">
                        <i class="bi bi-{{ $bill->status === 'paga' ? 'check-circle-fill text-success' : 'x-circle-fill text-secondary' }} display-4"></i>
                        <p class="mt-3 fw-semibold text-white">{{ $bill->status_label }}</p>
                        @if($bill->paid_at)
                            <p class="text-soft small">Pago em {{ $bill->paid_at->format('d/m/Y') }} via {{ $bill->payment_method_label }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
