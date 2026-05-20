<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurar Empresa — Invexa</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23080D1A'/%3E%3Cpath d='M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z' fill='%230EA5E9'/%3E%3Ccircle cx='24' cy='10' r='2.2' fill='%2338BDF8'/%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --abyss:#080D1A; --navy:#0D1929; --sky:#0EA5E9; --elec:#38BDF8; --ice:#F0F9FF; }
        body {
            background: var(--abyss);
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(14,165,233,.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 80%, rgba(56,189,248,.05) 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            color: #e2e8f0;
        }
        .wizard-container { width: 100%; max-width: 560px; }
        .card-wizard {
            background: rgba(13,25,41,.97);
            border: 1px solid rgba(14,165,233,.15);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
        }
        .form-control, .form-select {
            background: rgba(8,13,26,.7) !important;
            border: 1px solid rgba(14,165,233,.2) !important;
            color: #e2e8f0 !important;
            border-radius: 8px !important;
        }
        .form-control:focus {
            border-color: var(--sky) !important;
            box-shadow: 0 0 0 .2rem rgba(14,165,233,.2) !important;
            color: #e2e8f0 !important;
        }
        .form-control::placeholder { color: rgba(226,232,240,.35); }
        .form-label { color: #94a3b8; font-weight: 500; font-size: .875rem; }
        .btn-sky { background: linear-gradient(135deg,var(--sky),var(--elec)); border:none; color:#fff; font-weight:700; }
        .btn-sky:hover { opacity:.88; color:#fff; }
        .text-soft { color: rgba(226,232,240,.5); }
        .step-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(14,165,233,.15); border: 1px solid rgba(14,165,233,.3);
            color: var(--elec); font-weight: 700; font-size: .85rem;
        }
        .logo-preview {
            width: 80px; height: 80px; border-radius: 12px;
            object-fit: cover; border: 2px solid rgba(14,165,233,.3);
            display: none;
        }
        .upload-area {
            border: 2px dashed rgba(14,165,233,.25);
            border-radius: 10px; padding: 1.5rem;
            text-align: center; cursor: pointer;
            transition: border-color .2s;
        }
        .upload-area:hover { border-color: var(--sky); }
    </style>
</head>
<body>
<div class="wizard-container">

    {{-- Logo --}}
    <div class="text-center mb-4">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#0D1929"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
            <span style="font-size:1.4rem; font-weight:800; color:var(--ice);">Invexa</span>
        </div>
        <p class="text-soft small mb-0">Vamos configurar sua empresa em menos de 2 minutos</p>
    </div>

    <div class="card card-wizard">
        <div class="card-body p-4">

            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="step-badge">1</div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--ice);">Configure sua empresa</h5>
                    <p class="text-soft small mb-0">Olá, <strong style="color:var(--elec);">{{ auth()->user()->name }}</strong>! Preencha os dados abaixo.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert mb-4" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171;border-radius:8px;">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <ul class="mb-0 mt-1 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Logo Upload --}}
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-image me-1"></i>Logo da Empresa <span class="text-soft">(opcional)</span></label>
                    <div class="upload-area" onclick="document.getElementById('logo').click()">
                        <img id="logoPreview" class="logo-preview mb-2" src="" alt="preview">
                        <div id="uploadText">
                            <i class="bi bi-cloud-upload" style="font-size:1.5rem; color:var(--sky);"></i>
                            <p class="text-soft small mb-0 mt-1">Clique para enviar · JPG, PNG ou WebP · Máx. 2MB</p>
                        </div>
                    </div>
                    <input type="file" id="logo" name="logo" accept="image/*" class="d-none">
                </div>

                {{-- Nome --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-building me-1"></i>Nome da Empresa <span style="color:#f87171;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $company->name) }}" placeholder="Razão social ou nome fantasia" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- CNPJ --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-card-text me-1"></i>CNPJ <span class="text-soft">(opcional)</span></label>
                    <input type="text" name="cnpj" id="cnpj" class="form-control @error('cnpj') is-invalid @enderror"
                           value="{{ old('cnpj', $company->cnpj) }}" placeholder="00.000.000/0000-00" maxlength="18">
                    @error('cnpj')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Telefone --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-telephone me-1"></i>Telefone <span class="text-soft">(opcional)</span></label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $company->phone) }}" placeholder="(00) 00000-0000" maxlength="15">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Endereço --}}
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Endereço <span class="text-soft">(opcional)</span></label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address', $company->address) }}" placeholder="Rua, número, cidade — UF">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sky flex-grow-1 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Salvar e Continuar
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <form action="{{ route('onboarding.skip') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link text-soft small p-0" style="font-size:.8rem;">
                        Pular por agora, configurar depois
                    </button>
                </form>
            </div>
        </div>
    </div>

    <p class="text-center text-soft small mt-3 mb-0">
        Seus dados estão protegidos · <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color:rgba(226,232,240,.4);">Sair</a>
    </p>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview do logo
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('logoPreview');
            const text = document.getElementById('uploadText');
            preview.src = ev.target.result;
            preview.style.display = 'block';
            text.innerHTML = '<p class="text-soft small mb-0 mt-1">' + file.name + '</p>';
        };
        reader.readAsDataURL(file);
    });

    // Máscara CNPJ
    document.getElementById('cnpj').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '').substring(0, 14);
        v = v.replace(/(\d{2})(\d)/, '$1.$2');
        v = v.replace(/(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
        v = v.replace(/(\d{4})(\d)/, '$1-$2');
        e.target.value = v;
    });

    // Máscara Telefone
    document.getElementById('phone').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '').substring(0, 11);
        if (v.length <= 10) {
            v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
        e.target.value = v;
    });
</script>
</body>
</html>
