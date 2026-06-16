@extends('layouts.app')
@section('title', 'Configuração Fiscal')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Cabeçalho --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:2.5rem;height:2.5rem;border-radius:.6rem;background:rgba(14,165,233,.12);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-receipt" style="color:#38BDF8;font-size:1.1rem;"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold" style="color:#F0F9FF;">Configuração Fiscal</h4>
                <p class="mb-0 text-soft" style="font-size:.82rem;">Parâmetros para emissão de NF-e via Focus NFe</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('settings.fiscal.update') }}" method="POST">
            @csrf @method('PATCH')

            {{-- ── Status Focus NFe ─────────────────────────────────────────── --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark py-3">
                    <span class="fw-semibold" style="color:#e2e8f0;">
                        <i class="bi bi-cloud-check me-2 opacity-75"></i>Integração Focus NFe
                    </span>
                </div>
                <div class="card-body">

                    @php $configured = $company->hasFiscalConfigured(); @endphp

                    <div class="mb-3">
                        @if($configured)
                            <span class="badge" style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.3);">
                                <i class="bi bi-check-circle-fill me-1"></i>Token configurado
                            </span>
                        @else
                            <span class="badge" style="background:rgba(148,163,184,.1);color:#94a3b8;border:1px solid rgba(148,163,184,.2);">
                                <i class="bi bi-dash-circle me-1"></i>Não configurado
                            </span>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label text-soft" style="font-size:.82rem;">Ambiente <span class="text-danger">*</span></label>
                            <select name="ambiente_nfe" class="form-select @error('ambiente_nfe') is-invalid @enderror"
                                    style="background:rgba(13,25,41,.7);border-color:rgba(14,165,233,.2);color:#e2e8f0;">
                                <option value="homologacao" @selected(($company->ambiente_nfe ?? 'homologacao') === 'homologacao')>🧪 Homologação (testes)</option>
                                <option value="producao"    @selected($company->ambiente_nfe === 'producao')>🟢 Produção</option>
                            </select>
                            @error('ambiente_nfe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text" style="font-size:.75rem;color:rgba(148,163,184,.5);">Use Homologação para testar antes de emitir documentos reais.</div>
                        </div>

                        <div class="col-12 col-md-8">
                            <label class="form-label text-soft" style="font-size:.82rem;">
                                Token Focus NFe
                                <a href="https://focusnfe.com.br" target="_blank" rel="noopener"
                                   class="ms-2" style="font-size:.75rem;color:#38BDF8;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Acessar painel
                                </a>
                            </label>
                            <input type="password" name="focusnfe_token"
                                   class="form-control @error('focusnfe_token') is-invalid @enderror"
                                   placeholder="{{ $configured ? '●●●●●●●● (deixe em branco para manter)' : 'Cole seu token aqui' }}"
                                   autocomplete="new-password"
                                   style="background:rgba(13,25,41,.7);border-color:rgba(14,165,233,.2);color:#e2e8f0;">
                            @error('focusnfe_token')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text" style="font-size:.75rem;color:rgba(148,163,184,.5);">Seu token nunca é exibido após salvo.</div>
                        </div>
                    </div>

                    @if(! $configured)
                    <div class="mt-3 p-3 rounded" style="background:rgba(14,165,233,.05);border:1px solid rgba(14,165,233,.12);font-size:.82rem;color:rgba(148,163,184,.7);">
                        <i class="bi bi-info-circle me-1" style="color:#38BDF8;"></i>
                        Após configurar, você poderá emitir <strong style="color:#e2e8f0;">NF-e</strong> diretamente pelas vendas do sistema.
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── Dados Tributários ─────────────────────────────────────────── --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark py-3">
                    <span class="fw-semibold" style="color:#e2e8f0;">
                        <i class="bi bi-bank me-2 opacity-75"></i>Dados Tributários
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-soft">Inscrição Estadual</label>
                            <input type="text" name="inscricao_estadual"
                                   class="form-control @error('inscricao_estadual') is-invalid @enderror"
                                   value="{{ old('inscricao_estadual', $company->inscricao_estadual) }}"
                                   placeholder="Ex: 123.456.789.000">
                            @error('inscricao_estadual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-soft">Inscrição Municipal</label>
                            <input type="text" name="inscricao_municipal"
                                   class="form-control @error('inscricao_municipal') is-invalid @enderror"
                                   value="{{ old('inscricao_municipal', $company->inscricao_municipal) }}"
                                   placeholder="Opcional">
                            @error('inscricao_municipal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label text-soft">Regime Tributário <span class="text-danger">*</span></label>
                            <select name="regime_tributario" class="form-select @error('regime_tributario') is-invalid @enderror">
                                <option value="" disabled @selected(! $company->regime_tributario)>— Selecione —</option>
                                @foreach(\App\Models\Company::REGIMES_TRIBUTARIOS as $valor => $label)
                                    <option value="{{ $valor }}" @selected(old('regime_tributario', $company->regime_tributario) == $valor)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('regime_tributario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Numeração NF-e ────────────────────────────────────────────── --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark py-3">
                    <span class="fw-semibold" style="color:#e2e8f0;">
                        <i class="bi bi-hash me-2 opacity-75"></i>Numeração NF-e
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-soft">Série <span class="text-danger">*</span></label>
                            <input type="text" name="serie_nfe"
                                   class="form-control @error('serie_nfe') is-invalid @enderror"
                                   value="{{ old('serie_nfe', $company->serie_nfe ?? '1') }}"
                                   maxlength="3" placeholder="1">
                            @error('serie_nfe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text" style="font-size:.75rem;color:rgba(148,163,184,.5);">Geralmente "1". Altere apenas se orientado pela contabilidade.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-soft">Próximo Número <span class="text-danger">*</span></label>
                            <input type="number" name="proximo_numero_nfe"
                                   class="form-control @error('proximo_numero_nfe') is-invalid @enderror"
                                   value="{{ old('proximo_numero_nfe', $company->proximo_numero_nfe ?? 1) }}"
                                   min="1" placeholder="1">
                            @error('proximo_numero_nfe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text" style="font-size:.75rem;color:rgba(148,163,184,.5);">Número da próxima NF-e a ser emitida.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── NFC-e (CSC) ───────────────────────────────────────────────── --}}
            <div class="card card-dark-bg mb-4">
                <div class="card-header card-header-dark py-3">
                    <span class="fw-semibold" style="color:#e2e8f0;">
                        <i class="bi bi-upc-scan me-2 opacity-75"></i>NFC-e — Código de Segurança do Contribuinte (CSC)
                        <span class="badge ms-2" style="background:rgba(148,163,184,.12);color:#94a3b8;font-size:.7rem;font-weight:500;">Opcional</span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-soft">CSC ID</label>
                            <input type="text" name="csc_id"
                                   class="form-control @error('csc_id') is-invalid @enderror"
                                   value="{{ old('csc_id', $company->csc_id) }}"
                                   placeholder="Ex: 000001">
                            @error('csc_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-soft">CSC Token</label>
                            <input type="password" name="csc_token"
                                   class="form-control @error('csc_token') is-invalid @enderror"
                                   placeholder="{{ $company->csc_token ? '●●●●●●●● (deixe em branco para manter)' : 'Token CSC fornecido pela SEFAZ' }}"
                                   autocomplete="new-password">
                            @error('csc_token')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-2" style="font-size:.78rem;color:rgba(148,163,184,.5);">
                        Necessário apenas para emissão de NFC-e (Nota Fiscal ao Consumidor Eletrônica). Obtenha no portal da SEFAZ do seu estado.
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('settings.company') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>Salvar Configurações
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
