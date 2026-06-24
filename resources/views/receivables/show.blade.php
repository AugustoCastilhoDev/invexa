@extends('layouts.app')

@section('title', 'Conta a Receber #' . $receivable->id)

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h4 class="mb-1 text-white">Conta a Receber #{{ $receivable->id }}</h4>
                <p class="text-soft mb-0">{{ $receivable->description }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                {{-- Botão PDF --}}
                <a href="{{ route('receivables.pdf', $receivable) }}"
                   target="_blank"
                   class="btn btn-sm"
                   style="background:rgba(14,165,233,.15);border:1px solid rgba(14,165,233,.4);color:#38bdf8;"
                   title="Gerar PDF de cobrança">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF / Imprimir
                </a>

                @if(!in_array($receivable->status, ['recebida', 'cancelada']))
                    <a href="{{ route('receivables.edit', $receivable) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form action="{{ route('receivables.cancel', $receivable) }}" method="POST"
                          onsubmit="return confirm('Cancelar esta conta?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                    </form>
                @endif
                @if(auth()->user()->hasLegacyRole(['admin']))
                <form action="{{ route('receivables.destroy', $receivable) }}" method="POST"
                      onsubmit="return confirm('Excluir esta conta permanentemente?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i>Excluir
                    </button>
                </form>
                @endif
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
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
                        <p class="text-white fw-bold fs-5 mb-0">R$ {{ number_format($receivable->amount, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Recebido</p>
                        <p class="text-success fw-bold fs-5 mb-0">R$ {{ number_format($receivable->amount_received, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border {{ $receivable->balance > 0 ? 'border-warning' : 'border-success' }} h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Saldo Restante</p>
                        <p class="{{ $receivable->balance > 0 ? 'text-warning' : 'text-success' }} fw-bold fs-5 mb-0">
                            R$ {{ number_format($receivable->balance, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.75rem;">Status</p>
                        <span class="badge bg-{{ $receivable->status_color }} fs-6">
                            {{ $receivable->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Detalhes --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body">
                        <p class="text-soft fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;">Detalhes</p>
                        <dl class="row mb-0" style="font-size:.9rem;">
                            <dt class="col-5 text-soft">Categoria</dt>
                            <dd class="col-7 text-white">{{ $receivable->category_label }}</dd>

                            <dt class="col-5 text-soft">Vencimento</dt>
                            <dd class="col-7 {{ $receivable->status === 'vencida' ? 'text-danger fw-bold' : 'text-white' }}">
                                {{ $receivable->due_date->format('d/m/Y') }}
                                @if($receivable->status === 'vencida')
                                    <i class="bi bi-exclamation-triangle-fill ms-1"></i>
                                @endif
                            </dd>

                            @if($receivable->received_at)
                            <dt class="col-5 text-soft">Data Recebimento</dt>
                            <dd class="col-7 text-white">{{ $receivable->received_at->format('d/m/Y') }}</dd>
                            @endif

                            @if($receivable->payment_method)
                            <dt class="col-5 text-soft">Forma de Pag.</dt>
                            <dd class="col-7 text-white">{{ $receivable->payment_method_label }}</dd>
                            @endif

                            <dt class="col-5 text-soft">Cliente</dt>
                            <dd class="col-7 text-white">
                                @if($receivable->customer)
                                    <a href="{{ route('customers.show', $receivable->customer) }}"
                                       style="color:#60a5fa;text-decoration:none;">
                                        {{ $receivable->customer->name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </dd>

                            @if($receivable->sale_id)
                            <dt class="col-5 text-soft">Venda Vinculada</dt>
                            <dd class="col-7">
                                <a href="{{ route('sales.show', $receivable->sale_id) }}"
                                   style="color:#60a5fa;text-decoration:none;">
                                    #{{ $receivable->sale_id }}
                                </a>
                            </dd>
                            @endif

                            @if($receivable->installment_number && $receivable->installments)
                            <dt class="col-5 text-soft">Parcela</dt>
                            <dd class="col-7 text-white">{{ $receivable->installment_number }}/{{ $receivable->installments }}</dd>
                            @endif

                            @if($receivable->recurrence && $receivable->installment_number)
                            <dt class="col-5 text-soft">Recorrência</dt>
                            <dd class="col-7 text-white">{{ $receivable->installment_number }}/{{ $receivable->recurrence }}</dd>
                            @endif

                            @if($receivable->notes)
                            <dt class="col-5 text-soft">Observações</dt>
                            <dd class="col-7 text-white">{{ $receivable->notes }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Formulário de recebimento --}}
            @if(!in_array($receivable->status, ['recebida', 'cancelada']))
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-success h-100">
                    <div class="card-body">
                        <p class="text-success fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;">
                            <i class="bi bi-cash-coin me-1"></i>Registrar Recebimento
                        </p>
                        @if($errors->any())
                            <div class="alert alert-danger py-2">
                                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <form action="{{ route('receivables.receive', $receivable) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-soft small">Valor Recebido (R$) <span class="text-danger">*</span></label>
                                <input type="number" name="amount_received" class="form-control" required
                                       step="0.01" min="0.01"
                                       max="{{ $receivable->balance }}"
                                       value="{{ old('amount_received', number_format($receivable->balance, 2, '.', '')) }}"
                                       placeholder="0,00">
                                <div class="form-text text-soft">Saldo restante: R$ {{ number_format($receivable->balance, 2, ',', '.') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-soft small">Data do Recebimento <span class="text-danger">*</span></label>
                                <input type="date" name="received_at" class="form-control" required
                                       value="{{ old('received_at', today()->format('Y-m-d')) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-soft small">Forma de Pagamento <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach(\App\Models\Receivable::PAYMENT_METHODS as $val => $label)
                                        <option value="{{ $val }}" {{ old('payment_method') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-circle me-1"></i>Confirmar Recebimento
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-{{ $receivable->status_color }} h-100 d-flex align-items-center justify-content-center text-center">
                    <div class="card-body">
                        <i class="bi bi-{{ $receivable->status === 'recebida' ? 'check-circle-fill text-success' : 'x-circle-fill text-secondary' }} display-4"></i>
                        <p class="mt-3 fw-semibold text-white">{{ $receivable->status_label }}</p>
                        @if($receivable->received_at)
                            <p class="text-soft small">Recebido em {{ $receivable->received_at->format('d/m/Y') }} via {{ $receivable->payment_method_label }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
