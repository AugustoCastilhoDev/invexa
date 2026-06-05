@extends('layouts.app')

@section('title', 'Nova Conta a Receber')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 text-white">Nova Conta a Receber</h4>
                <p class="text-soft mb-0">Registre um valor a receber manualmente.</p>
            </div>
            <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('receivables.store') }}" method="POST">
            @csrf
            <div class="row g-3">

                {{-- Descrição --}}
                <div class="col-12 col-md-8">
                    <label class="form-label text-soft">Descrição <span class="text-danger">*</span></label>
                    <input type="text" name="description"
                           class="form-control bg-dark text-white border-secondary @error('description') is-invalid @enderror"
                           value="{{ old('description') }}" placeholder="Ex: Cobrança cliente João - Junho">
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Valor --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Valor (R$) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           class="form-control bg-dark text-white border-secondary @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" placeholder="0,00">
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Vencimento --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft">Vencimento <span class="text-danger">*</span></label>
                    <input type="date" name="due_date"
                           class="form-control bg-dark text-white border-secondary @error('due_date') is-invalid @enderror"
                           value="{{ old('due_date') }}">
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Categoria --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft">Categoria <span class="text-danger">*</span></label>
                    <select name="category"
                            class="form-select bg-dark text-white border-secondary @error('category') is-invalid @enderror">
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ old('category', 'vendas') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Status --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft">Status <span class="text-danger">*</span></label>
                    <select name="status"
                            class="form-select bg-dark text-white border-secondary @error('status') is-invalid @enderror">
                        @foreach($statuses as $val => $label)
                            <option value="{{ $val }}" {{ old('status', 'pendente') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Cliente --}}
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft">Cliente</label>
                    <select name="customer_id"
                            class="form-select bg-dark text-white border-secondary @error('customer_id') is-invalid @enderror">
                        <option value="">— Nenhum —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Forma de Pagamento --}}
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Forma de Pagamento</label>
                    <select name="payment_method"
                            class="form-select bg-dark text-white border-secondary">
                        <option value="">Não informado</option>
                        <option value="pix"      {{ old('payment_method') === 'pix'       ? 'selected' : '' }}>PIX</option>
                        <option value="dinheiro" {{ old('payment_method') === 'dinheiro'  ? 'selected' : '' }}>Dinheiro</option>
                        <option value="cartao"   {{ old('payment_method') === 'cartao'    ? 'selected' : '' }}>Cartão</option>
                        <option value="boleto"   {{ old('payment_method') === 'boleto'    ? 'selected' : '' }}>Boleto</option>
                        <option value="ted"      {{ old('payment_method') === 'ted'       ? 'selected' : '' }}>TED/DOC</option>
                    </select>
                </div>

                {{-- Parcelas --}}
                <div class="col-6 col-md-2">
                    <label class="form-label text-soft">Parcelas</label>
                    <input type="number" name="installments" min="1" max="60"
                           class="form-control bg-dark text-white border-secondary"
                           value="{{ old('installments', 1) }}" placeholder="1">
                </div>

                {{-- Recorrência --}}
                <div class="col-6 col-md-3">
                    <label class="form-label text-soft">Recorrência</label>
                    <select name="recurrence" class="form-select bg-dark text-white border-secondary">
                        <option value="none"    {{ old('recurrence', 'none') === 'none'    ? 'selected' : '' }}>Sem recorrência</option>
                        <option value="monthly" {{ old('recurrence') === 'monthly'         ? 'selected' : '' }}>Mensal</option>
                        <option value="weekly"  {{ old('recurrence') === 'weekly'          ? 'selected' : '' }}>Semanal</option>
                    </select>
                </div>

                {{-- Observações --}}
                <div class="col-12">
                    <label class="form-label text-soft">Observações</label>
                    <textarea name="notes" class="form-control bg-dark text-white border-secondary" rows="3"
                              placeholder="Informações adicionais...">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Salvar Conta
                </button>
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
