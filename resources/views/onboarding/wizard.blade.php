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
            display: flex; align-items: center; justify-content: center;
            padding: 32px 16px; color: #e2e8f0;
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
            color: #e2e8f0 !important; border-radius: 8px !important;
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

        /* Stepper */
        .stepper { display: flex; align-items: center; gap: 0; margin-bottom: 2rem; }
        .step-item { display: flex; align-items: center; flex: 1; }
        .step-item:last-child { flex: 0; }
        .step-circle {
            width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem; transition: all .3s;
        }
        .step-circle.done  { background: rgba(34,197,94,.2); border: 1.5px solid #4ade80; color: #4ade80; }
        .step-circle.active { background: rgba(14,165,233,.2); border: 1.5px solid var(--sky); color: var(--elec); }
        .step-circle.pending { background: rgba(148,163,184,.07); border: 1.5px solid rgba(148,163,184,.2); color: rgba(148,163,184,.4); }
        .step-label { font-size: .72rem; color: rgba(148,163,184,.6); margin-top: .25rem; white-space: nowrap; }
        .step-label.active-label { color: var(--elec); font-weight: 600; }
        .step-connector { flex: 1; height: 1px; background: rgba(14,165,233,.15); margin: 0 6px; margin-bottom: 1rem; }
        .step-connector.done-line { background: rgba(34,197,94,.35); }

        .upload-area {
            border: 2px dashed rgba(14,165,233,.25);
            border-radius: 10px; padding: 1.5rem;
            text-align: center; cursor: pointer; transition: border-color .2s;
        }
        .upload-area:hover { border-color: var(--sky); }
        .logo-preview { width: 80px; height: 80px; border-radius: 12px; object-fit: cover;
            border: 2px solid rgba(14,165,233,.3); display: none; }

        .skip-step { font-size:.78rem; color:rgba(226,232,240,.35); cursor:pointer;
            text-decoration:none; transition: color .2s; }
        .skip-step:hover { color: rgba(226,232,240,.6); }
    </style>
</head>
<body>
<div class="wizard-container">

    {{-- Logo --}}
    <div class="text-center mb-4">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
            <svg width="30" height="30" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="7" fill="#0D1929"/>
                <path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/>
                <circle cx="24" cy="10" r="2.2" fill="#38BDF8"/>
            </svg>
            <span style="font-size:1.3rem; font-weight:800; color:var(--ice);">Invexa</span>
        </div>
        <p class="text-soft small mb-0">Configure tudo em menos de 2 minutos</p>
    </div>

    <div class="card card-wizard">
        <div class="card-body p-4">

            {{-- Stepper --}}
            <div class="stepper">
                {{-- Step 1 --}}
                <div class="d-flex flex-column align-items-center">
                    <div class="step-circle {{ $step > 1 ? 'done' : ($step === 1 ? 'active' : 'pending') }}">
                        @if($step > 1) <i class="bi bi-check-lg"></i> @else 1 @endif
                    </div>
                    <div class="step-label {{ $step === 1 ? 'active-label' : '' }}">Empresa</div>
                </div>
                <div class="step-connector {{ $step > 1 ? 'done-line' : '' }}"></div>
                {{-- Step 2 --}}
                <div class="d-flex flex-column align-items-center">
                    <div class="step-circle {{ $step > 2 ? 'done' : ($step === 2 ? 'active' : 'pending') }}">
                        @if($step > 2) <i class="bi bi-check-lg"></i> @else 2 @endif
                    </div>
                    <div class="step-label {{ $step === 2 ? 'active-label' : '' }}">Produto</div>
                </div>
                <div class="step-connector {{ $step > 2 ? 'done-line' : '' }}"></div>
                {{-- Step 3 --}}
                <div class="d-flex flex-column align-items-center">
                    <div class="step-circle {{ $step === 3 ? 'active' : 'pending' }}">
                        3
                    </div>
                    <div class="step-label {{ $step === 3 ? 'active-label' : '' }}">Cliente</div>
                </div>
            </div>

            {{-- Erros --}}
            @if($errors->any())
            <div class="alert mb-4" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171;border-radius:8px;">
                <i class="bi bi-exclamation-circle me-2"></i>
                <ul class="mb-0 mt-1 ps-3 small">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="step" value="{{ $step }}">

                {{-- ═══ PASSO 1: EMPRESA ═══ --}}
                @if($step === 1)
                <div class="mb-1">
                    <h5 class="fw-bold mb-0" style="color:var(--ice);">Sua empresa</h5>
                    <p class="text-soft small mb-4">Olá, <strong style="color:var(--elec);">{{ auth()->user()->name }}</strong>! Configure os dados da sua empresa.</p>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-image me-1"></i>Logo <span class="text-soft">(opcional)</span></label>
                    <div class="upload-area" onclick="document.getElementById('logo').click()">
                        <img id="logoPreview" class="logo-preview mb-2" src="" alt="preview">
                        <div id="uploadText">
                            <i class="bi bi-cloud-upload" style="font-size:1.5rem; color:var(--sky);"></i>
                            <p class="text-soft small mb-0 mt-1">Clique para enviar · JPG, PNG ou WebP · Máx. 2MB</p>
                        </div>
                    </div>
                    <input type="file" id="logo" name="logo" accept="image/*" class="d-none">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-building me-1"></i>Nome da Empresa <span style="color:#f87171;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $company->name) }}" placeholder="Razão social ou nome fantasia" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label"><i class="bi bi-card-text me-1"></i>CNPJ <span class="text-soft">(opcional)</span></label>
                        <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ old('cnpj', $company->cnpj) }}" placeholder="00.000.000/0000-00" maxlength="18">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label"><i class="bi bi-telephone me-1"></i>Telefone <span class="text-soft">(opcional)</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $company->phone) }}" placeholder="(00) 00000-0000" maxlength="15">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Endereço <span class="text-soft">(opcional)</span></label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}" placeholder="Rua, número, cidade — UF">
                </div>
                @endif

                {{-- ═══ PASSO 2: PRIMEIRO PRODUTO ═══ --}}
                @if($step === 2)
                <div class="mb-1">
                    <h5 class="fw-bold mb-0" style="color:var(--ice);">Cadastre seu primeiro produto</h5>
                    <p class="text-soft small mb-4">Opcional — você pode pular e adicionar depois.</p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-box-seam me-1"></i>Nome do Produto</label>
                    <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror"
                           value="{{ old('product_name') }}" placeholder="Ex: Camisa Polo Branca P">
                    @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="form-label"><i class="bi bi-currency-dollar me-1"></i>Preço de Venda (R$)</label>
                        <input type="number" name="product_price" step="0.01" min="0" class="form-control @error('product_price') is-invalid @enderror"
                               value="{{ old('product_price', '0.00') }}" placeholder="0,00">
                        @error('product_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label"><i class="bi bi-stack me-1"></i>Quantidade em Estoque</label>
                        <input type="number" name="product_qty" min="0" class="form-control @error('product_qty') is-invalid @enderror"
                               value="{{ old('product_qty', '0') }}" placeholder="0">
                        @error('product_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endif

                {{-- ═══ PASSO 3: PRIMEIRO CLIENTE ═══ --}}
                @if($step === 3)
                <div class="mb-1">
                    <h5 class="fw-bold mb-0" style="color:var(--ice);">Adicione seu primeiro cliente</h5>
                    <p class="text-soft small mb-4">Opcional — você pode pular e adicionar depois.</p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person me-1"></i>Nome do Cliente</label>
                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                           value="{{ old('customer_name') }}" placeholder="Nome completo ou razão social">
                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i>E-mail <span class="text-soft">(opcional)</span></label>
                        <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                               value="{{ old('customer_email') }}" placeholder="cliente@email.com">
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label"><i class="bi bi-telephone me-1"></i>Telefone <span class="text-soft">(opcional)</span></label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control"
                               value="{{ old('customer_phone') }}" placeholder="(00) 00000-0000" maxlength="15">
                    </div>
                </div>
                @endif

                {{-- Botões --}}
                <div class="d-flex gap-2 align-items-center">
                    <button type="submit" class="btn btn-sky flex-grow-1 py-2">
                        @if($step < 3)
                            Próximo <i class="bi bi-arrow-right ms-1"></i>
                        @else
                            <i class="bi bi-check2-circle me-1"></i> Concluir
                        @endif
                    </button>
                </div>
            </form>

            {{-- Pular passo (steps 2 e 3) ou pular tudo (step 1) --}}
            <div class="text-center mt-3">
                @if($step === 1)
                    <form action="{{ route('onboarding.skip') }}" method="POST">
                        @csrf
                        <button type="submit" class="skip-step btn btn-link p-0">Pular configuração, ir para o painel</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('onboarding.store') }}">
                        @csrf
                        <input type="hidden" name="step" value="{{ $step }}">
                        <button type="submit" class="skip-step btn btn-link p-0">Pular este passo</button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    {{-- Progresso texto --}}
    <p class="text-center text-soft small mt-3 mb-0">
        Passo {{ $step }} de 3 &nbsp;&middot;&nbsp;
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="color:rgba(226,232,240,.3);">Sair</a>
    </p>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview do logo
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
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
    }

    // Máscara CNPJ
    const cnpjInput = document.getElementById('cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 14);
            v = v.replace(/(\d{2})(\d)/, '$1.$2');
            v = v.replace(/(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = v;
        });
    }

    // Máscara Telefone empresa
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 11);
            v = v.length <= 10
                ? v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
                : v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            e.target.value = v;
        });
    }

    // Máscara Telefone cliente
    const custPhone = document.getElementById('customer_phone');
    if (custPhone) {
        custPhone.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 11);
            v = v.length <= 10
                ? v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
                : v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            e.target.value = v;
        });
    }
</script>
</body>
</html>
