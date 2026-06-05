@extends('layouts.app')
@section('title', isset($purchaseOrder) ? 'Editar Ordem de Compra' : 'Nova Ordem de Compra')

@push('styles')
<style>
body {
    background: radial-gradient(circle at top left, rgba(96,165,250,.10), transparent 20%),
                radial-gradient(circle at bottom right, rgba(34,197,94,.12), transparent 18%),
                #08101d;
    color: #e2e8f0;
}
.card-dark-bg  { background: rgba(15,23,42,.88); border: 1px solid rgba(148,163,184,.14); }
.card-header-dark { background: rgba(15,23,42,.92); border-color: rgba(148,163,184,.12); }
.text-soft { color: rgba(226,232,240,.72) !important; }
.form-control, .form-select {
    background: rgba(15,23,42,.6) !important;
    border-color: rgba(148,163,184,.25) !important;
    color: #e2e8f0 !important;
}
.form-control:focus, .form-select:focus {
    background: rgba(15,23,42,.9) !important;
    border-color: rgba(99,179,237,.5) !important;
    box-shadow: 0 0 0 3px rgba(99,179,237,.15) !important;
    color: #f0f4f8 !important;
}
.form-control::placeholder { color: rgba(148,163,184,.5) !important; }
.form-label { color: rgba(226,232,240,.80); font-size: .82rem; font-weight: 600; letter-spacing: .04em; margin-bottom: .3rem; }

