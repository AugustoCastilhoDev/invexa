@extends('layouts.app')

@section('title', 'Nova Movimentação de Estoque')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">

    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">Nova Movimentação</h4>
                <p class="text-soft mb-0">Registre uma entrada, saída ou ajuste de estoque.</p>
            </div>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-light btn-sm">Voltar</a>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('stock.store') }}">
            @csrf

            <div class="row g-4">

                {{-- Produto --}}
                <div class="col-12 col-md-6">
                    <label for="product_id" class="form-label text-soft">Produto <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror"
                            onchange="loadProductInfo(this.value)">
                        <option value="">Selecione um produto...</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                    data-qty="{{ $p->quantity }}"
                                    data-unit="{{ $p->unit }}"
                                    data-sku="{{ $p->sku }}"
                                    {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} &mdash; {{ $p->sku }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Info atual do produto --}}
                <div class="col-12 col-md-6">
                    <label class="form-label text-soft">Estoque atual</label>
                    <div class="card card-dark-bg border border-secondary p-3" id="productInfo">
                        <p class="text-soft mb-0" style="font-size:.85rem;">Selecione um produto para ver o estoque atual.</p>
                    </div>
                </div>

                {{-- Tipo de movimentação --}}
                <div class="col-12 col-md-4">
                    <label for="type" class="form-label text-soft">Tipo <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                            onchange="updateTypeHint(this.value)">
                        <option value="">Selecione...</option>
                        <option value="entrada" {{ old('type')=='entrada'?'selected':'' }}>Entrada (aumenta estoque)</option>
                        <option value="saida"   {{ old('type')=='saida'  ?'selected':'' }}>Saída (reduz estoque)</option>
                        <option value="ajuste"  {{ old('type')=='ajuste' ?'selected':'' }}>Ajuste (define valor exato)</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Motivo --}}
                <div class="col-12 col-md-4">
                    <label for="reason" class="form-label text-soft">Motivo <span class="text-danger">*</span></label>
                    <select name="reason" id="reason" class="form-select @error('reason') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        <optgroup label="Entradas">
                            <option value="compra"        {{ old('reason')=='compra'       ?'selected':'' }}>Compra / Reposição</option>
                            <option value="devolucao"     {{ old('reason')=='devolucao'    ?'selected':'' }}>Devolução de cliente</option>
                            <option value="transferencia" {{ old('reason')=='transferencia'?'selected':'' }}>Transferência</option>
                        </optgroup>
                        <optgroup label="Saídas">
                            <option value="venda"         {{ old('reason')=='venda'        ?'selected':'' }}>Venda</option>
                            <option value="perda"         {{ old('reason')=='perda'        ?'selected':'' }}>Perda / Avaria</option>
                            <option value="transferencia" {{ old('reason')=='transferencia'?'selected':'' }}>Transferência</option>
                        </optgroup>
                        <optgroup label="Ajuste">
                            <option value="ajuste"        {{ old('reason')=='ajuste'       ?'selected':'' }}>Ajuste de inventário</option>
                        </optgroup>
                    </select>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Quantidade --}}
                <div class="col-12 col-md-4">
                    <label for="quantity" class="form-label text-soft" id="quantityLabel">
                        Quantidade <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" min="1"
                           class="form-control @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity') }}" placeholder="0">
                    <div class="form-text text-soft" id="typeHint"></div>
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Observações --}}
                <div class="col-12">
                    <label for="notes" class="form-label text-soft">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Informações adicionais sobre esta movimentação...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Registrar Movimentação
                </button>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadProductInfo(productId) {
        const sel  = document.getElementById('product_id');
        const opt  = sel.options[sel.selectedIndex];
        const box  = document.getElementById('productInfo');

        if (!productId) {
            box.innerHTML = '<p class="text-soft mb-0" style="font-size:.85rem;">Selecione um produto para ver o estoque atual.</p>';
            return;
        }

        const qty  = opt.dataset.qty;
        const unit = opt.dataset.unit;
        const sku  = opt.dataset.sku;
        const low  = parseInt(qty) <= 5;

        box.innerHTML = `
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-soft" style="font-size:.75rem;">SKU</div>
                    <div class="text-white fw-semibold">${sku}</div>
                </div>
                <div>
                    <div class="text-soft" style="font-size:.75rem;">Estoque atual</div>
                    <div class="fw-bold fs-5 ${low ? 'text-danger' : 'text-success'}">${qty} ${unit}</div>
                </div>
            </div>
        `;
    }

    function updateTypeHint(type) {
        const hint  = document.getElementById('typeHint');
        const label = document.getElementById('quantityLabel');
        const hints = {
            entrada: 'O estoque será aumentado em X unidades.',
            saida:   'O estoque será reduzido em X unidades.',
            ajuste:  'O estoque será definido para exatamente X unidades.',
        };
        hint.textContent = hints[type] || '';
    }

    // Pré-carrega se vier com old('product_id')
    window.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('product_id');
        if (sel.value) loadProductInfo(sel.value);
        const type = document.getElementById('type');
        if (type.value) updateTypeHint(type.value);
    });
</script>
@endpush
