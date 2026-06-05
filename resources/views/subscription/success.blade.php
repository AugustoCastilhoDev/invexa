@extends('layouts.app')

@section('title', 'Assinatura confirmada')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 text-center py-5">

        {{-- Ícone de sucesso --}}
        <div class="mb-4" style="
            width:80px; height:80px; border-radius:50%; margin:0 auto;
            background:rgba(34,197,94,.12); border:2px solid rgba(34,197,94,.3);
            display:flex; align-items:center; justify-content:center;
        ">
            <i class="bi bi-check-lg" style="font-size:2.2rem; color:#4ade80;"></i>
        </div>

        <h2 class="fw-bold mb-2" style="color:#f1f5f9;">Assinatura confirmada! 🎉</h2>
        <p class="mb-1" style="color:rgba(226,232,240,.65); font-size:.95rem;">
            Bem-vindo ao plano <strong style="color:#38BDF8;">{{ strtoupper($company->plan) }}</strong>.
        </p>
        <p style="color:rgba(226,232,240,.5); font-size:.85rem;">
            Seu acesso já está ativo. Aproveite todos os recursos do Invexa.
        </p>

        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('dashboard') }}" class="btn btn-primary fw-semibold px-4"
               style="background:var(--brand-sky); border:none;">
                <i class="bi bi-speedometer2 me-2"></i>Ir para o Dashboard
            </a>
            <a href="{{ route('subscription.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-receipt me-2"></i>Ver minha assinatura
            </a>
        </div>
    </div>
</div>
@endsection
