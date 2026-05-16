@extends('layouts.app')

@section('title', 'Nova Venda')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">Registrar Nova Venda</h4>
                <p class="text-soft mb-0">Selecione o produto e informe os detalhes da transação.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-light">Voltar</a>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label for="customer_name" class="form-label text-soft fw-semibold">Cliente</label>
                    <input type="text" id="customer_name" name="customer_name"
                        class="form-control @error('customer_name') is-invalid @enderror"
                        value="{{ old('customer_name') }}" placeholder="Nome do cliente">
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="sale_date" class="form-label text-soft fw-semibold">Data da venda</label>
                    <input type="datetime-local" id="sale_date" name="sale_date"
                        class="form-control @error('sale_date') is-invalid @enderror"
                        value="{{ old('sale_date', now()->format('Y-m-d\\TH:i')) }}">
                    @error('sale_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-2">
                    <label for="status" class="form-label text-soft fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="concluida" @selected(old('status', 'concluida') === 'concluida')>Concluída</option>
                        <option value="pendente"  @selected(old('status') === 'pendente')>Pendente</option>
                        <option value="cancelada" @selected(old('status') === 'cancelada')>Cancelada</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-2">
                    <label for="notes" class="form-label text-soft fw-semibold">Observações</label>
                    <input type="text" id="notes" name="notes"
                        class="form-control @error('notes') is-invalid @enderror"
                        value="{{ old('notes') }}" placeholder="Opcional">
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark d-flex justify-content-between align-items-center">
                    <span class="text-white">Itens da venda</span>
                    <button type="button" class="btn btn-sm btn-primary" id="add-item">
                        <i class="bi bi-plus-circle"></i> Adicionar item
                    </button>
                </div>

                <div class="card-body">
                    @error('items')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div id="items-container" class="vstack gap-3">
                        @php
                            $oldItems = old('items', [['product_id' => '', 'quantity' => 1, 'price' => '']]);
                        @endphp

                        @foreach ($oldItems as $index => $item)
                            <div class="border rounded p-3 item-row">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label text-soft fw-semibold">Produto</label>
                                        <select name="items[{{ $index }}][product_id]"
                                            class="form-select product-select @error('items.' . $index . '.product_id') is-invalid @enderror">
                                            <option value="">Selecione</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    @selected((string)($item['product_id'] ?? '') === (string)$product->id)>
                                                    {{ $product->name }} (Estoque: {{ $product->quantity }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('items.' . $index . '.product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-6 col-md-2">
                                        <label class="form-label text-soft fw-semibold">Quantidade</label>
                                        <input type="number" min="1"
                                            name="items[{{ $index }}][quantity]"
                                            class="form-control @error('items.' . $index . '.quantity') is-invalid @enderror"
                                            value="{{ $item['quantity'] ?? 1 }}">
                                        @error('items.' . $index . '.quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-6 col-md-2">
                                        <label class="form-label text-soft fw-semibold">Preço Unitário</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                                            <input type="number" step="0.01" min="0"
                                                name="items[{{ $index }}][price]"
                                                class="form-control price-input @error('items.' . $index . '.price') is-invalid @enderror"
                                                value="{{ $item['price'] ?? '' }}"
                                                placeholder="0.00">
                                        </div>
                                        @error('items.' . $index . '.price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-2 d-grid">
                                        <button type="button" class="btn btn-outline-danger remove-item">Remover</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar venda</button>
            </div>
        </form>
    </div>
</div>

<template id="item-template">
    <div class="border rounded p-3 item-row">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label text-soft fw-semibold">Produto</label>
                <select name="items[__INDEX__][product_id]" class="form-select product-select">
                    <option value="">Selecione</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} (Estoque: {{ $product->quantity }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label text-soft fw-semibold">Quantidade</label>
                <input type="number" min="1" name="items[__INDEX__][quantity]" class="form-control" value="1">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label text-soft fw-semibold">Preço Unitário</label>
                <div class="input-group">
                    <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                    <input type="number" step="0.01" min="0"
                           name="items[__INDEX__][price]"
                           class="form-control price-input"
                           placeholder="0.00">
                </div>
            </div>

            <div class="col-12 col-md-2 d-grid">
                <button type="button" class="btn btn-outline-danger remove-item">Remover</button>
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
    const addButton = document.getElementById('add-item');

    // Preenche o preço ao selecionar produto
    function bindProductSelect(row) {
        const select     = row.querySelector('.product-select');
        const priceInput = row.querySelector('.price-input');

        if (!select || !priceInput) return;

        select.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const price    = selected ? selected.dataset.price : '';
            if (price) {
                priceInput.value = parseFloat(price).toFixed(2);
            } else {
                priceInput.value = '';
            }
        });
    }

    function bindRemoveButtons() {
        container.querySelectorAll('.remove-item').forEach(button => {
            button.onclick = function () {
                const items = container.querySelectorAll('.item-row');
                if (items.length > 1) {
                    this.closest('.item-row').remove();
                    refreshIndexes();
                }
            };
        });
    }

    function refreshIndexes() {
        container.querySelectorAll('.item-row').forEach((row, index) => {
            row.querySelectorAll('select, input').forEach(field => {
                field.name = field.name.replace(/items\[\d+\]/, `items[${index}]`);
            });
        });
        bindRemoveButtons();
    }

    addButton.addEventListener('click', function () {
        const index = container.querySelectorAll('.item-row').length;
        container.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index));
        const newRow = container.querySelectorAll('.item-row')[index];
        bindProductSelect(newRow);
        refreshIndexes();
    });

    // Bind nos itens já existentes na página
    container.querySelectorAll('.item-row').forEach(row => bindProductSelect(row));

    bindRemoveButtons();
});
</script>
@endpush
