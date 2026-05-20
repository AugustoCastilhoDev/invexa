@extends('layouts.app')
@section('title', 'Notificações')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
    <div>
        <h1 class="h3 mb-1 text-white"><i class="bi bi-bell me-2 text-info"></i>Notificações</h1>
        <p class="text-soft mb-0">Histórico de alertas e avisos do sistema.</p>
    </div>
    @if(auth()->user()->unreadNotifications->count() > 0)
    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-info btn-sm">
            <i class="bi bi-check2-all me-1"></i>Marcar todas como lidas
        </button>
    </form>
    @endif
</div>

<div class="card card-dark-bg">
    <div class="card-body p-0">
        @forelse($notifications as $n)
        @php
            $type = $n->data['type']    ?? 'info';
            $icon = $n->data['icon']    ?? 'bi-bell';
            $title   = $n->data['title']   ?? 'Notificação';
            $message = $n->data['message'] ?? '';
            $url     = $n->data['url']     ?? null;
            $colorMap = ['danger'=>'#f87171','warning'=>'#facc15','info'=>'#38BDF8','success'=>'#4ade80'];
            $bgMap    = ['danger'=>'rgba(239,68,68,.15)','warning'=>'rgba(234,179,8,.12)','info'=>'rgba(14,165,233,.12)','success'=>'rgba(34,197,94,.12)'];
            $color = $colorMap[$type] ?? '#38BDF8';
            $bg    = $bgMap[$type]    ?? 'rgba(14,165,233,.12)';
            $unread = is_null($n->read_at);
        @endphp
        <div class="d-flex align-items-start gap-3 px-4 py-3"
             style="border-bottom:1px solid rgba(148,163,184,.08); {{ $unread ? 'background:rgba(14,165,233,.04);' : '' }}">
            <div style="width:2.2rem;height:2.2rem;border-radius:50%;background:{{ $bg }};color:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                <i class="bi {{ $icon }}"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold" style="color:#e2e8f0;font-size:.875rem;">{{ $title }}</span>
                    @if($unread)
                        <span class="badge" style="background:rgba(14,165,233,.2);color:#38BDF8;font-size:.6rem;padding:.2rem .5rem;">Nova</span>
                    @endif
                </div>
                <p class="mb-1" style="color:rgba(148,163,184,.85);font-size:.8rem;">{{ $message }}</p>
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size:.72rem;color:rgba(148,163,184,.5);">{{ $n->created_at->diffForHumans() }}</span>
                    @if($url)
                        <a href="{{ $url }}" style="font-size:.72rem;color:#38BDF8;">Ver detalhes <i class="bi bi-arrow-right"></i></a>
                    @endif
                    <form action="{{ route('notifications.destroy', $n->id) }}" method="POST" class="m-0">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:rgba(248,113,113,.6);font-size:.72rem;padding:0;cursor:pointer;">
                            <i class="bi bi-trash"></i> Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-soft">
            <i class="bi bi-bell-slash fs-2 d-block mb-2 opacity-40"></i>
            Nenhuma notificação encontrada.
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer" style="background:transparent;border-top:1px solid rgba(14,165,233,.1);">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
