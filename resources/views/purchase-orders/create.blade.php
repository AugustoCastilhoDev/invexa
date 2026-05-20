@extends('layouts.app')

@section('title', 'Nova Ordem de Compra')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex align-items-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Nova Ordem de Compra</h4>
            <p class="text-soft mb-0">Registre uma nova ordem de compra para reposição de estoque.</p>
        </div>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <div class="card-body">
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

        <form method="POST" action="{{ route('purchase-orders.store') }}" id="orderForm">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label text-soft">Fornecedor <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select bg-dark text-white border-secondary @error('supplier_id') is-invalid @enderror" required>
                        <option value="">Selecione o fornecedor...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft">Data do Pedido <span class="text-danger">*</span></label>
                    <input type="date" name="ordered_at" value="{{ old('ordered_at', date('Y-m-d')) }}"
                           class="form-control bg-dark text-white border-secondary @error('ordered_at') is-invalid @enderror" required>
                    @error('ordered_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label text-soft">Observações</label>
                    <textarea name="notes" rows="2"
                              class="form-control bg-dark text-white border-secondary @error('notes') is-invalid @enderror"
                              placeholder="Observações sobre o pedido...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="text-white mb-3">Itens do Pedido</h6>

            <div id="items-container">
                <div class="row g-2 mb-2 item-row align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label text-soft small">Produto</label>
                        <select name="items[0][product_id]" class="form-select bg-dark text-white border-secondary product-select" required>
                            <option value="">Selecione o produto...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->cost_price ?? $product->price }}">
                                    {{ $product->name }} (Estoque: {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-soft small">Qtd</label>
                        <input type="number" name="items[0][quantity]" min="1" value="1"
                               class="form-control bg-dark text-white border-secondary qty-input" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-soft small">Preço Unit.</label>
                        <input type="number" name="items[0][unit_price]" min="0" step="0.01" value="0.00"
                               class="form-control bg-dark text-white border-secondary price-input" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-soft small">Subtotal</label>
                        <input type="text" class="form-control bg-dark text-soft border-secondary subtotal-input" value="R$ 0,00" readonly>
                    </div>
                    <div class="col-6 col-md-1">
                        <label class="form-label text-soft small d-none d-md-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item" disabled>
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-item" class="btn btn-outline-secondary btn-sm mt-1 mb-4">
                <i class="bi bi-plus-circle me-1"></i>Adicionar item
            </button>

            <div class="d-flex justify-content-between align-items-center border-top border-secondary pt-3">
                <div class="text-white fs-5">
                    Total: <strong id="order-total">R$ 0,00</strong>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Criar Ordem
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;
    const fmt = v => v.toLocaleString('pt-BR', {style:'currency', currency:'BRL'});

    function calcRow(row) {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        row.querySelector('.subtotal-input').value = fmt(qty * price);
        calcTotal();
    }

    function calcTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            total += qty * price;
        });
        document.getElementById('order-total').textContent = fmt(total);
    }

    function bindRow(row) {
        row.querySelector('.product-select').addEventListener('change', function() {
            const price = this.options[this.selectedIndex].dataset.price || 0;
            row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
            calcRow(row);
        });
        row.querySelector('.qty-input').addEventListener('input',   () => calcRow(row));
        row.querySelector('.price-input').addEventListener('input', () => calcRow(row));
        row.querySelector('.remove-item').addEventListener('click', function() {
            row.remove();
            updateRemoveButtons();
            calcTotal();
        });
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(r => r.querySelector('.remove-item').disabled = rows.length === 1);
    }

    document.querySelectorAll('.item-row').forEach(bindRow);

    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const first     = container.querySelector('.item-row');
        const clone     = first.cloneNode(true);
        clone.querySelectorAll('input, select').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            if (el.type !== 'number' || el.classList.contains('qty-input')) el.value = el.type === 'number' ? '1' : '';
            if (el.classList.contains('price-input')) el.value = '0.00';
            if (el.classList.contains('subtotal-input')) el.value = 'R$ 0,00';
        });
        clone.querySelector('.product-select').value = '';
        bindRow(clone);
        container.appendChild(clone);
        itemIndex++;
        updateRemoveButtons();
    });
</script>
@endpush
@endsection
