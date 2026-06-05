@extends('layouts.app')

@section('title', 'Nova Devolução')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 text-white">Nova Devolução</h4>
                <p class="text-soft mb-0">Selecione a venda e os itens a devolver.</p>
            </div>
            <a href="{{ route('returns.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Alerta de erro global --}}
        @if (session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('returns.store') }}" id="returnForm">
            @csrf

            <div class="row g-4">

                {{-- Seleção da venda --}}
                <div class="col-12 col-md-6">
                    <label for="sale_id" class="form-label text-soft fw-semibold">
                        Venda <span class="text-danger">*</span>
                    </label>
                    <select name="sale_id" id="sale_id"
                            class="form-select @error('sale_id') is-invalid @enderror">
                        <option value="">Selecione a venda...</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}"
                                {{ old('sale_id', $sale?->id) == $s->id ? 'selected' : '' }}>
                                #{{ $s->id }} —
                                {{ $s->customer_name ?? 'Sem nome' }} —
                                {{ $s->sale_date?->format('d/m/Y') }} —
                                R$ {{ number_format($s->total, 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Motivo --}}
                <div class="col-12 col-md-6">
                    <label for="reason" class="form-label text-soft fw-semibold">
                        Motivo <span class="text-danger">*</span>
                    </label>
                    <select name="reason" id="reason"
                            class="form-select @error('reason') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        <option value="defeito"        {{ old('reason') == 'defeito'        ? 'selected' : '' }}>Produto com defeito</option>
                        <option value="arrependimento" {{ old('reason') == 'arrependimento' ? 'selected' : '' }}>Arrependimento do cliente</option>
                        <option value="troca"          {{ old('reason') == 'troca'          ? 'selected' : '' }}>Troca de produto</option>
                        <option value="erro_venda"     {{ old('reason') == 'erro_venda'     ? 'selected' : '' }}>Erro na venda</option>
                        <option value="outro"          {{ old('reason') == 'outro'          ? 'selected' : '' }}>Outro motivo</option>
                    </select>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Loading spinner --}}
                <div class="col-12" id="loadingSection" style="display:none;">
                    <div class="d-flex align-items-center gap-2 text-soft">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span>Carregando itens da venda...</span>
                    </div>
                </div>

                {{-- Itens da venda --}}
                <div class="col-12" id="itemsSection" style="display:none;">
                    <label class="form-label text-soft fw-semibold">
                        Itens para devolver <span class="text-danger">*</span>
                    </label>
                    <div class="card card-dark-bg border border-secondary">
                        <div class="table-responsive">
                            <table class="table table-dark mb-0 align-middle" id="itemsTable">
                                <thead>
                                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;
                                               text-transform:uppercase;color:rgba(148,163,184,.85);
                                               border-bottom:1px solid rgba(148,163,184,.15);">
                                        <th class="ps-3 py-3" style="width:2.5rem;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th class="py-3">Produto</th>
                                        <th class="py-3">Qtd. Vendida</th>
                                        <th class="py-3">Já Devolvida</th>
                                        <th class="py-3" style="width:11rem;">Qtd. a Devolver</th>
                                        <th class="py-3">Preço Unit.</th>
                                        <th class="py-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                                <tfoot>
                                    <tr style="border-top:1px solid rgba(148,163,184,.2);">
                                        <td colspan="6" class="ps-3 py-3 text-soft fw-semibold text-end">
                                            Total a estornar
                                        </td>
                                        <td class="py-3 fw-bold text-danger fs-6" id="returnTotal">R$ 0,00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="noItemsAlert" class="alert alert-warning mt-2" style="display:none;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Todos os itens desta venda já foram devolvidos integralmente.
                    </div>
                </div>

                {{-- Observações --}}
                <div class="col-12" id="notesSection" style="display:none;">
                    <label for="notes" class="form-label text-soft fw-semibold">Observações</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="form-control">{{ old('notes') }}</textarea>
                </div>

                {{-- Botões --}}
                <div class="col-12" id="submitSection" style="display:none;">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger" id="submitBtn">
                            <i class="bi bi-arrow-return-left me-1"></i>Registrar Devolução
                        </button>
                        <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const saleSelect   = document.getElementById('sale_id');
const oldItems     = @json(old('items', []));
const oldSaleId    = '{{ old('sale_id', $sale?->id ?? '') }}';

