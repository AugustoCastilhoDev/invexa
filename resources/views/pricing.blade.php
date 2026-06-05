@extends('layouts.app')

@section('title', 'Planos e Preços')

@section('content')
<div class="py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold fs-2 text-white">Escolha seu plano</h1>
        <p class="text-soft fs-5">Comece grátis. Escale quando precisar.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center g-4">

        {{-- FREE --}}
        <div class="col-md-4">
            <div class="card card-dark-bg h-100">
                <div class="card-body d-flex flex-column p-4">
                    <span class="text-uppercase text-soft fw-semibold" style="font-size:.75rem; letter-spacing:.1em;">Free</span>
                    <div class="my-3">
                        <span class="fs-1 fw-bold text-white">R$ 0</span>
                        <span class="text-soft">/mês</span>
                    </div>
                    <p class="text-soft small mb-4">Para quem está começando.</p>
                    <ul class="list-unstyled flex-grow-1 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Até 50 produtos</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Até 100 clientes</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>2 usuários</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Relatórios básicos</li>
                        <li class="mb-2 text-soft"><i class="bi bi-x-circle me-2"></i>PDV avançado</li>
                        <li class="mb-2 text-soft"><i class="bi bi-x-circle me-2"></i>Suporte prioritário</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100">Começar grátis</a>
                </div>
            </div>
        </div>

        {{-- PRO (destaque) --}}
        <div class="col-md-4">
            <div class="card h-100 border-0" style="background: linear-gradient(135deg, #0c1e3a, #0d2a4a); border: 1px solid rgba(14,165,233,.4) !important;">
                <div class="card-body d-flex flex-column p-4 position-relative">
                    <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill" style="background:#f59e0b; color:#1a1a1a; font-size:.7rem; padding:.35rem .75rem;">Mais popular</span>
                    <span class="text-uppercase fw-semibold mt-3" style="font-size:.75rem; letter-spacing:.1em; color:#38BDF8;">Pro</span>
                    <div class="my-3">
                        <span class="fs-1 fw-bold text-white">R$ 39,90</span>
                        <span class="text-decoration-line-through ms-2" style="color:rgba(226,232,240,.45);">R$ 59,90</span>
                        <span style="color:rgba(226,232,240,.6);">/mês</span>
                    </div>
                    <p class="small mb-1" style="color:#7DD3FC;">🔥 Oferta de lançamento</p>
                    <p class="small mb-4" style="color:rgba(226,232,240,.7);">Para negócios em crescimento.</p>
                    <ul class="list-unstyled flex-grow-1 mb-4" style="color:rgba(226,232,240,.9);">
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Até 500 produtos</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Até 1.000 clientes</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>10 usuários</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Todos os relatórios + PDF/CSV</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>PDV completo</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:#38BDF8;"></i>Suporte por e-mail</li>
                    </ul>
                    @auth
                    <form action="{{ route('subscription.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="pro_launch">
                        <button type="submit" class="btn w-100 fw-semibold" style="background:#0EA5E9; color:#fff;">Assinar Pro — R$ 39,90/mês</button>
                    </form>
                    @else
                    <a href="{{ route('register') }}" class="btn w-100 fw-semibold" style="background:#0EA5E9; color:#fff;">Criar conta grátis</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- BUSINESS --}}
        <div class="col-md-4">
            <div class="card card-dark-bg h-100">
                <div class="card-body d-flex flex-column p-4">
                    <span class="text-uppercase text-soft fw-semibold" style="font-size:.75rem; letter-spacing:.1em;">Business</span>
                    <div class="my-3">
                        <span class="fs-1 fw-bold text-white">R$ 119,90</span>
                        <span class="text-soft">/mês</span>
                    </div>
                    <p class="text-soft small mb-4">Para empresas sem limites.</p>
                    <ul class="list-unstyled flex-grow-1 mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Produtos ilimitados</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Clientes ilimitados</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Usuários ilimitados</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Todos os recursos Pro</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>API REST (em breve)</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Suporte prioritário</li>
                    </ul>
                    @auth
                    <form action="{{ route('subscription.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="business">
                        <button type="submit" class="btn btn-outline-light w-100 fw-semibold">Assinar Business — R$ 119,90/mês</button>
                    </form>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-outline-light w-100 fw-semibold">Criar conta grátis</a>
                    @endauth
                </div>
            </div>
        </div>

    </div>{{-- /row --}}
</div>
@endsection
