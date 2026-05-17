@extends('layouts.app')

@section('title', 'Nova Ordem de Compra')

@push('styles')
<style>
    .item-row td { vertical-align: middle; }
    .subtotal-cell { font-weight: 600; color: #4ade80; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-white">Nova Ordem de Compra</h1>
        <p class="text-soft mb-0">Preencha os dados e adicione os produtos.</p>
    </div>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light">Voltar</a>
</div>

<form method="POST" action="{{ route('purchase-orders.store') }}" id="ocForm">
    @csrf

    <div class="card card-dark-bg shadow-sm mb-4">
        <div class="card-header card-header-dark border-bottom">
            <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                <i class="bi bi-truck me-1"></i>Dados da Ordem
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label text-soft">Fornecedor <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                        <option value="">Selecione o fornecedor...</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft">Previsão de Entrega</label>
                    <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-soft">Observações</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Condições, prazos...">
                </div>
            </div>
        </div>
    </div>

    {{-- Itens --}}
    <div class="card card-dark-bg shadow-sm mb-4">
        <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
            <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                <i class="bi bi-list-ul me-1"></i>Itens do Pedido
            </span>
            <button type="button" id="addItem" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus me-1"></i>Adicionar Item
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark mb-0 align-middle" id="itemsTable">
                    <thead>
                        <tr style="font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                   color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                            <th class="ps-3 py-3" style="min-width:220px;">Produto</th>
                            <th class="py-3" style="min-width:120px;">Qtd Pedida</th>
                            <th class="py-3" style="min-width:150px;">Custo Unitário (R$)</th>
                            <th class="py-3" style="min-width:120px;">Subtotal</th>
                            <th class="py-3 pe-3"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center py-4 text-soft">
                                <i class="bi bi-cart me-1"></i>Clique em "Adicionar Item" para incluir produtos.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="border-top:1px solid rgba(148,163,184,.2);">
                            <td colspan="3" class="text-end pe-3 py-3 text-soft fw-semibold">Total do Pedido:</td>
                            <td class="py-3 fw-bold text-white" id="grandTotal">R$ 0,00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($errors->has('items'))
        <div class="alert alert-danger">{{ $errors->first('items') }}</div>
    @endif

    <div class="d-flex gap-2">
        <button type="submit" name="save" class="btn btn-outline-secondary">
            <i class="bi bi-floppy me-1"></i>Salvar Rascunho
        </button>
        <button type="submit" name="send" value="1" class="btn btn-primary">
            <i class="bi bi-send me-1"></i>Salvar e Enviar ao Fornecedor
        </button>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary ms-auto">Cancelar</a>
    </div>

</form>

{{-- Template de linha (hidden) --}}
<template id="rowTemplate">
    <tr class="item-row">
        <td class="ps-3">
            <select name="items[__IDX__][product_id]" class="form-select form-select-sm product-select" required>
                <option value="">Selecione...</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-cost="{{ $p->cost }}">{{ $p->name }} ({{ $p->sku }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[__IDX__][quantity]" class="form-control form-control-sm qty-input" min="1" value="1" required>
        </td>
        <td>
            <input type="number" name="items[__IDX__][unit_cost]" class="form-control form-control-sm cost-input" step="0.01" min="0" value="0" required>
        </td>
        <td class="subtotal-cell" data-subtotal="0">R$ 0,00</td>
        <td class="pe-3">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    let idx = 0;
    const body = document.getElementById('itemsBody');
    const emptyRow = document.getElementById('emptyRow');
    const grandTotalEl = document.getElementById('grandTotal');

    function fmtBRL(val) {
        return 'R$ ' + parseFloat(val || 0).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('[data-subtotal]').forEach(el => {
            total += parseFloat(el.dataset.subtotal || 0);
        });
        grandTotalEl.textContent = fmtBRL(total);
    }

    function bindRow(row) {
        const productSel = row.querySelector('.product-select');
        const qtyInput   = row.querySelector('.qty-input');
        const costInput  = row.querySelector('.cost-input');
        const subtotalEl = row.querySelector('[data-subtotal]');

        function updateSubtotal() {
            const qty  = parseFloat(qtyInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;
            const sub  = qty * cost;
            subtotalEl.dataset.subtotal = sub;
            subtotalEl.textContent = fmtBRL(sub);
            recalcTotal();
        }

        productSel.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const cost = parseFloat(opt.dataset.cost) || 0;
            if (cost > 0) costInput.value = cost.toFixed(2);
            updateSubtotal();
        });

        qtyInput.addEventListener('input', updateSubtotal);
        costInput.addEventListener('input', updateSubtotal);

        row.querySelector('.remove-row').addEventListener('click', function() {
            row.remove();
            if (!body.querySelector('.item-row')) body.appendChild(emptyRow);
            recalcTotal();
        });
    }

    document.getElementById('addItem').addEventListener('click', function() {
        if (body.contains(emptyRow)) body.removeChild(emptyRow);
        const tpl = document.getElementById('rowTemplate').innerHTML.replaceAll('__IDX__', idx++);
        const div = document.createElement('tbody');
        div.innerHTML = tpl;
        const row = div.firstElementChild;
        body.appendChild(row);
        bindRow(row);
    });

    document.getElementById('ocForm').addEventListener('submit', function(e) {
        if (!body.querySelector('.item-row')) {
            e.preventDefault();
            alert('Adicione pelo menos um item ao pedido.');
        }
    });
</script>
@endpush
@endsection
