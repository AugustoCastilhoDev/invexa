@extends('layouts.app')

@section('title', isset($customer) ? 'Editar Cliente' : 'Novo Cliente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1">
            <i class="bi bi-person-plus me-2 text-primary"></i>
            {{ isset($customer) ? 'Editar Cliente' : 'Novo Cliente' }}
        </h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">
            {{ isset($customer) ? 'Atualize os dados do cliente.' : 'Cadastre um novo cliente na base.' }}
        </p>
    </div>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4"
         style="background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3);color:#fca5a5;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Corrija os erros abaixo:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-dark-bg border border-secondary" style="max-width:700px;">
    <div class="card-body p-4">
        <form method="POST"
              action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}">
            @csrf
            @if(isset($customer))
                @method('PUT')
            @endif

            {{-- Nome --}}
            <div class="mb-3">
                <label class="form-label text-soft"
                       style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Nome <span class="text-danger">*</span>
                </label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Nome completo ou razão social"
                       value="{{ old('name', $customer->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- E-mail + Telefone --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft"
                           style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        E-mail
                    </label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="cliente@email.com"
                           value="{{ old('email', $customer->email ?? '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft"
                           style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        Telefone / WhatsApp
                    </label>
                    <input type="text" name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="(00) 00000-0000"
                           value="{{ old('phone', $customer->phone ?? '') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Documento --}}
            <div class="mb-3">
                <label class="form-label text-soft"
                       style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    CPF / CNPJ
                </label>
                <input type="text" name="document"
                       class="form-control @error('document') is-invalid @enderror"
                       placeholder="000.000.000-00 ou 00.000.000/0000-00"
                       value="{{ old('document', $customer->document ?? '') }}">
                @error('document')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Endereço --}}
            <div class="mb-3">
                <label class="form-label text-soft"
                       style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Endereço
                </label>
                <input type="text" name="address"
                       class="form-control @error('address') is-invalid @enderror"
                       placeholder="Rua, número, bairro, cidade…"
                       value="{{ old('address', $customer->address ?? '') }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Observações --}}
            <div class="mb-4">
                <label class="form-label text-soft"
                       style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Observações
                </label>
                <textarea name="notes" rows="3"
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Anotações internas sobre este cliente (opcional)…">{{ old('notes', $customer->notes ?? '') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Botões --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ isset($customer) ? 'Salvar Alterações' : 'Cadastrar Cliente' }}
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
