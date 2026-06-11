@extends('layouts.app')

@section('title', 'Editar Perfil')

@push('styles')
<style>
.nav-tabs .nav-link.active {
    background: rgba(14,165,233,.12) !important;
    border-color: rgba(14,165,233,.3) rgba(14,165,233,.3) transparent !important;
    color: #38BDF8 !important;
}
.nav-tabs .nav-link:hover:not(.active) {
    background: rgba(14,165,233,.05);
    color: #e2e8f0 !important;
    border-color: transparent;
}
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: .18rem .55rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .03em;
    line-height: 1;
}
.role-badge.admin    { background: rgba(14,165,233,.15); border: 1px solid rgba(14,165,233,.3); color: #38BDF8; }
.role-badge.gerente  { background: rgba(168,85,247,.15); border: 1px solid rgba(168,85,247,.3); color: #c084fc; }
.role-badge.vendedor { background: rgba(34,197,94,.15);  border: 1px solid rgba(34,197,94,.3);  color: #4ade80; }
.role-badge.default  { background: rgba(148,163,184,.12); border: 1px solid rgba(148,163,184,.2); color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        {{-- Cabeçalho --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="user-avatar" style="width:3rem;height:3rem;font-size:1.1rem;border-radius:.6rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h4 class="mb-0 fw-semibold" style="color:#F0F9FF;">Editar Perfil</h4>
                <p class="mb-0" style="font-size:.85rem; color:rgba(148,163,184,.7);">
                    @php
                        $roleClass = match($user->role) {
                            'admin'    => 'admin',
                            'gerente'  => 'gerente',
                            'vendedor' => 'vendedor',
                            default    => 'default',
                        };
                    @endphp
                    <span class="role-badge {{ $roleClass }}">{{ $user->role_label }}</span>
                    <span class="mx-1" style="color:rgba(148,163,184,.35);">·</span>
                    <span style="color:rgba(148,163,184,.6);">{{ $user->company->name ?? 'Empresa não definida' }}</span>
                </p>
            </div>
        </div>

        {{-- Abas --}}
        <ul class="nav nav-tabs mb-4" id="profileTabs" style="border-color:rgba(14,165,233,.15);">
            <li class="nav-item">
                <a class="nav-link active" id="tab-pessoal" href="#pessoal" data-bs-toggle="tab"
                   style="color:rgba(226,232,240,.7); border-color:transparent;">
                    <i class="bi bi-person me-1"></i>Dados Pessoais
                </a>
            </li>
            @if(Auth::user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link" id="tab-empresa" href="#empresa" data-bs-toggle="tab"
                   style="color:rgba(226,232,240,.7); border-color:transparent;">
                    <i class="bi bi-building me-1"></i>Empresa
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">

            {{-- ── ABA DADOS PESSOAIS ── --}}
            <div class="tab-pane fade show active" id="pessoal">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="card card-dark-bg shadow-sm border-0 mb-4">
                        <div class="card-header card-header-dark border-bottom">
                            <h6 class="mb-0 text-white fw-semibold">
                                <i class="bi bi-person me-2 text-primary"></i>Dados Pessoais
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label text-soft fw-semibold">
                                        Nome completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}"
                                           placeholder="Seu nome completo" required autofocus>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label text-soft fw-semibold">
                                        E-mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}"
                                           placeholder="seu@email.com" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-dark-bg shadow-sm border-0 mb-4">
                        <div class="card-header card-header-dark border-bottom">
                            <h6 class="mb-0 text-white fw-semibold">
                                <i class="bi bi-shield-lock me-2 text-warning"></i>Alterar Senha
                                <small class="text-soft fw-normal ms-2" style="font-size:.75rem;">Deixe em branco para manter a atual</small>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label text-soft fw-semibold">Senha atual</label>
                                    <div class="input-group">
                                        <input type="password" id="current_password" name="current_password"
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               placeholder="Digite sua senha atual" autocomplete="current-password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label text-soft fw-semibold">Nova senha</label>
                                    <div class="input-group">
                                        <input type="password" id="password" name="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="Mínimo 8 caracteres" autocomplete="new-password"
                                               oninput="checkStrength(this.value)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mt-2" id="strengthBar" style="display:none;">
                                        <div class="progress" style="height:4px;background:rgba(148,163,184,.15);">
                                            <div id="strengthFill" class="progress-bar" style="width:0%;transition:width .3s;"></div>
                                        </div>
                                        <small id="strengthLabel" class="text-soft" style="font-size:.72rem;"></small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="password_confirmation" class="form-label text-soft fw-semibold">Confirmar nova senha</label>
                                    <div class="input-group">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                               placeholder="Repita a nova senha" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Salvar alterações
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

            {{-- ── ABA EMPRESA (somente admin) ── --}}
            @if(Auth::user()->isAdmin())
            <div class="tab-pane fade" id="empresa">
                @php $company = Auth::user()->company; @endphp

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

                    {{-- Dados --}}
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
                                    <input type="text" name="phone" id="phone_company" class="form-control @error('phone') is-invalid @enderror"
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

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Salvar Alterações
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>

                {{-- ── Integração Pix / Asaas ──────────────────────────── --}}
                <hr class="my-4" style="border-color:rgba(14,165,233,.15);">

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
            @endif

        </div>{{-- /tab-content --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
function checkStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (!val) { bar.style.display = 'none'; return; }
    bar.style.display = '';
    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { pct: '20%',  color: '#ef4444', text: 'Muito fraca' },
        { pct: '40%',  color: '#f97316', text: 'Fraca' },
        { pct: '60%',  color: '#eab308', text: 'Média' },
        { pct: '80%',  color: '#22c55e', text: 'Forte' },
        { pct: '100%', color: '#10b981', text: 'Muito forte' },
    ];
    const lvl = levels[Math.min(score, 4)];
    fill.style.width           = lvl.pct;
    fill.style.backgroundColor = lvl.color;
    label.textContent          = lvl.text;
    label.style.color          = lvl.color;
}
function previewLogo(input) {
    const preview     = document.getElementById('logoPreview');
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
    v = v.replace(/(\d{2})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2')
         .replace(/(\d{3})(\d)/, '$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
    this.value = v;
});
document.getElementById('phone_company')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else                v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    this.value = v.trim().replace(/-$/, '');
});
@if($errors->has('name') || $errors->has('cnpj') || $errors->has('phone') || $errors->has('address') || $errors->has('logo'))
    document.addEventListener('DOMContentLoaded', () => {
        const tab = document.querySelector('#tab-empresa');
        if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
    });
@endif
</script>
@endpush
