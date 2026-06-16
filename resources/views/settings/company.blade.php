@extends('layouts.app')
@section('title', 'Perfil da Empresa')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:2.5rem;height:2.5rem;border-radius:.6rem;background:rgba(14,165,233,.12);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-building" style="color:#38BDF8;font-size:1.1rem;"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold" style="color:#F0F9FF;">Perfil da Empresa</h4>
                <p class="mb-0 text-soft" style="font-size:.82rem;">Gerencie os dados e identidade visual da sua empresa</p>
            </div>
        </div>

        <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')

            {{-- Logo --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark py-3">
                    <span class="fw-semibold" style="color:#e2e8f0;"><i class="bi bi-image me-2 opacity-75"></i>Logo da Empresa</span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div style="width:7rem;height:7rem;border-radius:.75rem;border:2px dashed rgba(14,165,233,.25);background:rgba(13,25,41,.6);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                            @if($company->logo)
                                <img id="logoPreview" src="{{ Storage::url($company->logo) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;padding:6px;">
                            @else
                                <img id="logoPreview" src="" alt="" style="width:100%;height:100%;object-fit:contain;padding:6px;display:none;">
                                <i id="logoPlaceholder" class="bi bi-building" style="font-size:2.5rem;color:rgba(14,165,233,.3);"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label text-soft" style="font-size:.82rem;">Arquivo de imagem</label>
                            <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/*" onchange="previewLogo(this)">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-1" style="font-size:.75rem;color:rgba(148,163,184,.6);">JPG, PNG, WEBP ou SVG — máx. 2 MB</div>
                            @if($company->logo)
                                <form action="{{ route('settings.company.logo.destroy') }}" method="POST" class="d-inline mt-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2" style="font-size:.75rem;"
                                        onclick="return confirm('Remover logo?')">
                                        <i class="bi bi-trash me-1"></i>Remover logo
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dados da empresa --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark py-3">
                    <span class="fw-semibold" style="color:#e2e8f0;"><i class="bi bi-info-circle me-2 opacity-75"></i>Dados da Empresa</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label text-soft">Razão Social / Nome <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $company->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-soft">CNPJ</label>
                            <input type="text" name="cnpj" id="cnpj" class="form-control @error('cnpj') is-invalid @enderror"
                                   value="{{ old('cnpj', $company->cnpj) }}" maxlength="18" placeholder="00.000.000/0000-00">
                            @error('cnpj')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-soft">E-mail</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $company->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-soft">Telefone</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $company->phone) }}" maxlength="15" placeholder="(00) 00000-0000">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label text-soft">Endereço</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $company->address) }}" placeholder="Rua, número, bairro, cidade - UF">
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>

        {{-- ── Integração Pix / Asaas ── --}}
        <div class="card card-dark-bg" style="border-color:rgba(14,165,233,.2);">
            <div class="card-body p-4">

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(14,165,233,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-qr-code" style="font-size:1.2rem;color:#38BDF8;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color:#f1f5f9;">Integração Pix — Asaas</h6>
                        <small style="color:rgba(148,163,184,.6);">Configure sua conta Asaas para receber Pix nas vendas automaticamente</small>
                    </div>
                </div>

                @php $asaasConfigured = ! empty($company->asaas_api_key); @endphp

                <div class="mb-3">
                    @if($asaasConfigured)
                        <span class="badge" style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
                            <i class="bi bi-check-circle-fill me-1"></i>Asaas configurado
                        </span>
                    @else
                        <span class="badge" style="background:rgba(148,163,184,.1);color:#94a3b8;border:1px solid rgba(148,163,184,.2);">
                            <i class="bi bi-dash-circle me-1"></i>Não configurado
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('settings.asaas.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label text-soft" style="font-size:.82rem;">Ambiente</label>
                            <select name="asaas_environment" class="form-select form-select-sm"
                                    style="background:rgba(13,25,41,.7);border-color:rgba(14,165,233,.2);color:#e2e8f0;">
                                <option value="production" {{ ($company->asaas_environment ?? 'production') === 'production' ? 'selected' : '' }}>Produção</option>
                                <option value="sandbox"    {{ ($company->asaas_environment ?? '') === 'sandbox' ? 'selected' : '' }}>Sandbox (testes)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label text-soft" style="font-size:.82rem;">
                                API Key Asaas
                                <a href="https://www.asaas.com/config/index" target="_blank" rel="noopener"
                                   class="ms-2" style="font-size:.75rem;color:#38BDF8;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Onde encontrar?
                                </a>
                            </label>
                            <input type="password" name="asaas_api_key" class="form-control form-control-sm"
                                   placeholder="{{ $asaasConfigured ? '●●●●●●●● (deixe em branco para manter)' : '$aact_...' }}"
                                   autocomplete="new-password"
                                   style="background:rgba(13,25,41,.7);border-color:rgba(14,165,233,.2);color:#e2e8f0;">
                            <div class="form-text" style="font-size:.75rem;color:rgba(148,163,184,.5);">
                                Sua chave nunca é exibida após salva. Para substituir, insira a nova chave.
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <button type="submit" class="btn btn-sm btn-primary" style="background:#0EA5E9;border:none;font-weight:600;">
                            <i class="bi bi-check-lg me-1"></i>
                            {{ $asaasConfigured ? 'Atualizar integração' : 'Salvar e conectar' }}
                        </button>
                        @if($asaasConfigured)
                        <button type="submit" name="remove_api_key" value="1"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Remover integração Asaas?')">
                            <i class="bi bi-trash me-1"></i>Remover
                        </button>
                        @endif
                    </div>
                </form>

                @if(! $asaasConfigured)
                <div class="mt-3 p-3 rounded" style="background:rgba(14,165,233,.05);border:1px solid rgba(14,165,233,.12);font-size:.82rem;color:rgba(148,163,184,.7);">
                    <i class="bi bi-info-circle me-1" style="color:#38BDF8;"></i>
                    Após configurar, toda venda <strong style="color:#e2e8f0;">pendente com cliente vinculado</strong>
                    gera um QR Code Pix automaticamente. A confirmação é feita sem intervenção manual.
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    const placeholder = document.getElementById('logoPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
document.getElementById('cnpj')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 14);
    v = v.replace(/(\d{2})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1\/$2');
    v = v.replace(/(\d{4})(\d)/, '$1-$2');
    this.value = v;
});
document.getElementById('phone')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    this.value = v.trim().replace(/-$/, '');
});
</script>
@endpush
