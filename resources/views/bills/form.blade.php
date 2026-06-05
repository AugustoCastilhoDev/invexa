@extends('layouts.app')

@section('title', isset($bill) ? 'Editar Conta' : 'Nova Conta a Pagar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-white">{{ isset($bill) ? 'Editar Conta' : 'Nova Conta a Pagar' }}</h1>
        <p class="text-soft mb-0">{{ isset($bill) ? 'Atualize os dados da conta' : 'Registre uma nova conta a pagar' }}</p>
    </div>
    <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-receipt me-1"></i>Dados da Conta
                </span>
            </div>
            <div class="card-body">
                <form action="{{ isset($bill) ? route('bills.update', $bill) : route('bills.store') }}" method="POST">
                    @csrf
                    @if(isset($bill))
                        @method('PUT')
                    @endif

                    {{-- Descrição --}}
                    <div class="mb-3">
                        <label for="description" class="form-label text-soft fw-semibold" style="font-size:.8rem;">Descrição <span class="text-danger">*</span></label>
                        <input type="text" id="description" name="description"
                               class="form-control form-control-dark @error('description') is-invalid @enderror"
                               value="{{ old('description', $bill->description ?? '') }}"
                               placeholder="Ex: Aluguel, Conta de luz, Fatura fornecedor..."
                               required>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Categoria --}}
                    <div class="mb-3">
                        <label for="category" class="form-label text-soft fw-semibold" style="font-size:.8rem;">Categoria</label>
                        <select id="category" name="category"
                                class="form-select form-control-dark @error('category') is-invalid @enderror">
                            <option value="">— Selecione —</option>
                            @foreach(\App\Models\Bill::CATEGORY_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $bill->category ?? '') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Valor e Vencimento --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="amount" class="form-label text-soft fw-semibold" style="font-size:.8rem;">Valor (R$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-soft">R$</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                       class="form-control form-control-dark @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', isset($bill) ? number_format($bill->amount, 2, '.', '') : '') }}"
                                       placeholder="0,00" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="due_date" class="form-label text-soft fw-semibold" style="font-size:.8rem;">Vencimento <span class="text-danger">*</span></label>
                            <input type="date" id="due_date" name="due_date"
                                   class="form-control form-control-dark @error('due_date') is-invalid @enderror"
                                   value="{{ old('due_date', isset($bill) ? $bill->due_date->format('Y-m-d') : '') }}"
                                   required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div class="mb-4">
                        <label for="notes" class="form-label text-soft fw-semibold" style="font-size:.8rem;">Observações</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="form-control form-control-dark @error('notes') is-invalid @enderror"
                                  placeholder="Informações adicionais sobre esta conta...">{{ old('notes', $bill->notes ?? '') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>{{ isset($bill) ? 'Salvar Alterações' : 'Criar Conta' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
