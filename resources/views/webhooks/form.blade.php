@extends('layouts.app')
@section('title', isset($webhook) ? 'Editar Webhook' : 'Novo Webhook')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0" style="color:var(--brand-ice);">{{ isset($webhook) ? 'Editar Webhook' : 'Novo Webhook' }}</h2>
    <p class="mb-0" style="font-size:.82rem; color:rgba(148,163,184,.6);">Configure a URL e os eventos que deseja monitorar.</p>
</div>

<div class="card-dark-bg rounded-3 p-4" style="max-width:680px;">
    <form action="{{ isset($webhook) ? route('webhooks.update', $webhook) : route('webhooks.store') }}" method="POST">
        @csrf
        @if(isset($webhook)) @method('PUT') @endif

        {{-- URL --}}
        <div class="mb-3">
            <label class="form-label" style="font-size:.8rem; font-weight:600; color:rgba(226,232,240,.8);">URL do Endpoint <span class="text-danger">*</span></label>
            <input type="url" name="url" class="form-control @error('url') is-invalid @enderror"
                   placeholder="https://meusite.com/webhook"
                   value="{{ old('url', $webhook->url ?? '') }}" required>
            @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Descrição --}}
        <div class="mb-3">
            <label class="form-label" style="font-size:.8rem; font-weight:600; color:rgba(226,232,240,.8);">Descrição <span style="color:rgba(148,163,184,.5); font-weight:400;">(opcional)</span></label>
            <input type="text" name="description" class="form-control"
                   placeholder="Ex: Integração com ERP"
                   value="{{ old('description', $webhook->description ?? '') }}" maxlength="120">
        </div>

        {{-- Eventos --}}
        <div class="mb-3">
            <label class="form-label d-flex align-items-center justify-content-between" style="font-size:.8rem; font-weight:600; color:rgba(226,232,240,.8);">
                Eventos
                <label class="d-flex align-items-center gap-2 mb-0" style="font-weight:400; font-size:.78rem; cursor:pointer;">
                    <input type="checkbox" id="selectAll" class="form-check-input m-0">
                    <span style="color:rgba(148,163,184,.7);">Selecionar todos</span>
                </label>
            </label>
            @php
                $allEvents = [
                    'sale.created'         => 'Venda criada',
                    'sale.cancelled'       => 'Venda cancelada',
                    'sale.deleted'         => 'Venda excluída',
                    'product.low_stock'    => 'Estoque baixo',
                    'product.created'      => 'Produto criado',
                    'product.updated'      => 'Produto atualizado',
                    'customer.created'     => 'Cliente criado',
                    'bill.paid'            => 'Conta paga',
                    'receivable.received'  => 'Recebível recebido',
                    'purchase_order.received' => 'Ordem de compra recebida',
                ];
                $selectedEvents = old('events', $webhook->events ?? []);
            @endphp
            <div class="row g-2">
                @foreach($allEvents as $key => $label)
                <div class="col-6">
                    <label class="d-flex align-items-center gap-2 p-2 rounded" style="background:rgba(13,25,41,.6); border:1px solid rgba(14,165,233,.1); cursor:pointer; font-size:.8rem;">
                        <input type="checkbox" name="events[]" value="{{ $key }}" class="form-check-input m-0 event-checkbox"
                               {{ in_array($key, $selectedEvents) ? 'checked' : '' }}>
                        <span style="color:rgba(226,232,240,.75);">{{ $label }}</span>
                    </label>
                </div>
                @endforeach
            </div>
            @error('events')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
        </div>

        {{-- Ativo --}}
        <div class="mb-4">
            <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" class="form-check-input m-0"
                       {{ old('active', $webhook->active ?? true) ? 'checked' : '' }}>
                <span style="font-size:.82rem; color:rgba(226,232,240,.8);">Webhook ativo</span>
            </label>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-sm px-4"
                    style="background:rgba(14,165,233,.2); border:1px solid rgba(14,165,233,.4); color:#38BDF8; font-weight:600;">
                <i class="bi bi-check-lg me-1"></i>{{ isset($webhook) ? 'Salvar' : 'Criar Webhook' }}
            </button>
            <a href="{{ route('webhooks.index') }}" class="btn btn-sm px-3"
               style="background:rgba(148,163,184,.08); border:1px solid rgba(148,163,184,.15); color:rgba(226,232,240,.7);">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const allBoxes = document.querySelectorAll('.event-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.addEventListener('change', () => allBoxes.forEach(b => b.checked = selectAll.checked));
    allBoxes.forEach(b => b.addEventListener('change', () => {
        selectAll.checked = [...allBoxes].every(b => b.checked);
    }));
</script>
@endpush
