@extends('layouts.app')

@section('title', 'Tokens de API')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center mb-4 gap-2">
        <i class="fas fa-key fa-lg text-primary"></i>
        <h1 class="h3 mb-0">Tokens de API</h1>
    </div>

    {{-- Alerta com o token recém-criado --}}
    @if(session('new_token'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-check-circle fa-lg mt-1"></i>
            <div class="w-100">
                <strong>Token criado! Copie agora — ele não será exibido novamente.</strong>
                <div class="input-group mt-2">
                    <input type="text" class="form-control font-monospace" id="new-token-value"
                           value="{{ session('new_token') }}" readonly>
                    <button class="btn btn-outline-success" type="button"
                            onclick="navigator.clipboard.writeText(document.getElementById('new-token-value').value); this.innerHTML='<i class=\'fas fa-check\'></i> Copiado!'">
                        <i class="fas fa-copy"></i> Copiar
                    </button>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success') && !session('new_token'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Criar novo token --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Novo Token</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.api.tokens.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome do token</label>
                            <input type="text" name="token_name" class="form-control @error('token_name') is-invalid @enderror"
                                   placeholder="Ex: Integração Shopify, ERP Externo..."
                                   value="{{ old('token_name') }}">
                            @error('token_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dê um nome descritivo para identificar onde este token será usado.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-key me-1"></i> Gerar Token
                        </button>
                    </form>
                </div>
            </div>

            {{-- Docs da API --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-book me-2 text-info"></i>Como usar</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Inclua o token no header de todas as requisições:</p>
                    <pre class="bg-dark text-success p-3 rounded small">Authorization: Bearer SEU_TOKEN</pre>
                    <p class="text-muted small mt-3 mb-1"><strong>Base URL:</strong></p>
                    <code class="small">{{ config('app.url') }}/api/v1</code>
                    <hr>
                    <p class="text-muted small fw-semibold mb-2">Endpoints disponíveis:</p>
                    <ul class="list-unstyled small text-muted">
                        <li><span class="badge bg-success me-1">GET</span> /products</li>
                        <li><span class="badge bg-success me-1">GET</span> /products/{id}</li>
                        <li><span class="badge bg-primary me-1">POST</span> /products</li>
                        <li><span class="badge bg-warning text-dark me-1">PUT</span> /products/{id}</li>
                        <li><span class="badge bg-danger me-1">DEL</span> /products/{id}</li>
                        <li class="mt-1"><span class="badge bg-success me-1">GET</span> /customers</li>
                        <li><span class="badge bg-primary me-1">POST</span> /customers</li>
                        <li class="mt-1"><span class="badge bg-success me-1">GET</span> /sales</li>
                        <li><span class="badge bg-primary me-1">POST</span> /sales</li>
                        <li class="mt-1"><span class="badge bg-success me-1">GET</span> /stock</li>
                        <li><span class="badge bg-success me-1">GET</span> /stock/low</li>
                        <li><span class="badge bg-primary me-1">POST</span> /stock/movement</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Tokens existentes --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-secondary"></i>Tokens Ativos</h5>
                    <span class="badge bg-secondary">{{ $tokens->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($tokens->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-key fa-2x mb-2 d-block opacity-30"></i>
                            Nenhum token gerado ainda.
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($tokens as $token)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                <div>
                                    <div class="fw-semibold">{{ $token->name }}</div>
                                    <small class="text-muted">
                                        Criado em {{ $token->created_at->format('d/m/Y \à\s H:i') }}
                                        @if($token->last_used_at)
                                            · Último uso {{ $token->last_used_at->diffForHumans() }}
                                        @else
                                            · <span class="text-warning">Nunca usado</span>
                                        @endif
                                    </small>
                                </div>
                                <form method="POST"
                                      action="{{ route('settings.api.tokens.destroy', $token->id) }}"
                                      onsubmit="return confirm('Revogar este token? Integrações que o usam vão parar de funcionar.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i> Revogar
                                    </button>
                                </form>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
