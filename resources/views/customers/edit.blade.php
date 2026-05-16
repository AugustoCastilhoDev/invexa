@extends('layouts.app')
@section('title', 'Editar Cliente')

@push('styles')
<style>
body { background: radial-gradient(circle at top left,rgba(96,165,250,.10),transparent 20%), radial-gradient(circle at bottom right,rgba(34,197,94,.12),transparent 18%), #08101d; color:#e2e8f0; }
.card-dark-bg { background:rgba(15,23,42,.88); border:1px solid rgba(148,163,184,.14); }
.card-header-dark { background:rgba(15,23,42,.92); border-color:rgba(148,163,184,.12); }
.text-soft { color:rgba(226,232,240,.72) !important; }
.form-label { font-size:.82rem; color:rgba(226,232,240,.75); }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-light btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0 text-white">Editar Cliente</h1>
        <p class="text-soft mb-0">{{ $customer->name }}</p>
    </div>
</div>

<div class="card card-dark-bg shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')
            <div class="row g-3">

                <div class="col-12 col-md-6">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}"
                           class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">CPF / CNPJ</label>
                    <input type="text" name="document" value="{{ old('document', $customer->document) }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="form-control bg-dark text-white border-secondary @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>

                <div class="col-12">
                    <label class="form-label">Endereço</label>
                    <input type="text" name="address" value="{{ old('address', $customer->address) }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>

                <div class="col-12 col-md-8">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city) }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Estado (UF)</label>
                    <input type="text" name="state" value="{{ old('state', $customer->state) }}"
                           class="form-control bg-dark text-white border-secondary" maxlength="2">
                </div>

                <div class="col-12">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" rows="3"
                              class="form-control bg-dark text-white border-secondary">{{ old('notes', $customer->notes) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" value="1"
                               id="active" {{ old('active', $customer->active) ? 'checked' : '' }}>
                        <label class="form-check-label text-soft" for="active">Cliente ativo</label>
                    </div>
                </div>

            </div>

            <hr class="my-4" style="border-color:rgba(148,163,184,.15);">

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
