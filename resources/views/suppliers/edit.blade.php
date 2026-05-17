@extends('layouts.app')

@section('title', 'Editar Fornecedor')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 text-white">Editar Fornecedor</h4>
            <p class="text-soft mb-0">{{ $supplier->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-light btn-sm">Ver Detalhes</a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf @method('PUT')

            <div class="row g-4">

                <div class="col-12">
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem;letter-spacing:.08em;">
                        <i class="bi bi-building me-1"></i>Dados da Empresa
                    </h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label text-soft">Razão Social <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $supplier->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="trade_name" class="form-label text-soft">Nome Fantasia</label>
                            <input type="text" name="trade_name" id="trade_name"
                                   class="form-control @error('trade_name') is-invalid @enderror"
                                   value="{{ old('trade_name', $supplier->trade_name) }}">
                            @error('trade_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="document" class="form-label text-soft">CNPJ / CPF</label>
                            <input type="text" name="document" id="document"
                                   class="form-control @error('document') is-invalid @enderror"
                                   value="{{ old('document', $supplier->document) }}">
                            @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="phone" class="form-label text-soft">Telefone</label>
                            <input type="text" name="phone" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $supplier->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="email" class="form-label text-soft">E-mail</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $supplier->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contact_person" class="form-label text-soft">Pessoa de Contato</label>
                            <input type="text" name="contact_person" id="contact_person"
                                   class="form-control @error('contact_person') is-invalid @enderror"
                                   value="{{ old('contact_person', $supplier->contact_person) }}">
                            @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem;letter-spacing:.08em;">
                        <i class="bi bi-geo-alt me-1"></i>Endereço
                    </h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="address" class="form-label text-soft">Logradouro</label>
                            <input type="text" name="address" id="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $supplier->address) }}">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="city" class="form-label text-soft">Cidade</label>
                            <input type="text" name="city" id="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $supplier->city) }}">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-1">
                            <label for="state" class="form-label text-soft">UF</label>
                            <input type="text" name="state" id="state" maxlength="2"
                                   class="form-control @error('state') is-invalid @enderror"
                                   value="{{ old('state', $supplier->state) }}">
                            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="zip_code" class="form-label text-soft">CEP</label>
                            <input type="text" name="zip_code" id="zip_code" maxlength="10"
                                   class="form-control @error('zip_code') is-invalid @enderror"
                                   value="{{ old('zip_code', $supplier->zip_code) }}">
                            @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-9">
                    <label for="notes" class="form-label text-soft">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
                               {{ old('active', $supplier->active) ? 'checked' : '' }}>
                        <label class="form-check-label text-soft" for="active">Fornecedor ativo</label>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Salvar Alterações
                </button>
                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
