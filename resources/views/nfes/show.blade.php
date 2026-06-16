@extends('layouts.app')

@section('title', 'NF-e ' . ($nfe->numero_formatado ?? 'Nova'))

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('nfes.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Voltar</a>
        <h1 class="h4 mb-0">NF-e {{ $nfe->numero_formatado ?? 'Pendente' }}</h1>
        <span class="badge bg-{{ $nfe->status_badge }}">{{ $nfe->status_label }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row g-3">

        {{-- Dados Gerais --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Dados Gerais</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Número</dt>
                        <dd class="col-7">{{ $nfe->numero_formatado ?? '—' }}</dd>

                        <dt class="col-5">Série</dt>
                        <dd class="col-7">{{ $nfe->serie }}</dd>

                        <dt class="col-5">Ambiente</dt>
                        <dd class="col-7"><span class="badge bg-{{ $nfe->ambiente === 'producao' ? 'primary' : 'secondary' }}">{{ ucfirst($nfe->ambiente) }}</span></dd>

                        <dt class="col-5">Emissão</dt>
                        <dd class="col-7">{{ $nfe->data_emissao?->format('d/m/Y H:i') ?? '—' }}</dd>

                        <dt class="col-5">Autorização</dt>
                        <dd class="col-7">{{ $nfe->data_autorizacao?->format('d/m/Y H:i') ?? '—' }}</dd>

                        <dt class="col-5">Chave de Acesso</dt>
                        <dd class="col-7"><small class="font-monospace">{{ $nfe->chave_acesso ?? '—' }}</small></dd>

                        <dt class="col-5">Protocolo</dt>
                        <dd class="col-7">{{ $nfe->protocolo ?? '—' }}</dd>

                        <dt class="col-5">Ref. Focus</dt>
                        <dd class="col-7"><small class="text-muted">{{ $nfe->ref_focusnfe }}</small></dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Valores --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Valores</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Produtos</dt>
                        <dd class="col-6">R$ {{ number_format($nfe->valor_produtos, 2, ',', '.') }}</dd>

                        <dt class="col-6">Desconto</dt>
                        <dd class="col-6">R$ {{ number_format($nfe->valor_desconto, 2, ',', '.') }}</dd>

                        <dt class="col-6">Frete</dt>
                        <dd class="col-6">R$ {{ number_format($nfe->valor_frete, 2, ',', '.') }}</dd>

                        <dt class="col-6 fw-bold">Total</dt>
                        <dd class="col-6 fw-bold">R$ {{ number_format($nfe->valor_total, 2, ',', '.') }}</dd>

                        <dt class="col-6">ICMS</dt>
                        <dd class="col-6">R$ {{ number_format($nfe->valor_icms, 2, ',', '.') }}</dd>

                        <dt class="col-6">PIS</dt>
                        <dd class="col-6">R$ {{ number_format($nfe->valor_pis, 2, ',', '.') }}</dd>

                        <dt class="col-6">COFINS</dt>
                        <dd class="col-6">R$ {{ number_format($nfe->valor_cofins, 2, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Ações --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">Ações</div>
                <div class="card-body d-flex flex-wrap gap-2">

                    {{-- Consultar --}}
                    <form action="{{ route('nfes.consultar', $nfe) }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-primary btn-sm">🔄 Consultar Status</button>
                    </form>

                    {{-- XML / DANFE --}}
                    @if($nfe->isAutorizada())
                        <a href="{{ route('nfes.xml', $nfe) }}" class="btn btn-outline-secondary btn-sm" target="_blank">📄 Baixar XML</a>
                        <a href="{{ route('nfes.danfe', $nfe) }}" class="btn btn-outline-secondary btn-sm" target="_blank">🖨️ DANFE (PDF)</a>
                    @endif

                    {{-- Cancelar --}}
                    @if($nfe->isAutorizada())
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalCancelar">❌ Cancelar NF-e</button>
                    @endif

                    {{-- CC-e --}}
                    @if($nfe->isAutorizada())
                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalCce">✏️ Carta de Correção</button>
                    @endif

                </div>
            </div>
        </div>

        {{-- Motivo rejeição --}}
        @if($nfe->motivo_rejeicao)
        <div class="col-12">
            <div class="alert alert-danger mb-0">
                <strong>Motivo da rejeição:</strong> {{ $nfe->motivo_rejeicao }}
            </div>
        </div>
        @endif

        {{-- CC-e enviada --}}
        @if($nfe->cce_correcao)
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header fw-semibold text-warning">Carta de Correção</div>
                <div class="card-body">
                    <p class="mb-1">{{ $nfe->cce_correcao }}</p>
                    <small class="text-muted">Protocolo: {{ $nfe->cce_protocolo ?? '—' }} — {{ $nfe->cce_data?->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal Cancelar --}}
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('nfes.cancelar', $nfe) }}" method="POST" class="modal-content">
            @csrf @method('POST')
            <div class="modal-header">
                <h5 class="modal-title">Cancelar NF-e</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Justificativa <span class="text-danger">*</span></label>
                <textarea name="justificativa" class="form-control" rows="3" minlength="15" required
                    placeholder="Mínimo 15 caracteres..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal CC-e --}}
<div class="modal fade" id="modalCce" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('nfes.carta-correcao', $nfe) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Carta de Correção Eletrônica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Texto da Correção <span class="text-danger">*</span></label>
                <textarea name="correcao" class="form-control" rows="4" minlength="15" required
                    placeholder="Descreva a correção (mínimo 15 caracteres)..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-warning">Enviar CC-e</button>
            </div>
        </form>
    </div>
</div>
@endsection
