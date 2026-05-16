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
            <a href="{{ route('returns.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('returns.store') }}" id="returnForm">
            @csrf

            <div class="row g-4">

                {{-- Seleção da venda --}}
                <div class="col-12 col-md-6">
                    <label for="sale_id" class="form-label text-soft">Venda <span class="text-danger">*</span></label>
                    <select name="sale_id" id="sale_id"
                            class="form-select @error('sale_id') is-invalid @enderror">
                        <option value="">Selecione a venda...</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}"
                                {{ (old('sale_id', $sale?->id) == $s->id) ? 'selected' : '' }}>
                                #{{ $s->id }} —
                                {{ $s->customer_name ?? 'Sem nome' }} —
                                {{ $s->sale_date?->format('d/m/Y') }} —
                                R$ {{ number_format($s->total, 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Motivo --}}
                <div class="col-12 col-md-6">
                    <label for="reason" class="form-label text-soft">Motivo <span class="text-danger">*</span></label>
                    <select name="reason" id="reason"
                            class="form-select @error('reason') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        <option value="defeito"        {{ old('reason')=='defeito'        ?'selected':'' }}>Produto com defeito</option>
                        <option value="arrependimento" {{ old('reason')=='arrependimento' ?'selected':'' }}>Arrependimento do cliente</option>
                        <option value="troca"          {{ old('reason')=='troca'          ?'selected':'' }}>Troca de produto</option>
                        <option value="erro_venda"     {{ old('reason')=='erro_venda'     ?'selected':'' }}>Erro na venda</option>
                        <option value="outro"          {{ old('reason')=='outro'          ?'selected':'' }}>Outro motivo</option>
                    </select>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Itens da venda (carregados via JS) --}}
                <div class="col-12" id="itemsSection" style="display:none;">
                    <label class="form-label text-soft">Itens para devolver <span class="text-danger">*</span></label>
                    <div class="card card-dark-bg border border-secondary">
                        <div class="table-responsive">
                            <table class="table table-dark mb-0 align-middle" id="itemsTable">
                                <thead>
                                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                                        <th class="ps-3 py-3" style="width:2rem;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th class="py-3">Produto</th>
                                        <th class="py-3">Qtd. Vendida</th>
                                        <th class="py-3" style="width:10rem;">Qtd. a Devolver</th>
                                        <th class="py-3">Prço Unit.</th>
                                        <th class="py-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                                <tfoot>
                                    <tr style="border-top:1px solid rgba(148,163,184,.2);">
                                        <td colspan="5" class="ps-3 py-3 text-soft fw-semibold text-end">Total a estornar</td>
                                        <td class="py-3 fw-bold text-danger fs-6" id="returnTotal">R$ 0,00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Observações --}}
                <div class="col-12" id="notesSection" style="display:none;">
                    <label for="notes" class="form-label text-soft">Observações</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="form-control">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="d-flex gap-2 mt-4" id="submitSection" style="display:none !important;">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-arrow-return-left me-1"></i>Registrar Devolução
                </button>
                <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const saleSelect = document.getElementById('sale_id');

    function formatBRL(value) {
        return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

    function loadSaleItems(saleId) {
        if (!saleId) {
            document.getElementById('itemsSection').style.display   = 'none';
            document.getElementById('notesSection').style.display   = 'none';
            document.getElementById('submitSection').style.display  = 'none';
            return;
        }

        fetch(`/returns/sale-items/${saleId}`)
            .then(r => r.json())
            .then(items => {
                const tbody = document.getElementById('itemsBody');
                tbody.innerHTML = '';

                items.forEach((item, i) => {
                    const row = document.createElement('tr');
                    row.style.borderColor = 'rgba(148,163,184,.07)';
                    row.innerHTML = `
                        <td class="ps-3 py-3">
                            <input type="checkbox" name="items[${i}][selected]" value="1"
                                   class="form-check-input item-check" checked
                                   onchange="recalcTotal()">
                        </td>
                        <td class="py-3 text-white fw-semibold">
                            ${item.product_name}
                            <input type="hidden" name="items[${i}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${i}][price]"      value="${item.price}">
                        </td>
                        <td class="py-3" style="color:#94a3b8;">${item.quantity} un.</td>
                        <td class="py-3">
                            <input type="number" name="items[${i}][quantity]"
                                   class="form-control form-control-sm qty-input"
                                   value="${item.quantity}" min="1" max="${item.quantity}"
                                   data-price="${item.price}"
                                   oninput="recalcTotal()" style="width:6rem;">
                        </td>
                        <td class="py-3" style="color:#94a3b8;">${formatBRL(item.price)}</td>
                        <td class="py-3 fw-bold text-danger item-subtotal">${formatBRL(item.subtotal)}</td>
                    `;
                    tbody.appendChild(row);
                });

                document.getElementById('itemsSection').style.display  = '';
                document.getElementById('notesSection').style.display  = '';
                document.getElementById('submitSection').style.display = '';
                recalcTotal();
            });
    }

    // Select all
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.item-check').forEach(c => c.checked = this.checked);
        recalcTotal();
    });

    saleSelect.addEventListener('change', () => loadSaleItems(saleSelect.value));

    // Auto-carrega se vier com ?sale_id=
    if (saleSelect.value) loadSaleItems(saleSelect.value);
</script>
@endpush
