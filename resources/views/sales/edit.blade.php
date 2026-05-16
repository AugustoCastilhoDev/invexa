@extends('layouts.app')

@section('title', 'Editar Venda')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h4 class="mb-1">Editar Venda</h4>
                <p class="text-muted mb-0">Altere os dados da venda e seus itens.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo.</strong>
            </div>
        @endif

        <form action="{{ route('sales.update', $sale) }}" method="POST" id="sale-form">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label for="customer_name" class="form-label">Cliente</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $sale->customer_name) }}" placeholder="Nome do cliente">
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label for="sale_date" class="form-label">Data da venda</label>
                    <input type="datetime-local" id="sale_date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{ old('sale_date', optional($sale->sale_date)->format('Y-m-d\TH:i')) }}">
                    @error('sale_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="concluida" @selected(old('status', $sale->status) === 'concluida')>Concluída</option>
                        <option value="pendente" @selected(old('status', $sale->status) === 'pendente')>Pendente</option>
                        <option value="cancelada" @selected(old('status', $sale->status) === 'cancelada')>Cancelada</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-2">
                    <label for="notes" class="form-label">Observações</label>
                    <input type="text" id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes', $sale->notes) }}" placeholder="Opcional">
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Itens da venda</span>
                    <button type="button" class="btn btn-sm btn-primary" id="add-item">Adicionar item</button>
                </div>

                <div class="card-body">
                    @error('items')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div id="items-container" class="vstack gap-3">
                        @php
                            $oldItems = old('items');
                        @endphp

                        @if ($oldItems)
                            @foreach ($oldItems as $index => $item)
                                <div class="border rounded p-3 item-row">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-12 col-md-5">
                                            <label class="form-label">Produto</label>
                                            <select name="items[{{ $index }}][product_id]" class="form-select">
                                                <option value="">Selecione</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" @selected((string)($item['product_id'] ?? '') === (string)$product->id)>
                                                        {{ $product->name }} (Estoque: {{ $product->quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-6 col-md-2">
                                            <label class="form-label">Quantidade</label>
                                            <input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item['quantity'] ?? 1 }}">
                                        </div>

                                        <div class="col-6 col-md-3">
                                            <label class="form-label">Preço</label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" class="form-control" value="{{ $item['price'] ?? '' }}">
                                        </div>

                                        <div class="col-12 col-md-2 d-grid">
                                            <button type="button" class="btn btn-outline-danger remove-item">Remover</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @foreach ($sale->items as $index => $item)
                                <div class="border rounded p-3 item-row">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-12 col-md-5">
                                            <label class="form-label">Produto</label>
                                            <select name="items[{{ $index }}][product_id]" class="form-select">
                                                <option value="">Selecione</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" @selected($item->product_id == $product->id)>
                                                        {{ $product->name }} (Estoque: {{ $product->quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-6 col-md-2">
                                            <label class="form-label">Quantidade</label>
                                            <input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}">
                                        </div>

                                        <div class="col-6 col-md-3">
                                            <label class="form-label">Preço</label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" class="form-control" value="{{ $item->price }}">
                                        </div>

                                        <div class="col-12 col-md-2 d-grid">
                                            <button type="button" class="btn btn-outline-danger remove-item">Remover</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar venda</button>
            </div>
        </form>
    </div>
</div>

<template id="item-template">
    <div class="border rounded p-3 item-row">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label">Produto</label>
                <select name="items[__INDEX__][product_id]" class="form-select">
                    <option value="">Selecione</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (Estoque: {{ $product->quantity }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label">Quantidade</label>
                <input type="number" min="1" name="items[__INDEX__][quantity]" class="form-control" value="1">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label">Preço</label>
                <input type="number" step="0.01" min="0" name="items[__INDEX__][price]" class="form-control">
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
    const template = document.getElementById('item-template').innerHTML;
    const addButton = document.getElementById('add-item');

    function bindRemoveButtons() {
        document.querySelectorAll('.remove-item').forEach(button => {
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
        refreshIndexes();
    });

    bindRemoveButtons();
});
</script>
@endpush