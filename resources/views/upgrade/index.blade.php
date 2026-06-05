@extends('layouts.app')
@section('title', 'Upgrade de Plano')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">

        <div class="mb-4">
            <h2 class="fw-bold" style="color:#F0F9FF;">Escolha seu Plano</h2>
            <p class="text-soft">Escale sua operação com o plano ideal para o seu negócio.</p>
        </div>

        @php $currentPlan = auth()->user()->company?->plan ?? 'free'; @endphp

        <div class="row g-4">

            {{-- FREE --}}
            <div class="col-12 col-md-4">
                <div class="card-dark-bg rounded-4 p-4 h-100 position-relative {{ $currentPlan === 'free' ? 'border border-secondary' : '' }}">
                    @if($currentPlan === 'free')<span class="badge bg-secondary position-absolute top-0 end-0 m-3" style="font-size:.65rem;">Plano atual</span>@endif
                    <div class="mb-3">
                        <h4 class="fw-bold" style="color:#94a3b8;">Free</h4>
                        <div style="font-size:1.8rem; font-weight:800; color:#F0F9FF;">R$ 0<span style="font-size:.9rem; font-weight:400; color:rgba(148,163,184,.6);">/mês</span></div>
                    </div>
                    <ul class="list-unstyled mb-4" style="font-size:.875rem; color:rgba(226,232,240,.75);">
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>50 produtos</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>100 clientes</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>10 fornecedores</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>2 usuários</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>20 ordens de compra</strong></li>
                        <li class="mb-2"><i class="bi bi-x me-2" style="color:#f87171;"></i>Sem relatórios avançados</li>
                    </ul>
                    <button class="btn w-100 {{ $currentPlan === 'free' ? 'btn-secondary disabled' : 'btn-outline-secondary' }}" disabled>
                        {{ $currentPlan === 'free' ? 'Plano atual' : 'Downgrade' }}
                    </button>
                </div>
            </div>

            {{-- PRO --}}
            <div class="col-12 col-md-4">
                <div class="rounded-4 p-4 h-100 position-relative" style="background:rgba(14,165,233,.07); border:2px solid rgba(14,165,233,.35);">
                    <span class="badge position-absolute top-0 end-0 m-3" style="background:rgba(14,165,233,.2); color:#38BDF8; font-size:.65rem;">Recomendado</span>
                    @if($currentPlan === 'pro')<span class="badge bg-primary position-absolute top-0 start-0 m-3" style="font-size:.65rem;">Plano atual</span>@endif
                    <div class="mb-3">
                        <h4 class="fw-bold" style="color:#38BDF8;">Pro</h4>
                        <div style="font-size:1.8rem; font-weight:800; color:#F0F9FF;">R$ 79<span style="font-size:.9rem; font-weight:400; color:rgba(148,163,184,.6);">/mês</span></div>
                    </div>
                    <ul class="list-unstyled mb-4" style="font-size:.875rem; color:rgba(226,232,240,.75);">
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>500 produtos</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>1.000 clientes</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>100 fornecedores</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>10 usuários</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Até <strong>200 ordens de compra</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Relatórios completos</li>
                    </ul>
                    <button class="btn w-100 {{ $currentPlan === 'pro' ? 'disabled' : '' }}" style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.4); color:#38BDF8; {{ $currentPlan === 'pro' ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                        {{ $currentPlan === 'pro' ? 'Plano atual' : 'Assinar Pro' }}
                    </button>
                </div>
            </div>

            {{-- BUSINESS --}}
            <div class="col-12 col-md-4">
                <div class="rounded-4 p-4 h-100 position-relative" style="background:rgba(168,85,247,.06); border:2px solid rgba(168,85,247,.25);">
                    @if($currentPlan === 'business')<span class="badge position-absolute top-0 start-0 m-3" style="background:rgba(168,85,247,.2); color:#c084fc; font-size:.65rem;">Plano atual</span>@endif
                    <div class="mb-3">
                        <h4 class="fw-bold" style="color:#c084fc;">Business</h4>
                        <div style="font-size:1.8rem; font-weight:800; color:#F0F9FF;">R$ 149<span style="font-size:.9rem; font-weight:400; color:rgba(148,163,184,.6);">/mês</span></div>
                    </div>
                    <ul class="list-unstyled mb-4" style="font-size:.875rem; color:rgba(226,232,240,.75);">
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i><strong>Produtos ilimitados</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i><strong>Clientes ilimitados</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i><strong>Fornecedores ilimitados</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i><strong>Usuários ilimitados</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i><strong>Ordens ilimitadas</strong></li>
                        <li class="mb-2"><i class="bi bi-check2 me-2" style="color:#4ade80;"></i>Suporte prioritário</li>
                    </ul>
                    <button class="btn w-100 {{ $currentPlan === 'business' ? 'disabled' : '' }}" style="background:rgba(168,85,247,.15); border:1px solid rgba(168,85,247,.4); color:#c084fc; {{ $currentPlan === 'business' ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                        {{ $currentPlan === 'business' ? 'Plano atual' : 'Assinar Business' }}
                    </button>
                </div>
            </div>

        </div>

        {{-- USO ATUAL --}}
        @php $company = auth()->user()->company; @endphp
        @if($company)
        <div class="card-dark-bg rounded-4 p-4 mt-4">
            <h6 class="fw-bold mb-3" style="color:#F0F9FF;"><i class="bi bi-bar-chart-line me-2" style="color:#0EA5E9;"></i>Uso atual do plano {{ strtoupper($company->plan) }}</h6>
            <div class="row g-3">
                @foreach([
                    ['products',        'Produtos',         'bi-box-seam'],
                    ['customers',       'Clientes',         'bi-people'],
                    ['suppliers',       'Fornecedores',     'bi-building'],
                    ['users',           'Usuários',         'bi-shield-person'],
                    ['purchase_orders', 'Ordens de Compra', 'bi-cart-check'],
                ] as [$key, $label, $icon])
                    @php
                        $limit = $company->limit($key);
                        $pct   = $company->usagePercent($key);
                        $isInf = $limit === PHP_INT_MAX;
                        $color = $pct >= 90 ? '#f87171' : ($pct >= 70 ? '#fbbf24' : '#4ade80');
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div style="font-size:.78rem; color:rgba(148,163,184,.6); margin-bottom:.3rem;">
                            <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                        </div>
                        @if($isInf)
                            <div style="font-size:.9rem; color:#4ade80; font-weight:600;"><i class="bi bi-infinity me-1"></i>Ilimitado</div>
                        @else
                            <div style="height:6px; background:rgba(255,255,255,.07); border-radius:999px; overflow:hidden; margin-bottom:.25rem;">
                                <div style="height:100%; width:{{ $pct }}%; background:{{ $color }}; border-radius:999px; transition:width .4s;"></div>
                            </div>
                            <div style="font-size:.75rem; color:rgba(148,163,184,.55);">{{ $pct }}% usado &mdash; limite: {{ number_format($limit) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
