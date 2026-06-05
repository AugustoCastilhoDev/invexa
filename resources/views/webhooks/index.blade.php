@extends('layouts.app')
@section('title', 'Webhooks')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="color:var(--brand-ice);">Webhooks</h2>
        <p class="mb-0" style="font-size:.82rem; color:rgba(148,163,184,.6);">Receba notificações automáticas em sua URL quando eventos ocorrerem no Invexa.</p>
    </div>
    @if($canCreate)
    <a href="{{ route('webhooks.create') }}" class="btn btn-sm"
       style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.3); color:#38BDF8; font-size:.82rem;">
        <i class="bi bi-plus-lg me-1"></i>Novo Webhook
    </a>
    @endif
</div>

{{-- Aviso de plano --}}
@if(!$isBusiness)
<div class="alert mb-4" style="background:rgba(168,85,247,.1); border:1px solid rgba(168,85,247,.25); color:#c084fc; border-radius:10px;">
    <i class="bi bi-stars me-2"></i>
    Webhooks estão disponíveis apenas no <strong>Plano Business</strong>.
    <a href="{{ route('upgrade') }}" class="ms-2" style="color:#c084fc; font-weight:600;">Fazer upgrade →</a>
</div>
@endif

@if($webhooks->isEmpty())
<div class="card-dark-bg rounded-3 p-5 text-center">
    <i class="bi bi-arrow-repeat d-block mb-3" style="font-size:2.5rem; color:rgba(14,165,233,.3);"></i>
    <p class="mb-1 fw-semibold" style="color:rgba(226,232,240,.6);">Nenhum webhook configurado</p>
    <p class="mb-3" style="font-size:.8rem; color:rgba(148,163,184,.45);">Crie um webhook para integrar o Invexa com outros sistemas.</p>
    @if($canCreate)
    <a href="{{ route('webhooks.create') }}" class="btn btn-sm"
       style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.3); color:#38BDF8;">
        <i class="bi bi-plus-lg me-1"></i>Criar primeiro webhook
    </a>
    @endif
</div>
@else
<div class="table-responsive">
    <table class="w-100" style="border-collapse:collapse; font-size:.875rem;">
        <thead>
            <tr style="border-bottom:1px solid rgba(14,165,233,.1);">
                <th style="padding:.6rem 1rem; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(148,163,184,.5); background:rgba(13,25,41,.6);">URL</th>
                <th style="padding:.6rem 1rem; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(148,163,184,.5); background:rgba(13,25,41,.6);">Eventos</th>
                <th style="padding:.6rem 1rem; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(148,163,184,.5); background:rgba(13,25,41,.6);">Status</th>
                <th style="padding:.6rem 1rem; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(148,163,184,.5); background:rgba(13,25,41,.6);">Criado em</th>
                <th style="padding:.6rem 1rem; text-align:center; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(148,163,184,.5); background:rgba(13,25,41,.6);">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($webhooks as $webhook)
            <tr style="border-bottom:1px solid rgba(14,165,233,.06); transition:background .15s;" onmouseover="this.style.background='rgba(14,165,233,.04)'" onmouseout="this.style.background=''">
                <td style="padding:.75rem 1rem; color:rgba(226,232,240,.8); max-width:280px;">
                    <div class="text-truncate" style="font-family:monospace; font-size:.8rem; color:#38BDF8;" title="{{ $webhook->url }}">{{ $webhook->url }}</div>
                    @if($webhook->description)
                        <div style="font-size:.72rem; color:rgba(148,163,184,.5); margin-top:.15rem;">{{ $webhook->description }}</div>
                    @endif
                </td>
                <td style="padding:.75rem 1rem;">
                    @php $events = $webhook->events ?? []; @endphp
                    @if(in_array('*', $events))
                        <span class="badge" style="background:rgba(14,165,233,.15); color:#38BDF8; font-size:.68rem;">Todos os eventos</span>
                    @else
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(array_slice($events, 0, 3) as $evt)
                                <span class="badge" style="background:rgba(13,25,41,.8); border:1px solid rgba(14,165,233,.2); color:rgba(226,232,240,.7); font-size:.65rem;">{{ $evt }}</span>
                            @endforeach
                            @if(count($events) > 3)
                                <span class="badge" style="background:rgba(148,163,184,.1); color:rgba(148,163,184,.6); font-size:.65rem;">+{{ count($events) - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </td>
                <td style="padding:.75rem 1rem;">
                    @if($webhook->active)
                        <span class="d-inline-flex align-items-center gap-1" style="background:rgba(34,197,94,.12); color:#4ade80; border-radius:999px; padding:.15rem .6rem; font-size:.7rem; font-weight:600;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;"></span>Ativo
                        </span>
                    @else
                        <span class="d-inline-flex align-items-center gap-1" style="background:rgba(239,68,68,.12); color:#f87171; border-radius:999px; padding:.15rem .6rem; font-size:.7rem; font-weight:600;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#f87171;"></span>Inativo
                        </span>
                    @endif
                </td>
                <td style="padding:.75rem 1rem; font-size:.78rem; color:rgba(148,163,184,.6);">{{ $webhook->created_at->format('d/m/Y') }}</td>
                <td style="padding:.75rem 1rem;">
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('webhooks.show', $webhook) }}" class="btn-action" style="font-size:.75rem; padding:.25rem .75rem; border-radius:.4rem; border:1px solid rgba(14,165,233,.25); background:rgba(14,165,233,.1); color:#38BDF8; text-decoration:none;">
                            <i class="bi bi-eye me-1"></i>Ver
                        </a>
                        <a href="{{ route('webhooks.edit', $webhook) }}" class="btn-action" style="font-size:.75rem; padding:.25rem .75rem; border-radius:.4rem; border:1px solid rgba(234,179,8,.25); background:rgba(234,179,8,.1); color:#facc15; text-decoration:none;">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" class="m-0"
                              onsubmit="return confirm('Excluir este webhook?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="font-size:.75rem; padding:.25rem .75rem; border-radius:.4rem; border:1px solid rgba(239,68,68,.25); background:rgba(239,68,68,.1); color:#f87171; cursor:pointer;">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
