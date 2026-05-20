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

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>

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
// Máscara CNPJ
document.getElementById('cnpj')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 14);
    v = v.replace(/(\d{2})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1/$2');
    v = v.replace(/(\d{4})(\d)/, '$1-$2');
    this.value = v;
});
// Máscara telefone
document.getElementById('phone')?.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    this.value = v.trim().replace(/-$/, '');
});
</script>
@endpush
