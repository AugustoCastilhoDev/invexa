@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-bell me-2 text-warning"></i>Notificações</h4>
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar todas como lidas</button>
        </form>
    </div>

    @forelse($notifications as $notification)
    <div class="card border-0 shadow-sm mb-2 {{ is_null($notification->read_at) ? 'border-start border-primary border-3' : '' }}">
        <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                @php
                    $icons = [
                        'low_stock'       => 'fas fa-boxes text-warning',
                        'bill_due'        => 'fas fa-exclamation-circle text-danger',
                        'bill_overdue'    => 'fas fa-times-circle text-danger',
                        'receivable_due'  => 'fas fa-clock text-info',
                    ];
                    $icon = $icons[$notification->data['type'] ?? ''] ?? 'fas fa-bell text-secondary';
                @endphp
                <i class="{{ $icon }} me-2"></i>
                <strong>{{ $notification->data['title'] ?? 'Notificação' }}</strong>
                <div class="text-muted small mt-1">{{ $notification->data['message'] ?? '' }}</div>
                <div class="text-muted" style="font-size:10px;">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if(!empty($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary">Ver</a>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center text-muted py-5">
        <i class="fas fa-bell-slash fa-2x mb-3"></i>
        <div>Nenhuma notificação no momento.</div>
    </div>
    @endforelse

    <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