/* Items table */
#items-body tr { border-bottom: 1px solid rgba(148,163,184,.08); }
#items-body td { padding: .5rem .4rem; vertical-align: middle; }
.item-remove { background: transparent; border: none; color: rgba(248,113,113,.75); cursor: pointer; transition: color .15s; }
.item-remove:hover { color: #f87171; }
.total-row { background: rgba(15,23,42,.6); border-top: 1px solid rgba(148,163,184,.2) !important; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="h4 mb-0 text-white">
            {{ isset($purchaseOrder) ? 'Editar Ordem de Compra #' . $purchaseOrder->id : 'Nova Ordem de Compra' }}
        </h1>
        <p class="text-soft mb-0" style="font-size:.82rem">
            {{ isset($purchaseOrder) ? 'Atualize os dados da ordem de compra.' : 'Preencha os dados para criar uma nova ordem de compra.' }}
        </p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <strong>Atenção:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST"
      action="{{ isset($purchaseOrder) ? route('purchase-orders.update', $purchaseOrder) : route('purchase-orders.store') }}"
      id="purchase-form">
    @csrf
    @if(isset($purchaseOrder)) @method('PUT') @endif

    <div class="row g-4">
        {{-- Coluna principal --}}
        <div class="col-12 col-lg-8">

            {{-- Dados gerais --}}
            <div class="card card-dark-bg shadow-sm mb-4">
                <div class="card-header card-header-dark border-bottom">
                    <h5 class="mb-0 text-white" style="font-size:.95rem">
                        <i class="bi bi-clipboard-data me-2 text-info"></i>Dados Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Fornecedor</label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">— Sem fornecedor —</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Data do Pedido <span class="text-danger">*</span></label>
                            <input type="date" name="order_date"
                                   class="form-control @error('order_date') is-invalid @enderror"
                                   value="{{ old('order_date', isset($purchaseOrder) ? $purchaseOrder->order_date?->format('Y-m-d') : now()->format('Y-m-d')) }}">
                            @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Previsão de Chegada</label>
                            <input type="date" name="expected_date"
                                   class="form-control @error('expected_date') is-invalid @enderror"
                                   value="{{ old('expected_date', isset($purchaseOrder) ? $purchaseOrder->expected_date?->format('Y-m-d') : '') }}">
                            @error('expected_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observações</label>
                            <textarea name="notes" rows="2"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Observações internas sobre esta ordem...">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Itens --}}
            <div class="card card-dark-bg shadow-sm">
                <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white" style="font-size:.95rem">
                        <i class="bi bi-box-seam me-2 text-warning"></i>Itens do Pedido
                    </h5>
                    <button type="button" id="add-item" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-plus-lg me-1"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0" style="font-size:.85rem">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(148,163,184,.2)">
                                    <th class="ps-3" style="width:40%; color:rgba(148,163,184,.8); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; padding:.8rem .5rem">Produto</th>
                                    <th style="width:18%; color:rgba(148,163,184,.8); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em">Qtd</th>
                                    <th style="width:22%; color:rgba(148,163,184,.8); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em">Custo Unit.</th>
                                    <th style="width:15%; color:rgba(148,163,184,.8); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em">Subtotal</th>
                                    <th style="width:5%"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                @if(isset($purchaseOrder) && $purchaseOrder->items->count())
                                    @foreach($purchaseOrder->items as $i => $item)
                                    <tr data-index="{{ $i }}">
                                        <td class="ps-3">
                                            <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm product-select" required>
                                                <option value="">Selecione...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-price="{{ $product->price }}"
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][quantity]" min="1"
                                                   class="form-control form-control-sm qty-input" required
                                                   value="{{ $item->quantity }}">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][cost]" min="0" step="0.01"
                                                   class="form-control form-control-sm cost-input" required
                                                   value="{{ number_format($item->unit_cost, 2, '.', '') }}">
                                        </td>
                                        <td class="subtotal-cell fw-semibold" style="color:#4ade80">
                                            R$ {{ number_format($item->quantity * $item->unit_cost, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            <button type="button" class="item-remove" title="Remover">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    {{-- linha vazia inicial para novo pedido --}}
                                    <tr data-index="0">
                                        <td class="ps-3">
                                            <select name="items[0][product_id]" class="form-select form-select-sm product-select" required>
                                                <option value="">Selecione...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" min="1" value="1"
                                                   class="form-control form-control-sm qty-input" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][cost]" min="0" step="0.01" value="0.00"
                                                   class="form-control form-control-sm cost-input" required>
                                        </td>
                                        <td class="subtotal-cell fw-semibold" style="color:#4ade80">R$ 0,00</td>
                                        <td>
                                            <button type="button" class="item-remove" title="Remover">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="3" class="text-end pe-3 text-soft ps-3 py-3" style="font-size:.82rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em">Total do Pedido</td>
                                    <td colspan="2" id="grand-total" class="fw-bold py-3" style="color:#4ade80; font-size:1rem">R$ 0,00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @error('items')
                    <div class="px-3 pb-2"><small class="text-danger">{{ $message }}</small></div>
                @enderror
            </div>
        </div>

        {{-- Coluna lateral --}}
        <div class="col-12 col-lg-4">
            <div class="card card-dark-bg shadow-sm sticky-top" style="top: 1.5rem">
                <div class="card-header card-header-dark border-bottom">
                    <h5 class="mb-0 text-white" style="font-size:.95rem">
                        <i class="bi bi-send me-2 text-primary"></i>Salvar Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-soft mb-4" style="font-size:.83rem">
                        Revise os dados antes de salvar. A ordem ficará com status <strong class="text-warning">Pendente</strong> até ser recebida.
                    </p>

                    {{-- Resumo --}}
                    <div class="rounded p-3 mb-4" style="background:rgba(0,0,0,.3); border: 1px solid rgba(148,163,184,.1)">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-soft" style="font-size:.82rem">Itens</span>
                            <span id="summary-items" class="text-white fw-semibold">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-soft" style="font-size:.82rem">Total</span>
                            <span id="summary-total" class="fw-bold" style="color:#4ade80; font-size:1.05rem">R$ 0,00</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1"></i>
                            {{ isset($purchaseOrder) ? 'Salvar Alterações' : 'Criar Ordem de Compra' }}
                        </button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    // Produtos disponíveis injetados pelo PHP
    const products = @json($products->keyBy('id'));
    let rowIndex = {{ isset($purchaseOrder) ? $purchaseOrder->items->count() : 1 }};

    const body = document.getElementById('items-body');

    function fmt(val) {
        return 'R$ ' + parseFloat(val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalc() {
        let grand = 0;
        let itemCount = 0;
        body.querySelectorAll('tr').forEach(row => {
            const qty  = parseFloat(row.querySelector('.qty-input')?.value)  || 0;
            const cost = parseFloat(row.querySelector('.cost-input')?.value) || 0;
            const sub  = qty * cost;
            grand += sub;
            itemCount++;
            const cell = row.querySelector('.subtotal-cell');
            if (cell) cell.textContent = fmt(sub);
        });
        document.getElementById('grand-total').textContent   = fmt(grand);
        document.getElementById('summary-total').textContent = fmt(grand);
        document.getElementById('summary-items').textContent = itemCount;
    }

    function buildProductOptions(selectedId) {
        let opts = '<option value="">Selecione...</option>';
        Object.values(products).forEach(p => {
            opts += `<option value="${p.id}" data-price="${p.price}"${ p.id == selectedId ? ' selected' : '' }>${p.name}</option>`;
        });
        return opts;
    }

    function addRow(productId, qty, cost) {
        const idx = rowIndex++;
        const tr = document.createElement('tr');
        tr.dataset.index = idx;
        tr.innerHTML = `
            <td class="ps-3">
                <select name="items[${idx}][product_id]" class="form-select form-select-sm product-select" required>
                    ${buildProductOptions(productId)}
                </select>
            </td>
            <td>
                <input type="number" name="items[${idx}][quantity]" min="1"
                       class="form-control form-control-sm qty-input" required value="${qty || 1}">
            </td>
            <td>
                <input type="number" name="items[${idx}][cost]" min="0" step="0.01"
                       class="form-control form-control-sm cost-input" required value="${cost || '0.00'}">
            </td>
            <td class="subtotal-cell fw-semibold" style="color:#4ade80">R$ 0,00</td>
            <td>
                <button type="button" class="item-remove" title="Remover">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;
        body.appendChild(tr);
        recalc();
    }

    // Botão adicionar
    document.getElementById('add-item').addEventListener('click', () => addRow());

    // Delegação: remover linha
    body.addEventListener('click', e => {
        const btn = e.target.closest('.item-remove');
        if (!btn) return;
        const rows = body.querySelectorAll('tr');
        if (rows.length <= 1) { alert('O pedido deve ter ao menos 1 item.'); return; }
        btn.closest('tr').remove();
        recalc();
    });

    // Delegação: mudança de produto → preenche custo
    body.addEventListener('change', e => {
        if (e.target.classList.contains('product-select')) {
            const opt = e.target.selectedOptions[0];
            const costInput = e.target.closest('tr').querySelector('.cost-input');
            if (opt && opt.dataset.price && costInput) {
                costInput.value = parseFloat(opt.dataset.price).toFixed(2);
            }
        }
        recalc();
    });

    // Delegação: digitação em qty/cost
    body.addEventListener('input', e => {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('cost-input')) {
            recalc();
        }
    });

    // Recalcula na carga (edição)
    recalc();
})();
</script>
@endpush
