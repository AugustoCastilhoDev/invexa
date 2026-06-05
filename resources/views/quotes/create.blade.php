@extends('layouts.app')

@section('title', 'Novo Orçamento')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white"><i class="bi bi-file-earmark-plus me-2"></i>Novo Orçamento</h4>
                <p class="text-soft mb-0">Preencha os dados abaixo para gerar o orçamento.</p>
            </div>
            <a href="{{ route('quotes.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quotes.store') }}" method="POST" id="quote-form">
            @csrf

            {{-- Dados do cliente --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-5">
                    <label class="form-label text-soft fw-semibold">Cliente</label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">— Selecione (opcional) —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(old('customer_id')==$c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label text-soft fw-semibold">Validade</label>
                    <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                        value="{{ old('valid_until', now()->addDays(7)->format('Y-m-d')) }}">
                    @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label text-soft fw-semibold">Desconto (R$)</label>
                    <input type="number" step="0.01" min="0" name="discount" id="discount"
                        class="form-control @error('discount') is-invalid @enderror"
                        value="{{ old('discount', 0) }}" placeholder="0.00">
                    @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label text-soft fw-semibold">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="rascunho" @selected(old('status','rascunho')==='rascunho')>Rascunho</option>
                        <option value="enviado"  @selected(old('status')==='enviado')>Enviado</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Itens --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark d-flex justify-content-between align-items-center">
                    <span class="text-white"><i class="bi bi-list-ul me-2"></i>Itens do orçamento</span>
                    <button type="button" class="btn btn-sm btn-primary" id="add-item">
                        <i class="bi bi-plus-circle"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body">
                    @error('items')<div class="alert alert-danger">{{ $message }}</div>@enderror

                    <div id="items-container" class="vstack gap-3">
                        @php $oldItems = old('items', [['product_id'=>'','description'=>'','quantity'=>1,'unit_price'=>'']]); @endphp
                        @foreach($oldItems as $index => $item)
                        <div class="border rounded p-3 item-row" style="border-color:rgba(148,163,184,.15) !important;">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-soft fw-semibold">Produto</label>
                                    <select name="items[{{ $index }}][product_id]" class="form-select product-select">
                                        <option value="">— Produto (opcional) —</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->price }}"
                                                data-name="{{ $product->name }}"
                                                @selected(($item['product_id']??'')==$product->id)>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label text-soft fw-semibold">Descrição <span class="text-danger">*</span></label>
                                    <input type="text" name="items[{{ $index }}][description]" class="form-control desc-input"
                                        value="{{ $item['description']??'' }}" placeholder="Descrição obrigatória">
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label text-soft fw-semibold">Qtd</label>
                                    <input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control qty-input"
                                        value="{{ $item['quantity']??1 }}">
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="form-label text-soft fw-semibold">Preço Unit.</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" class="form-control price-input"
                                            value="{{ $item['unit_price']??'' }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-12 col-md-1 d-grid">
                                    <button type="button" class="btn btn-outline-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Resumo + Observações --}}
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-8">
                    <label class="form-label text-soft fw-semibold">Observações</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Condições de pagamento, prazo de entrega...">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-dark-bg h-100">
                        <div class="card-header card-header-dark">
                            <span class="text-white fw-semibold">Resumo</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between text-soft mb-2">
                                <span>Subtotal</span>
                                <span id="summary-subtotal">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between text-soft mb-3">
                                <span>Desconto</span>
                                <span id="summary-discount">R$ 0,00</span>
                            </div>
                            <hr style="border-color:rgba(148,163,184,.15);">
                            <div class="d-flex justify-content-between text-white fw-bold">
                                <span>Total</span>
                                <span id="summary-total">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Salvar Orçamento
                </button>
            </div>
        </form>
    </div>
</div>

<template id="item-template">
<div class="border rounded p-3 item-row" style="border-color:rgba(148,163,184,.15) !important;">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label text-soft fw-semibold">Produto</label>
            <select name="items[__INDEX__][product_id]" class="form-select product-select">
                <option value="">— Produto (opcional) —</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-name="{{ $product->name }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label text-soft fw-semibold">Descrição <span class="text-danger">*</span></label>
            <input type="text" name="items[__INDEX__][description]" class="form-control desc-input" placeholder="Descrição obrigatória">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label text-soft fw-semibold">Qtd</label>
            <input type="number" min="1" name="items[__INDEX__][quantity]" class="form-control qty-input" value="1">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label text-soft fw-semibold">Preço Unit.</label>
            <div class="input-group">
                <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                <input type="number" step="0.01" min="0" name="items[__INDEX__][unit_price]" class="form-control price-input" placeholder="0.00">
            </div>
        </div>
        <div class="col-12 col-md-1 d-grid">
            <button type="button" class="btn btn-outline-danger remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('items-container');
    const template  = document.getElementById('item-template').innerHTML;
    const addBtn    = document.getElementById('add-item');
    const discountInput = document.getElementById('discount');

    function fmt(v) {
        return 'R$ ' + parseFloat(v||0).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function recalc() {
        let subtotal = 0;
        container.querySelectorAll('.item-row').forEach(row => {
            const qty   = parseFloat(row.querySelector('.qty-input')?.value)   || 0;
            const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
            subtotal += qty * price;
        });
        const discount = parseFloat(discountInput?.value) || 0;
        const total    = Math.max(0, subtotal - discount);
        document.getElementById('summary-subtotal').textContent = fmt(subtotal);
        document.getElementById('summary-discount').textContent = fmt(discount);
        document.getElementById('summary-total').textContent    = fmt(total);
    }

    function bindRow(row) {
        row.querySelector('.product-select')?.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt?.dataset.price) {
                row.querySelector('.price-input').value = parseFloat(opt.dataset.price).toFixed(2);
            }
            if (opt?.dataset.name) {
                const desc = row.querySelector('.desc-input');
                if (!desc.value) desc.value = opt.dataset.name;
            }
            recalc();
        });
        row.querySelector('.qty-input')?.addEventListener('input', recalc);
        row.querySelector('.price-input')?.addEventListener('input', recalc);
        row.querySelector('.remove-item')?.addEventListener('click', function () {
            if (container.querySelectorAll('.item-row').length > 1) {
                this.closest('.item-row').remove();
                refreshIndexes();
                recalc();
            }
        });
    }

    function refreshIndexes() {
        container.querySelectorAll('.item-row').forEach((row, i) => {
            row.querySelectorAll('select, input').forEach(f => {
                f.name = f.name.replace(/items\[\d+\]/, `items[${i}]`);
            });
        });
    }

    addBtn.addEventListener('click', function () {
        const i = container.querySelectorAll('.item-row').length;
        container.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', i));
        const newRow = container.querySelectorAll('.item-row')[i];
        bindRow(newRow);
        refreshIndexes();
    });

    discountInput?.addEventListener('input', recalc);
    container.querySelectorAll('.item-row').forEach(row => bindRow(row));
    recalc();
});
</script>
@endpush
