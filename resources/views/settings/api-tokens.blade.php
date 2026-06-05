@extends('layouts.app')
@section('title', 'Tokens de API')

@section('content')

{{-- Cabeçalho --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="color:var(--brand-ice);">Tokens de API</h2>
        <p class="mb-0" style="font-size:.82rem; color:rgba(148,163,184,.6);">Gere tokens de acesso para integrar o Invexa com sistemas externos via API REST.</p>
    </div>
</div>

{{-- Token recém-criado --}}
@if(session('new_token'))
<div class="mb-4 p-3 rounded-3" style="background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.25);">
    <div class="d-flex align-items-start gap-3">
        <i class="bi bi-check-circle-fill mt-1" style="color:#4ade80; font-size:1.1rem;"></i>
        <div class="w-100">
            <p class="mb-2 fw-semibold" style="color:#4ade80; font-size:.85rem;">Token criado! Copie agora — ele não será exibido novamente.</p>
            <div class="input-group">
                <input type="text" class="form-control font-monospace" id="new-token-value"
                       value="{{ session('new_token') }}" readonly
                       style="background:rgba(13,25,41,.8); border:1px solid rgba(34,197,94,.3); color:#e2e8f0; font-size:.78rem;">
                <button class="btn btn-sm" type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('new-token-value').value); this.innerHTML='<i class=\'bi bi-check-lg\'></i> Copiado!'"
                        style="background:rgba(34,197,94,.15); border:1px solid rgba(34,197,94,.3); color:#4ade80; font-size:.78rem;">
                    <i class="bi bi-clipboard"></i> Copiar
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('success') && !session('new_token'))
<div class="alert alert-dismissible mb-4" role="alert"
     style="background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.25); color:#4ade80; border-radius:10px; font-size:.85rem;">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- Coluna esquerda: formulário + docs --}}
    <div class="col-md-5">

        {{-- Gerar novo token --}}
        <div class="card-dark-bg rounded-3 p-4 mb-4">
            <p class="fw-semibold mb-3" style="font-size:.82rem; text-transform:uppercase; letter-spacing:.07em; color:rgba(148,163,184,.5);">Novo Token</p>
            <form method="POST" action="{{ route('settings.api.tokens.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem; color:rgba(226,232,240,.7);">Nome do token</label>
                    <input type="text" name="token_name"
                           class="form-control @error('token_name') is-invalid @enderror"
                           placeholder="Ex: Integração Shopify, ERP Externo..."
                           value="{{ old('token_name') }}"
                           style="background:rgba(13,25,41,.8); border:1px solid rgba(14,165,233,.15); color:#e2e8f0; font-size:.85rem;">
                    @error('token_name')
                        <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
                    @enderror
                    <div style="font-size:.75rem; color:rgba(148,163,184,.45); margin-top:.35rem;">Dê um nome descritivo para identificar onde este token será usado.</div>
                </div>
                <button type="submit" class="btn btn-sm w-100"
                        style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.3); color:#38BDF8; font-size:.82rem;">
                    <i class="bi bi-key me-1"></i> Gerar Token
                </button>
            </form>
        </div>

        {{-- Como usar --}}
        <div class="card-dark-bg rounded-3 p-4">
            <p class="fw-semibold mb-3" style="font-size:.82rem; text-transform:uppercase; letter-spacing:.07em; color:rgba(148,163,184,.5);">Como usar</p>
            <p style="font-size:.78rem; color:rgba(148,163,184,.55); margin-bottom:.5rem;">Inclua o token no header de todas as requisições:</p>
            <pre class="rounded-2 p-3 mb-3" style="background:rgba(13,25,41,.9); border:1px solid rgba(14,165,233,.1); color:#4ade80; font-size:.75rem; white-space:pre-wrap; word-break:break-all;">Authorization: Bearer SEU_TOKEN</pre>
            <p style="font-size:.78rem; color:rgba(148,163,184,.55); margin-bottom:.25rem;"><strong style="color:rgba(226,232,240,.6);">Base URL:</strong></p>
            <code style="font-size:.75rem; color:#38BDF8;">{{ config('app.url') }}/api/v1</code>
            <hr style="border-color:rgba(14,165,233,.1); margin:1rem 0;">
            <p style="font-size:.78rem; font-weight:600; color:rgba(226,232,240,.5); margin-bottom:.6rem;">Endpoints disponíveis</p>
            <div class="d-flex flex-column gap-1">
                @foreach([
                    ['GET',    '/products'],
                    ['GET',    '/products/{id}'],
                    ['POST',   '/products'],
                    ['PUT',    '/products/{id}'],
                    ['DELETE', '/products/{id}'],
                    ['GET',    '/customers'],
                    ['POST',   '/customers'],
                    ['GET',    '/sales'],
                    ['POST',   '/sales'],
                    ['GET',    '/stock'],
                    ['GET',    '/stock/low'],
                    ['POST',   '/stock/movement'],
                ] as [$method, $path])
                @php
                    $colors = [
                        'GET'    => ['bg'=>'rgba(34,197,94,.12)',  'color'=>'#4ade80'],
                        'POST'   => ['bg'=>'rgba(14,165,233,.12)', 'color'=>'#38BDF8'],
                        'PUT'    => ['bg'=>'rgba(234,179,8,.12)',  'color'=>'#facc15'],
                        'DELETE' => ['bg'=>'rgba(239,68,68,.12)',  'color'=>'#f87171'],
                    ];
                    $c = $colors[$method];
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <span style="display:inline-block; min-width:52px; text-align:center; background:{{ $c['bg'] }}; color:{{ $c['color'] }}; border-radius:4px; font-size:.63rem; font-weight:700; padding:.1rem .3rem; letter-spacing:.04em;">{{ $method }}</span>
                    <span style="font-family:monospace; font-size:.75rem; color:rgba(226,232,240,.6);">{{ $path }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Coluna direita: tokens ativos --}}
    <div class="col-md-7">
        <div class="card-dark-bg rounded-3">
            <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3" style="border-bottom:1px solid rgba(14,165,233,.08);">
                <p class="fw-semibold mb-0" style="font-size:.82rem; text-transform:uppercase; letter-spacing:.07em; color:rgba(148,163,184,.5);">Tokens Ativos</p>
                <span style="background:rgba(14,165,233,.12); color:#38BDF8; border-radius:999px; padding:.1rem .6rem; font-size:.72rem; font-weight:600;">{{ $tokens->count() }}</span>
            </div>

            @if($tokens->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-key d-block mb-2" style="font-size:2rem; color:rgba(14,165,233,.2);"></i>
                    <p class="mb-0" style="font-size:.82rem; color:rgba(148,163,184,.45);">Nenhum token gerado ainda.</p>
                </div>
            @else
                <div>
                    @foreach($tokens as $token)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3"
                         style="border-bottom:1px solid rgba(14,165,233,.06); {{ $loop->last ? 'border-bottom:none;' : '' }}"
                         onmouseover="this.style.background='rgba(14,165,233,.03)'" onmouseout="this.style.background=''"
                    >
                        <div>
                            <div class="fw-semibold" style="font-size:.875rem; color:rgba(226,232,240,.85);">{{ $token->name }}</div>
                            <div style="font-size:.75rem; color:rgba(148,163,184,.5); margin-top:.15rem;">
                                Criado em {{ $token->created_at->format('d/m/Y \à\s H:i') }}
                                &middot;
                                @if($token->last_used_at)
                                    Último uso {{ $token->last_used_at->diffForHumans() }}
                                @else
                                    <span style="color:rgba(234,179,8,.7);">Nunca usado</span>
                                @endif
                            </div>
                        </div>
                        <form method="POST"
                              action="{{ route('settings.api.tokens.destroy', $token->id) }}"
                              onsubmit="return confirm('Revogar este token? Integrações que o usam vão parar de funcionar.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="font-size:.75rem; padding:.25rem .75rem; border-radius:.4rem; border:1px solid rgba(239,68,68,.25); background:rgba(239,68,68,.1); color:#f87171; cursor:pointer;">
                                <i class="bi bi-trash3 me-1"></i>Revogar
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
