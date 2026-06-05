@extends('layouts.app')

@section('title', 'Editar Venda #{{ $sale->id }}')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">Editar Venda #{{ $sale->id }}</h4>
                <p class="text-soft mb-0">Altere os dados da venda abaixo.</p>
            </div>
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-light">Voltar</a>
        </div>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @php
            $currentCustomerId   = old('customer_id',   $sale->customer_id);
            $currentCustomerName = old('customer_name',  $sale->customer?->name ?? $sale->customer_name);
        @endphp

        <form action="{{ route('sales.update', $sale) }}" method="POST" id="sale-form">
            @csrf @method('PUT')

            <input type="hidden" name="customer_id" id="customer_id" value="{{ $currentCustomerId }}">

            <div class="row g-3 mb-4">

                <div class="col-12 col-md-4">
                    <label for="customer_search" class="form-label text-soft fw-semibold">
                        Cliente <span class="text-danger">*</span>
                    </label>
                    <div class="position-relative">
                        <input type="text" id="customer_search" name="customer_name"
                            class="form-control @error('customer_id') is-invalid @enderror"
                            value="{{ $currentCustomerName }}"
                            placeholder="Digite para buscar um cliente..."
                            autocomplete="off">
                        <div id="customer-suggestions"
                             class="position-absolute w-100 z-3"
                             style="top:100%; display:none; background:rgba(10,18,35,.98);
                                    border:1px solid rgba(148,163,184,.18); border-radius:.5rem;
                                    box-shadow:0 12px 28px rgba(0,0,0,.45); max-height:220px; overflow-y:auto;">
                        </div>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="text-soft" id="customer-selected-hint"
                           style="display:{{ $currentCustomerId ? 'inline' : 'none' }};">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        <span id="customer-selected-name">{{ $currentCustomerName }}</span>
                        <a href="#" id="customer-clear" class="ms-2" style="color:#f87171;font-size:.8rem;">remover</a>
                    </small>
                    <div id="customer-required-msg" class="text-danger mt-1" style="font-size:.85rem; display:none;">
                        <i class="bi bi-exclamation-circle me-1"></i>Selecione um cliente da lista.
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label for="sale_date" class="form-label text-soft fw-semibold">Data da venda</label>
                    <input type="datetime-local" id="sale_date" name="sale_date"
                        class="form-control"
                        value="{{ old('sale_date', $sale->sale_date?->format('Y-m-d\\TH:i')) }}">
                </div>

                <div class="col-12 col-md-2">
                    <label for="status" class="form-label text-soft fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="concluida" @selected(old('status',$sale->status)==='concluida')>Concluída</option>
                        <option value="pendente"  @selected(old('status',$sale->status)==='pendente')>Pendente</option>
                        <option value="cancelada" @selected(old('status',$sale->status)==='cancelada')>Cancelada</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label for="notes" class="form-label text-soft fw-semibold">Observações</label>
                    <input type="text" id="notes" name="notes" class="form-control"
                           value="{{ old('notes', $sale->notes) }}" placeholder="Opcional">
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
                    <div id="items-container" class="vstack gap-3">
                        @php
                            $oldItems  = old('items');
                            $saleItems = $oldItems
                                ? collect($oldItems)->map(fn($i) => (object)$i)
                                : $sale->items;
                        @endphp

                        @foreach ($saleItems as $index => $item)
                            <div class="border rounded p-3 item-row">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label text-soft fw-semibold">Produto</label>
                                        <select name="items[{{ $index }}][product_id]" class="form-select product-select">
                                            <option value="">Selecione</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    data-stock="{{ $product->quantity }}"
                                                    @selected((string)($item->product_id ?? $item['product_id'] ?? '') === (string)$product->id)>
                                                    {{ $product->name }} (Estoque: {{ $product->quantity }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label text-soft fw-semibold">Quantidade</label>
                                        <input type="number" min="1" name="items[{{ $index }}][quantity]"
                                               class="form-control qty-input"
                                               value="{{ $item->quantity ?? $item['quantity'] ?? 1 }}">
                                        <div class="stock-warning text-warning mt-1" style="font-size:.78rem; display:none;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <span class="stock-warning-msg"></span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label text-soft fw-semibold">Preço Unit.</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                                            <input type="number" step="0.01" min="0"
                                                   name="items[{{ $index }}][price]"
                                                   class="form-control price-input"
                                                   value="{{ $item->price ?? $item['price'] ?? '' }}">
                                        </div>
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
                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
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
                        <option value="{{ $product->id }}"
                            data-price="{{ $product->price }}"
                            data-stock="{{ $product->quantity }}">
                            {{ $product->name }} (Estoque: {{ $product->quantity }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-soft fw-semibold">Quantidade</label>
                <input type="number" min="1" name="items[__INDEX__][quantity]" class="form-control qty-input" value="1">
                <div class="stock-warning text-warning mt-1" style="font-size:.78rem; display:none;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span class="stock-warning-msg"></span>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-soft fw-semibold">Preço Unit.</label>
                <div class="input-group">
                    <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                    <input type="number" step="0.01" min="0" name="items[__INDEX__][price]"
                           class="form-control price-input" placeholder="0.00">
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

    // ── Autocomplete de cliente ──────────────────────────────────────────
    const searchUrl   = '{{ route('customers.search') }}';
    const searchInput = document.getElementById('customer_search');
    const hiddenId    = document.getElementById('customer_id');
    const suggestions = document.getElementById('customer-suggestions');
    const hint        = document.getElementById('customer-selected-hint');
    const hintName    = document.getElementById('customer-selected-name');
    const clearBtn    = document.getElementById('customer-clear');
    const requiredMsg = document.getElementById('customer-required-msg');
    let   debounce;

    function selectCustomer(c) {
        searchInput.value    = c.name;
        hiddenId.value       = c.id;
        hintName.textContent = c.name;
        hint.style.display   = 'inline';
        requiredMsg.style.display = 'none';
        searchInput.classList.remove('is-invalid');
        suggestions.style.display = 'none';
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounce);
        hiddenId.value = '';
        hint.style.display = 'none';
        const q = this.value.trim();
        if (q.length < 2) { suggestions.style.display = 'none'; return; }
        debounce = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    if (!data.length) { suggestions.style.display = 'none'; return; }
                    data.forEach(c => {
                        const item = document.createElement('div');
                        item.style.cssText = 'padding:.55rem .85rem; cursor:pointer; font-size:.875rem; color:#cbd5e1; border-bottom:1px solid rgba(148,163,184,.10);';
                        item.innerHTML = `<span class="fw-semibold text-white">${c.name}</span>`
                            + (c.document ? ` <small class="text-soft ms-2">${c.document}</small>` : '')
                            + (c.phone    ? ` <small class="text-soft ms-2"><i class="bi bi-telephone"></i> ${c.phone}</small>` : '');
                        item.addEventListener('mousedown', e => { e.preventDefault(); selectCustomer(c); });
                        suggestions.appendChild(item);
                    });
                    suggestions.style.display = 'block';
                });
        }, 280);
    });

    searchInput.addEventListener('blur', () => setTimeout(() => {
        suggestions.style.display = 'none';
        if (!hiddenId.value) searchInput.value = '';
    }, 150));

    clearBtn.addEventListener('click', e => {
        e.preventDefault();
        hiddenId.value = '';
        searchInput.value = '';
        hint.style.display = 'none';
    });

    // ── Validação de estoque em tempo real ──────────────────────────────
    function checkStock(row) {
        const sel      = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const warning  = row.querySelector('.stock-warning');
        const warnMsg  = row.querySelector('.stock-warning-msg');
        if (!sel || !qtyInput || !warning) return true;

        const opt   = sel.options[sel.selectedIndex];
        const stock = opt ? parseInt(opt.dataset.stock ?? '-1') : -1;
        const qty   = parseInt(qtyInput.value) || 0;

        if (stock < 0 || !opt?.value) {
            warning.style.display = 'none';
            qtyInput.classList.remove('is-invalid');
            return true;
        }

        if (qty > stock) {
            warnMsg.textContent   = `Disponível: ${stock} un. Você está solicitando ${qty}.`;
            warning.style.display = 'block';
            qtyInput.classList.add('is-invalid');
            return false;
        }

        warning.style.display = 'none';
        qtyInput.classList.remove('is-invalid');
        return true;
    }

    function checkAllStock() {
        let valid = true;
        document.querySelectorAll('.item-row').forEach(row => {
            if (!checkStock(row)) valid = false;
        });
        return valid;
    }

    // ── Itens da venda ──────────────────────────────────────────────────
    const container = document.getElementById('items-container');
    const template  = document.getElementById('item-template').innerHTML;
    const addButton = document.getElementById('add-item');

    function bindRow(row) {
        const sel   = row.querySelector('.product-select');
        const price = row.querySelector('.price-input');
        const qty   = row.querySelector('.qty-input');

        if (sel) {
            sel.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt?.dataset.price) price.value = parseFloat(opt.dataset.price).toFixed(2);
                checkStock(row);
            });
        }
        if (qty) qty.addEventListener('input', () => checkStock(row));

        row.querySelector('.remove-item')?.addEventListener('click', function () {
            if (container.querySelectorAll('.item-row').length > 1) {
                this.closest('.item-row').remove();
                refreshIndexes();
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

    addButton.addEventListener('click', function () {
        const i = container.querySelectorAll('.item-row').length;
        container.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', i));
        bindRow(container.querySelectorAll('.item-row')[i]);
        refreshIndexes();
    });

    container.querySelectorAll('.item-row').forEach(row => bindRow(row));

    // Valida itens já carregados no edit
    container.querySelectorAll('.item-row').forEach(row => checkStock(row));

    // ── Bloquear submit ─────────────────────────────────────────────────
    document.getElementById('sale-form').addEventListener('submit', function (e) {
        let valid = true;

        if (!hiddenId.value) {
            e.preventDefault();
            searchInput.classList.add('is-invalid');
            requiredMsg.style.display = 'block';
            searchInput.focus();
            valid = false;
        }

        if (!checkAllStock()) {
            e.preventDefault();
            valid = false;
        }

        if (!valid) {
            const firstErr = document.querySelector('.is-invalid');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endpush
