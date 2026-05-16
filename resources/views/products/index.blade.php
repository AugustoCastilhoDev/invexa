@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
    <div>
        <h1 class="h3 mb-1 text-white">Usuários</h1>
        <p class="text-soft mb-0">Gerencie quem tem acesso à sua empresa.</p>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Novo Usuário
        </a>
    @endif
</div>

{{-- Limite do plano --}}
@php
    $company = auth()->user()->company;
    $limits  = $company->limits();
    $total   = $users->count();
@endphp
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background: rgba(59,130,246,.1); border: 1px solid rgba(59,130,246,.2); color: #93c5fd; border-radius: .6rem;">
    <i class="bi bi-people fs-5"></i>
    <div>
        Plano <strong>{{ $company->plan_label }}</strong> —
        {{ $total }} de {{ $limits['users'] }} usuário(s) utilizados.
        @if($total >= $limits['users'])
            <span class="text-warning ms-2"><i class="bi bi-exclamation-triangle"></i> Limite atingido.</span>
        @endif
    </div>
</div>

<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle"
                   style="background: rgba(15,23,42,.88);">
                <thead>
                    <tr style="font-size:.7rem; font-weight:700; letter-spacing:.08em;
                                text-transform:uppercase; color:rgba(148,163,184,.85);
                                border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-4 py-3">Usuário</th>
                        <th class="py-3">E-mail</th>
                        <th class="py-3">Perfil</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Avatar --}}
                                    <div style="width:2.2rem; height:2.2rem; border-radius:50%;
                                                background: linear-gradient(135deg,#4f46e5,#7c3aed);
                                                display:flex; align-items:center; justify-content:center;
                                                font-size:.72rem; font-weight:700; color:#fff; flex-shrink:0;">
                                        {{ $user->initials }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-white">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <small class="text-soft">Você</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3" style="color:#94a3b8; font-size:.875rem;">
                                {{ $user->email }}
                            </td>
                            <td class="py-3">
                                <span class="badge bg-{{ $user->role_badge }} bg-opacity-25
                                             text-{{ $user->role_badge }}"
                                      style="font-size:.72rem; padding:.3rem .65rem; border-radius:999px;">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($user->active)
                                    <span style="display:inline-flex; align-items:center; gap:.3rem;
                                                 font-size:.72rem; font-weight:600; padding:.28rem .65rem;
                                                 border-radius:999px; background:rgba(34,197,94,.12);
                                                 color:#4ade80; border:1px solid rgba(34,197,94,.25);">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;"></span>
                                        Ativo
                                    </span>
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:.3rem;
                                                 font-size:.72rem; font-weight:600; padding:.28rem .65rem;
                                                 border-radius:999px; background:rgba(239,68,68,.1);
                                                 color:#f87171; border:1px solid rgba(239,68,68,.2);">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#f87171;"></span>
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-4">
                                @if(auth()->user()->isAdmin())
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="btn btn-sm btn-outline-light">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.toggle-active', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-sm {{ $user->active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        title="{{ $user->active ? 'Desativar' : 'Ativar' }}">
                                                    <i class="bi bi-{{ $user->active ? 'pause-circle' : 'play-circle' }}"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Remover {{ $user->name }}? Esta ação não pode ser desfeita.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-soft py-5">
                                <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection