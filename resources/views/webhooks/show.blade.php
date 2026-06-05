@extends('layouts.app')
@section('title', 'Detalhes do Webhook')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="color:var(--brand-ice);">Detalhes do Webhook</h2>
        <p class="mb-0" style="font-size:.82rem; color:rgba(148,163,184,.6); font-family:monospace;">{{ $webhook->url }}</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('webhooks.test', $webhook) }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm"
                    style="background:rgba(168,85,247,.15); border:1px solid rgba(168,85,247,.3); color:#c084fc; font-size:.82rem;">
                <i class="bi bi-send me-1"></i>Testar
            </button>
        </form>
        <a href="{{ route('webhooks.edit', $webhook) }}" class="btn btn-sm"
           style="background:rgba(234,179,8,.12); border:1px solid rgba(234,179,8,.25); color:#facc15; font-size:.82rem;">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('webhooks.index') }}" class="btn btn-sm"
           style="background:rgba(148,163,184,.08); border:1px solid rgba(148,163,184,.15); color:rgba(226,232,240,.7); font-size:.82rem;">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Info geral --}}
    <div class="col-12 col-lg-5">
        <div class="card-dark-bg rounded-3 p-4 h-100">
            <div class="mb-1" style="font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(14,165,233,.7);">Configuração</div>

            <div class="mb-3">
                <div style="font-size:.72rem; color:rgba(148,163,184,.5); margin-bottom:.2rem;">URL</div>
                <div style="font-family:monospace; font-size:.82rem; color:#38BDF8; word-break:break-all;">{{ $webhook->url }}</div>
            </div>

            @if($webhook->description)
            <div class="mb-3">
                <div style="font-size:.72rem; color:rgba(148,163,184,.5); margin-bottom:.2rem;">Descrição</div>
                <div style="font-size:.85rem;">{{ $webhook->description }}</div>
            </div>
            @endif

            <div class="mb-3">
                <div style="font-size:.72rem; color:rgba(148,163,184,.5); margin-bottom:.2rem;">Status</div>
                @if($webhook->active)
                    <span style="background:rgba(34,197,94,.12); color:#4ade80; border-radius:999px; padding:.15rem .7rem; font-size:.75rem; font-weight:600;">
                        <i class="bi bi-circle-fill me-1" style="font-size:.4rem; vertical-align:middle;"></i>Ativo
                    </span>
                @else
                    <span style="background:rgba(239,68,68,.12); color:#f87171; border-radius:999px; padding:.15rem .7rem; font-size:.75rem; font-weight:600;">
                        <i class="bi bi-circle-fill me-1" style="font-size:.4rem; vertical-align:middle;"></i>Inativo
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <div style="font-size:.72rem; color:rgba(148,163,184,.5); margin-bottom:.35rem;">Eventos</div>
                @php $events = $webhook->events ?? []; @endphp
                @if(in_array('*', $events))
                    <span class="badge" style="background:rgba(14,165,233,.15); color:#38BDF8;">Todos os eventos</span>
                @else
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($events as $evt)
                            <span class="badge" style="background:rgba(13,25,41,.8); border:1px solid rgba(14,165,233,.2); color:rgba(226,232,240,.7); font-size:.68rem;">{{ $evt }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div style="font-size:.72rem; color:rgba(148,163,184,.5); margin-bottom:.25rem;">Secret (HMAC)</div>
                <div class="d-flex align-items-center gap-2">
                    <code style="font-size:.72rem; color:rgba(226,232,240,.5); background:rgba(13,25,41,.8); padding:.3rem .6rem; border-radius:.4rem; border:1px solid rgba(14,165,233,.1); word-break:break-all;">
                        {{ $webhook->secret ? Str::mask($webhook->secret, '*', 8) : '—' }}
                    </code>
                    <form action="{{ route('webhooks.regenerate-secret', $webhook) }}" method="POST" class="m-0"
                          onsubmit="return confirm('Regenerar o secret? Atualize seu sistema imediatamente após.')">
                        @csrf
                        <button type="submit" style="background:rgba(234,179,8,.1); border:1px solid rgba(234,179,8,.2); color:#facc15; font-size:.72rem; padding:.2rem .6rem; border-radius:.35rem; cursor:pointer;">
                            <i class="bi bi-arrow-clockwise me-1"></i>Regenerar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Entregas recentes --}}
    <div class="col-12 col-lg-7">
        <div class="card-dark-bg rounded-3 p-4 h-100">
            <div class="mb-3" style="font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(14,165,233,.7);">Entregas Recentes</div>
            @if(isset($deliveries) && $deliveries->count())
                @foreach($deliveries as $delivery)
                <div class="mb-2 p-3 rounded" style="background:rgba(13,25,41,.6); border:1px solid rgba(14,165,233,.08);">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span style="font-size:.78rem; font-family:monospace; color:rgba(226,232,240,.7);">{{ $delivery->event }}</span>
                        @if($delivery->response_code >= 200 && $delivery->response_code < 300)
                            <span style="background:rgba(34,197,94,.12); color:#4ade80; font-size:.68rem; padding:.1rem .5rem; border-radius:999px;">{{ $delivery->response_code }}</span>
                        @else
                            <span style="background:rgba(239,68,68,.12); color:#f87171; font-size:.68rem; padding:.1rem .5rem; border-radius:999px;">{{ $delivery->response_code ?? 'falhou' }}</span>
                        @endif
                    </div>
                    <div style="font-size:.7rem; color:rgba(148,163,184,.45);">{{ $delivery->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4" style="color:rgba(148,163,184,.4); font-size:.82rem;">
                    <i class="bi bi-inbox d-block fs-4 mb-2 opacity-50"></i>
                    Nenhuma entrega registrada ainda.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
