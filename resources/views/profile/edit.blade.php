@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        {{-- Cabeçalho --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="user-avatar" style="width:3rem;height:3rem;font-size:1.1rem;border-radius:.6rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h4 class="mb-0 text-white fw-semibold">Editar Perfil</h4>
                <p class="text-soft mb-0" style="font-size:.85rem;">
                    <span class="badge bg-opacity-20 bg-{{ $user->role_badge }} text-{{ $user->role_badge }}" style="font-size:.7rem;">
                        {{ $user->role_label }}
                    </span>
                    &middot; {{ $user->company->name ?? 'Empresa não definida' }}
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
            @if(Auth::user()->hasRole('admin'))
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
            @if(Auth::user()->hasRole('admin'))
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
            </div>
            @endif

        </div>{{-- /tab-content --}}
    </div>
</div>
@endsection

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
</style>
@endpush

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
    const bar = document.getElementById('strengthBar');
    const fill = document.getElementById('strengthFill');
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
        { pct: '20%', color: '#ef4444', text: 'Muito fraca' },
        { pct: '40%', color: '#f97316', text: 'Fraca' },
        { pct: '60%', color: '#eab308', text: 'Média' },
        { pct: '80%', color: '#22c55e', text: 'Forte' },
        { pct: '100%', color: '#10b981', text: 'Muito forte' },
    ];
    const lvl = levels[Math.min(score, 4)];
    fill.style.width = lvl.pct;
    fill.style.backgroundColor = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;
}
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
// Máscara CNPJ
document.getElementById('cnpj')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 14);
    v = v.replace(/(\d{2})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2')
         .replace(/(\d{3})(\d)/, '$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
    this.value = v;
});
// Máscara telefone
document.getElementById('phone_company')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    this.value = v.trim().replace(/-$/, '');
});

// Abre aba empresa se vier erro de empresa
@if($errors->has('name') || $errors->has('cnpj') || $errors->has('phone') || $errors->has('address') || $errors->has('logo'))
    document.addEventListener('DOMContentLoaded', () => {
        const tab = document.querySelector('#tab-empresa');
        if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
    });
@endif
</script>
@endpush