function formatBRL(value) {
    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const row   = input.closest('tr');
        const chk   = row.querySelector('.item-check');
        const price = parseFloat(input.dataset.price);
        const qty   = parseInt(input.value) || 0;
        const sub   = chk.checked ? price * qty : 0;
        row.querySelector('.item-subtotal').textContent = formatBRL(sub);
        total += sub;
    });
    document.getElementById('returnTotal').textContent = formatBRL(total);
}

function showSections(hasAvailable) {
    document.getElementById('itemsSection').style.display  = '';
    document.getElementById('notesSection').style.display  = hasAvailable ? '' : 'none';
    document.getElementById('submitSection').style.display = hasAvailable ? '' : 'none';
    document.getElementById('noItemsAlert').style.display  = hasAvailable ? 'none' : '';
}

function hideSections() {
    ['itemsSection','notesSection','submitSection','noItemsAlert','loadingSection'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
}

function buildRows(items) {
    const tbody = document.getElementById('itemsBody');
    tbody.innerHTML = '';
    let hasAvailable = false;

    items.forEach((item, i) => {
        const available = item.available ?? item.quantity;
        const fullyReturned = available === 0;
        if (available > 0) hasAvailable = true;

        // Resgata valores antigos se form foi rejeitado
        const oldItem    = oldItems[i] || {};
        const oldChecked = oldItem.selected ? 'checked' : (fullyReturned ? '' : 'checked');
        const oldQty     = oldItem.quantity ?? available;

        const row = document.createElement('tr');
        row.style.opacity         = fullyReturned ? '0.45' : '1';
        row.style.borderColor     = 'rgba(148,163,184,.07)';
        row.innerHTML = `
            <td class="ps-3 py-3">
                <input type="checkbox" name="items[${i}][selected]" value="1"
                       class="form-check-input item-check" ${oldChecked}
                       ${fullyReturned ? 'disabled' : ''}
                       onchange="recalcTotal()">
            </td>
            <td class="py-3 text-white fw-semibold">
                ${item.product_name}
                <input type="hidden" name="items[${i}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${i}][price]"      value="${item.price}">
            </td>
            <td class="py-3" style="color:#94a3b8;">${item.quantity} un.</td>
            <td class="py-3">
                ${item.already_returned > 0
                    ? `<span class="badge bg-warning text-dark">${item.already_returned} devolvida(s)</span>`
                    : `<span class="text-soft">—</span>`}
            </td>
            <td class="py-3">
                ${fullyReturned
                    ? `<span class="badge bg-secondary">Totalmente devolvido</span>`
                    : `<input type="number" name="items[${i}][quantity]"
                               class="form-control form-control-sm qty-input"
                               value="${Math.min(oldQty, available)}" min="1" max="${available}"
                               data-price="${item.price}"
                               oninput="recalcTotal()" style="width:6rem;">
                       <small class="text-soft" style="font-size:.7rem;">máx. ${available}</small>`}
            </td>
            <td class="py-3" style="color:#94a3b8;">${formatBRL(item.price)}</td>
            <td class="py-3 fw-bold text-danger item-subtotal">
                ${fullyReturned ? formatBRL(0) : formatBRL(item.price * Math.min(oldQty, available))}
            </td>
        `;
        tbody.appendChild(row);
    });

    return hasAvailable;
}

function loadSaleItems(saleId, preloadedOldItems) {
    if (!saleId) { hideSections(); return; }

    document.getElementById('loadingSection').style.display = '';
    hideSections();
    document.getElementById('itemsSection').style.display  = 'none';

    fetch(`/returns/${saleId}/items`)
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(items => {
            document.getElementById('loadingSection').style.display = 'none';
            const hasAvailable = buildRows(items);
            showSections(hasAvailable);
            recalcTotal();
        })
        .catch(() => {
            document.getElementById('loadingSection').style.display = 'none';
        });
}

// Select all
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.item-check:not(:disabled)').forEach(c => c.checked = this.checked);
    recalcTotal();
});

// Prevent submit se nenhum item marcado
document.getElementById('returnForm').addEventListener('submit', function(e) {
    const anyChecked = [...document.querySelectorAll('.item-check')].some(c => c.checked);
    if (!anyChecked) {
        e.preventDefault();
        alert('Selecione ao menos um item para devolver.');
    }
});

saleSelect.addEventListener('change', () => loadSaleItems(saleSelect.value));

// Auto-carrega ao voltar com erro de validação ou via ?sale_id=
if (oldSaleId) loadSaleItems(oldSaleId);
</script>
@endpush
