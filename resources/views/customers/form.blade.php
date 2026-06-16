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

<div class="card card-dark-bg border border-secondary" style="max-width:760px;">
    <div class="card-body p-4">
        <form method="POST"
              action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}">
            @csrf
            @if(isset($customer))
                @method('PUT')
            @endif

            {{-- Nome --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Nome <span class="text-danger">*</span>
                </label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Nome completo ou razão social"
                       value="{{ old('name', $customer->name ?? '') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- E-mail + Telefone --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">E-mail</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="cliente@email.com"
                           value="{{ old('email', $customer->email ?? '') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Telefone / WhatsApp</label>
                    <input type="text" name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="(00) 00000-0000"
                           value="{{ old('phone', $customer->phone ?? '') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- CPF / CNPJ --}}
            <div class="mb-3">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">CPF / CNPJ</label>
                <input type="text" name="document"
                       class="form-control @error('document') is-invalid @enderror"
                       placeholder="000.000.000-00 ou 00.000.000/0000-00"
                       value="{{ old('document', $customer->document ?? '') }}">
                @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ══════════════════════════ ENDEREÇO ══════════════════════════ --}}
            <hr class="border-secondary my-4">
            <h6 class="text-soft mb-3" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                <i class="bi bi-geo-alt me-1"></i> Endereço
                <span class="text-muted fw-normal ms-1" style="font-size:.75rem;text-transform:none;letter-spacing:0;">(obrigatório para emissão de NF-e)</span>
            </h6>

            {{-- CEP --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-4">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">CEP</label>
                    <div class="input-group">
                        <input type="text" name="cep" id="cep"
                               class="form-control @error('cep') is-invalid @enderror"
                               placeholder="00000-000" maxlength="9"
                               value="{{ old('cep', $customer->cep ?? '') }}">
                        <button type="button" class="btn btn-outline-secondary" id="btn-buscar-cep" title="Buscar CEP">
                            <i class="bi bi-search" id="cep-icon"></i>
                        </button>
                    </div>
                    @error('cep')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-sm-8">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro"
                           class="form-control @error('logradouro') is-invalid @enderror"
                           placeholder="Rua, Avenida, Praça…"
                           value="{{ old('logradouro', $customer->logradouro ?? '') }}">
                    @error('logradouro')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Número + Complemento --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-sm-3">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Número</label>
                    <input type="text" name="numero_endereco" id="numero_endereco"
                           class="form-control @error('numero_endereco') is-invalid @enderror"
                           placeholder="123"
                           value="{{ old('numero_endereco', $customer->numero_endereco ?? '') }}">
                    @error('numero_endereco')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6 col-sm-4">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Complemento</label>
                    <input type="text" name="complemento" id="complemento"
                           class="form-control"
                           placeholder="Apto, Sala…"
                           value="{{ old('complemento', $customer->complemento ?? '') }}">
                </div>
                <div class="col-12 col-sm-5">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Bairro</label>
                    <input type="text" name="bairro" id="bairro"
                           class="form-control @error('bairro') is-invalid @enderror"
                           placeholder="Bairro"
                           value="{{ old('bairro', $customer->bairro ?? '') }}">
                    @error('bairro')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Município + UF --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-8">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Município</label>
                    <input type="text" name="municipio" id="municipio"
                           class="form-control @error('municipio') is-invalid @enderror"
                           placeholder="Cidade"
                           value="{{ old('municipio', $customer->municipio ?? '') }}">
                    @error('municipio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-sm-4">
                    <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">UF</label>
                    <select name="uf" id="uf" class="form-select @error('uf') is-invalid @enderror">
                        <option value="">--</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ old('uf', $customer->uf ?? '') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    @error('uf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Observações --}}
            <div class="mb-4">
                <label class="form-label text-soft" style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Observações</label>
                <textarea name="notes" rows="3"
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Anotações internas sobre este cliente (opcional)…">{{ old('notes', $customer->notes ?? '') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Botões --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ isset($customer) ? 'Salvar Alterações' : 'Cadastrar Cliente' }}
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const cepInput   = document.getElementById('cep');
    const btnBuscar  = document.getElementById('btn-buscar-cep');
    const icon       = document.getElementById('cep-icon');

    function preencherEndereco(data) {
        document.getElementById('logradouro').value  = data.logradouro  || '';
        document.getElementById('bairro').value      = data.bairro      || '';
        document.getElementById('municipio').value   = data.localidade  || '';
        const ufSelect = document.getElementById('uf');
        if (data.uf) {
            for (let opt of ufSelect.options) {
                if (opt.value === data.uf) { opt.selected = true; break; }
            }
        }
        document.getElementById('numero_endereco').focus();
    }

    function buscarCep() {
        const cep = cepInput.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        icon.className = 'bi bi-arrow-clockwise spin';
        btnBuscar.disabled = true;

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(r => r.json())
            .then(data => {
                if (data.erro) {
                    cepInput.classList.add('is-invalid');
                    cepInput.nextElementSibling?.remove();
                } else {
                    cepInput.classList.remove('is-invalid');
                    preencherEndereco(data);
                }
            })
            .catch(() => {})
            .finally(() => {
                icon.className = 'bi bi-search';
                btnBuscar.disabled = false;
            });
    }

    // Máscara CEP
    cepInput.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').slice(0, 8);
        if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
        this.value = v;
    });

    cepInput.addEventListener('blur', buscarCep);
    btnBuscar.addEventListener('click', buscarCep);
})();
</script>
<style>.spin{animation:spin .7s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush
@endsection
