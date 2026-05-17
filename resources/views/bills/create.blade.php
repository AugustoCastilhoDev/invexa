@extends('layouts.app')

@section('title', 'Nova Conta a Pagar')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 text-white">Nova Conta a Pagar</h4>
                <p class="text-soft mb-0">Registre um novo compromisso financeiro.</p>
            </div>
            <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('bills.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label text-soft">Descrição <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" required
                           value="{{ old('description') }}" placeholder="Ex: Aluguel Maio/2026">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Valor (R$) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" required step="0.01" min="0.01"
                           value="{{ old('amount') }}" placeholder="0,00">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Vencimento <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control" required
                           value="{{ old('due_date') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Categoria <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" required>
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Fornecedor</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">— Nenhum —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label text-soft">Observações</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Informações adicionais...">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Salvar Conta
                </button>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
