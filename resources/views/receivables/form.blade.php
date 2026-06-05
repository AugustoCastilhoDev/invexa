@extends('layouts.app')

@section('title', isset($receivable) ? 'Editar Conta a Receber' : 'Nova Conta a Receber')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1">
            <i class="bi bi-cash-coin me-2 text-success"></i>
            {{ isset($receivable) ? 'Editar Conta a Receber' : 'Nova Conta a Receber' }}
        </h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">
            {{ isset($receivable) ? 'Atualize os dados da conta.' : 'Registre um novo recebimento ou cobrança.' }}
        </p>
    </div>
    <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4"
         style="background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3);color:#fca5a5;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Corrija os erros abaixo:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-dark-bg border border-secondary" style="max-width:700px;">
    <div class="card-body p-4">
        <form method="POST"
              action="{{ isset($receivable) ? route('receivables.update', $receivable) : route('receivables.store') }}">
            @csrf
            @if(isset($receivable))
                @method('PUT')
            @endif

            {{-- Descrição --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Descrição <span class="text-danger">*</span>
                </label>
                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
                       placeholder="Ex: Pagamento de cliente, Mensalidade..."
                       value="{{ old('description', $receivable->description ?? '') }}" required>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Cliente --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Cliente
                </label>
                <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                    <option value="">— Nenhum cliente vinculado —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ old('customer_id', $receivable->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Categoria --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Categoria
                </label>
                <select name="category" class="form-select @error('category') is-invalid @enderror">
                    <option value="">— Selecione uma categoria —</option>
                    @foreach(\App\Models\Receivable::CATEGORY_LABELS as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('category', $receivable->category ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Valor + Vencimento --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        Valor (R$) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(30,41,59,.8);border-color:rgba(148,163,184,.2);color:#94a3b8;">R$</span>
                        <input type="number" name="amount" step="0.01" min="0.01"
                               class="form-control @error('amount') is-invalid @enderror"
                               placeholder="0,00"
                               value="{{ old('amount', isset($receivable) ? number_format($receivable->amount, 2, '.', '') : '') }}"
                               required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        Vencimento <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="due_date"
                           class="form-control @error('due_date') is-invalid @enderror"
                           value="{{ old('due_date', isset($receivable) ? $receivable->due_date->format('Y-m-d') : '') }}"
                           required>
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Observações --}}
            <div class="mb-4">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Observações
                </label>
                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Informações adicionais (opcional)...">{{ old('notes', $receivable->notes ?? '') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Botões --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ isset($receivable) ? 'Salvar Alterações' : 'Criar Conta' }}
                </button>
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
