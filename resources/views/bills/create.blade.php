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

                {{-- Descrição --}}
                <div class="col-12 col-md-8">
                    <label class="form-label text-soft">Descrição <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control @error('description') is-invalid @enderror"
                           required value="{{ old('description') }}" placeholder="Ex: Aluguel Maio/2026">
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Valor --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Valor (R$) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                           required step="0.01" min="0.01" value="{{ old('amount') }}" placeholder="0,00">
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Vencimento --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Vencimento <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                           required value="{{ old('due_date') }}">
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Status --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pendente" {{ old('status','pendente') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="paga"     {{ old('status') == 'paga'     ? 'selected' : '' }}>Paga</option>
                        <option value="cancelada"{{ old('status') == 'cancelada'? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Categoria --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Categoria</label>
                    <select name="category" class="form-select">
                        <option value="">— Selecione —</option>
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Forma de Pagamento --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Forma de Pagamento</label>
                    <select name="payment_method" class="form-select">
                        <option value="">— Selecione —</option>
                        @foreach($paymentMethods as $val => $label)
                            <option value="{{ $val }}" {{ old('payment_method') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fornecedor --}}
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

                {{-- Parcelas --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Parcelas</label>
                    <input type="number" name="installments" class="form-control"
                           min="1" max="60" value="{{ old('installments', 1) }}" placeholder="1">
                    <div class="form-text text-soft">Deixe 1 para pagamento único.</div>
                </div>

                {{-- Recorrência --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Recorrência</label>
                    <select name="recurrence" class="form-select">
                        <option value="none"    {{ old('recurrence','none') == 'none'    ? 'selected' : '' }}>Sem recorrência</option>
                        <option value="monthly" {{ old('recurrence') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="weekly"  {{ old('recurrence') == 'weekly'  ? 'selected' : '' }}>Semanal</option>
                    </select>
                </div>

                {{-- Observações --}}
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
