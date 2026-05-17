@extends('layouts.app')

@section('title', 'Editar Conta a Receber')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 text-white">Editar Conta #{{ $receivable->id }}</h4>
                <p class="text-soft mb-0">{{ $receivable->description }}</p>
            </div>
            <a href="{{ route('receivables.show', $receivable) }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('receivables.update', $receivable) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label text-soft">Descrição <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" required
                           value="{{ old('description', $receivable->description) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Valor (R$) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" required step="0.01" min="0.01"
                           value="{{ old('amount', $receivable->amount) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Vencimento <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control" required
                           value="{{ old('due_date', $receivable->due_date->format('Y-m-d')) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Categoria <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" required>
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ old('category', $receivable->category) == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Cliente</label>
                    <select name="customer_id" class="form-select">
                        <option value="">— Nenhum —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $receivable->customer_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label text-soft">Observações</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $receivable->notes) }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Salvar Alterações
                </button>
                <a href="{{ route('receivables.show', $receivable) }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
