@extends('layouts.app')

@section('title', 'Minha Assinatura')

@section('content')
@php
    $subscription = $company->subscription('default');
    $isActive     = $company->subscribed('default');
    $onGracePeriod = $subscription?->onGracePeriod() ?? false;

    $planLabels = [
        'free'       => ['label' => 'Free',     'color' => '#94a3b8'],
        'pro_launch' => ['label' => 'Pro',       'color' => '#38BDF8'],
        'pro'        => ['label' => 'Pro',       'color' => '#38BDF8'],
        'business'   => ['label' => 'Business', 'color' => '#c084fc'],
    ];
    $planInfo  = $planLabels[$company->plan] ?? ['label' => strtoupper($company->plan), 'color' => '#94a3b8'];
    $planColor = $planInfo['color'];
    $planLabel = $planInfo['label'];
@endphp

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Cabeçalho --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:42px;height:42px;border-radius:10px;background:rgba(14,165,233,.12);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-credit-card-2-front" style="font-size:1.25rem;color:#38BDF8;"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold" style="color:#f1f5f9;">Minha Assinatura</h4>
                <small style="color:rgba(148,163,184,.6);">Gerencie seu plano e cobrança</small>
            </div>
        </div>

        {{-- Card plano atual --}}
        <div class="card card-dark-bg mb-4" style="border-color:{{ $planColor }}33;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="text-soft mb-1" style="font-size:.78rem; text-transform:uppercase; letter-spacing:.08em;">Plano atual</div>
                        <h3 class="fw-bold mb-0" style="color:{{ $planColor }};">{{ $planLabel }}</h3>
                    </div>
                    <span class="badge px-3 py-2" style="background:{{ $planColor }}18; color:{{ $planColor }}; border:1px solid {{ $planColor }}33; font-size:.8rem;">
                        @if($company->plan === 'free')
                            <i class="bi bi-gift me-1"></i>Gratuito
                        @elseif($isActive && !$onGracePeriod)
                            <i class="bi bi-check-circle me-1"></i>Ativo
                        @elseif($onGracePeriod)
                            <i class="bi bi-clock me-1"></i>Cancelado — acesso até {{ $subscription->ends_at->format('d/m/Y') }}
                        @else
                            <i class="bi bi-x-circle me-1"></i>Inativo
                        @endif
                    </span>
                </div>

                @if($isActive && $subscription)
                <hr style="border-color:rgba(14,165,233,.1); margin:1.25rem 0;">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-soft" style="font-size:.75rem;">PRÓXIMA COBRANÇA</div>
                        <div class="fw-semibold" style="font-size:.95rem; color:#e2e8f0;">
                            @if($onGracePeriod)
                                —
                            @elseif($subscription->ends_at)
                                {{ $subscription->ends_at->format('d/m/Y') }}
                            @else
                                {{ now()->addMonth()->format('d/m/Y') }}
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-soft" style="font-size:.75rem;">ASSINANTE DESDE</div>
                        <div class="fw-semibold" style="font-size:.95rem; color:#e2e8f0;">
                            {{ $subscription->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Ações --}}
        <div class="card card-dark-bg mb-4">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3" style="color:#e2e8f0;">Ações</h6>
                <div class="d-flex flex-column gap-2">

                    {{-- Portal de cobrança --}}
                    @if($isActive)
                    <a href="{{ route('subscription.billing-portal') }}"
                       class="btn btn-outline-primary d-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-up-right"></i>
                        Gerenciar cobrança no Stripe
                        <small class="text-soft ms-auto" style="font-size:.72rem;">faturas, cartão, histórico</small>
                    </a>
                    @endif

                    {{-- Upgrade --}}
                    @if($company->plan === 'free' || $company->plan === 'pro_launch' || $company->plan === 'pro')
                    <a href="{{ route('upgrade') }}"
                       class="btn d-flex align-items-center gap-2"
                       style="background:rgba(14,165,233,.1); border:1px solid rgba(14,165,233,.25); color:#38BDF8;">
                        <i class="bi bi-rocket-takeoff"></i>
                        Fazer upgrade de plano
                    </a>
                    @endif

                    {{-- Cancelar --}}
                    @if($isActive && !$onGracePeriod)
                    <form method="POST" action="{{ route('subscription.cancel') }}"
                          onsubmit="return confirm('Tem certeza? Você perderá o acesso ao fim do período pago.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-outline-danger d-flex align-items-center gap-2 w-100">
                            <i class="bi bi-x-circle"></i>Cancelar assinatura
                            <small class="text-soft ms-auto" style="font-size:.72rem;">sem multa, acesso até o fim do período</small>
                        </button>
                    </form>
                    @endif

                </div>
            </div>
        </div>

        {{-- Plano free — CTA upgrade --}}
        @if($company->plan === 'free' && !$isActive)
        <div class="card" style="background:rgba(14,165,233,.07); border:1px solid rgba(14,165,233,.2); border-radius:12px;">
            <div class="card-body p-4 text-center">
                <i class="bi bi-rocket-takeoff d-block mb-2" style="font-size:1.8rem; color:#38BDF8;"></i>
                <h6 class="fw-bold mb-1" style="color:#f1f5f9;">Potencialize seu negócio</h6>
                <p class="text-soft mb-3" style="font-size:.85rem;">Desbloqueie todos os recursos com o plano Pro ou Business.</p>
                <a href="{{ route('upgrade') }}" class="btn btn-primary fw-semibold"
                   style="background:var(--brand-sky); border:none;">
                    <i class="bi bi-stars me-2"></i>Ver planos
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
