@extends('layouts.app')

@section('title', isset($bill) ? 'Editar Conta' : 'Nova Conta a Pagar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1">
            <i class="bi bi-credit-card me-2 text-danger"></i>
            {{ isset($bill) ? 'Editar Conta' : 'Nova Conta a Pagar' }}
        </h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">
            {{ isset($bill) ? 'Atualize os dados da conta.' : 'Registre uma nova despesa ou pagamento.' }}
        </p>
    </div>
    <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">
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
        <form action="{{ isset($bill) ? route('bills.update', $bill) : route('bills.store') }}" method="POST">
            @csrf
            @if(isset($bill))
                @method('PUT')
            @endif

            {{-- Descrição --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Descrição <span class="text-danger">*</span>
                </label>
                <input type="text" name="description"
                       class="form-control @error('description') is-invalid @enderror"
                       placeholder="Ex: Aluguel, Conta de luz, Fatura fornecedor..."
                       value="{{ old('description', $bill->description ?? '') }}" required>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Categoria --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Categoria <span class="text-danger">*</span>
                </label>
                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="" disabled {{ old('category', $bill->category ?? '') === '' ? 'selected' : '' }}>— Selecione —</option>
                    @foreach(\App\Models\Bill::CATEGORY_LABELS as $key => $label)
                        <option value="{{ $key }}" {{ old('category', $bill->category ?? '') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                               value="{{ old('amount', isset($bill) ? number_format($bill->amount, 2, '.', '') : '') }}"
                               required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        Vencimento (1ª parcela) <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="due_date"
                           class="form-control @error('due_date') is-invalid @enderror"
                           value="{{ old('due_date', isset($bill) ? $bill->due_date->format('Y-m-d') : '') }}"
                           required>
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Tipo de Cobrança (apenas na criação) --}}
            @if(!isset($bill))
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Tipo de Lançamento
                </label>
                <div class="d-flex gap-3 flex-wrap mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="billing_type" id="type_single" value="single"
                               {{ old('billing_type', 'single') === 'single' ? 'checked' : '' }}>
                        <label class="form-check-label text-soft" for="type_single">Pagamento único</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="billing_type" id="type_installments" value="installments"
                               {{ old('billing_type') === 'installments' ? 'checked' : '' }}>
                        <label class="form-check-label text-soft" for="type_installments">Parcelado</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="billing_type" id="type_recurrent" value="recurrent"
                               {{ old('billing_type') === 'recurrent' ? 'checked' : '' }}>
                        <label class="form-check-label text-soft" for="type_recurrent">Recorrente</label>
                    </div>
                </div>
            </div>

            {{-- Painel: Parcelado --}}
            <div id="panel_installments" class="mb-3" style="display:none;">
                <div class="card" style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:16px;">
                    <label class="form-label text-soft mb-2" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        <i class="bi bi-grid-3x3-gap me-1" style="color:#f87171;"></i> Número de Parcelas
                    </label>
                    <select name="installments" class="form-select @error('installments') is-invalid @enderror" style="max-width:220px;">
                        <option value="">— Selecione —</option>
                        @foreach([2,3,4,5,6,7,8,9,10,11,12,18,24,36,48,60] as $n)
                            <option value="{{ $n }}" {{ old('installments') == $n ? 'selected' : '' }}>{{ $n }}x</option>
                        @endforeach
                    </select>
                    <small class="text-soft mt-2 d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        O valor informado será dividido igualmente. Os vencimentos são gerados mensalmente a partir da data da 1ª parcela.
                    </small>
                    @error('installments')
                        <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Painel: Recorrente --}}
            <div id="panel_recurrent" class="mb-3" style="display:none;">
                <div class="card" style="background:rgba(168,85,247,.07);border:1px solid rgba(168,85,247,.25);border-radius:8px;padding:16px;">
                    <label class="form-label text-soft mb-2" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        <i class="bi bi-arrow-repeat me-1" style="color:#a855f7;"></i> Quantidade de Recorrências (meses)
                    </label>
                    <select name="recurrence" class="form-select @error('recurrence') is-invalid @enderror" style="max-width:220px;">
                        <option value="">— Selecione —</option>
                        @foreach([2,3,4,5,6,7,8,9,10,11,12,18,24,36,48,60] as $n)
                            <option value="{{ $n }}" {{ old('recurrence') == $n ? 'selected' : '' }}>{{ $n }} meses</option>
                        @endforeach
                    </select>
                    <small class="text-soft mt-2 d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        Gera pagamentos mensais com o mesmo valor. Cada mês é uma conta independente.
                    </small>
                    @error('recurrence')
                        <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @endif

            {{-- Observações --}}
            <div class="mb-4">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Observações
                </label>
                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Informações adicionais (opcional)...">{{ old('notes', $bill->notes ?? '') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Botões --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ isset($bill) ? 'Salvar Alterações' : 'Criar Conta' }}
                </button>
                <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const radios = document.querySelectorAll('input[name="billing_type"]');
    const panelInstallments = document.getElementById('panel_installments');
    const panelRecurrent    = document.getElementById('panel_recurrent');

    function togglePanels() {
        const val = document.querySelector('input[name="billing_type"]:checked')?.value;
        if (panelInstallments) panelInstallments.style.display = val === 'installments' ? 'block' : 'none';
        if (panelRecurrent)    panelRecurrent.style.display    = val === 'recurrent'    ? 'block' : 'none';
    }

    radios.forEach(r => r.addEventListener('change', togglePanels));
    togglePanels();
</script>
@endpush
@endsection
