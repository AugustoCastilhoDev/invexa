@extends('layouts.app')

@section('title', 'Editar Perfil')

@push('styles')
<style>
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

        {{-- Aviso de atalho para configurações (somente admin) --}}
        @if(Auth::user()->isAdmin())
        <div class="mb-4 p-3 rounded d-flex align-items-center gap-3"
             style="background:rgba(14,165,233,.07);border:1px solid rgba(14,165,233,.18);">
            <i class="bi bi-building" style="font-size:1.1rem;color:#38BDF8;flex-shrink:0;"></i>
            <div style="font-size:.84rem;color:rgba(148,163,184,.85);">
                Para editar os dados da empresa, logo, informações fiscais e integração Pix,
                acesse
                <a href="{{ route('settings.company') }}" style="color:#38BDF8;font-weight:600;">
                    Configurações da Empresa <i class="bi bi-arrow-right ms-1" style="font-size:.75rem;"></i>
                </a>
            </div>
        </div>
        @endif

        {{-- ── DADOS PESSOAIS ── --}}
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
</script>
@endpush
