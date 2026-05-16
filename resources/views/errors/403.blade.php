@extends('layouts.app')

@section('title', 'Acesso Negado')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="card dashboard-card card-dark-bg shadow-sm border-0 text-center">
            <div class="card-body py-5 px-4">

                {{-- Ícone --}}
                <div class="mb-4" style="
                    width: 5rem; height: 5rem; border-radius: 50%;
                    background: rgba(239,68,68,.12);
                    border: 1px solid rgba(239,68,68,.25);
                    display: inline-flex; align-items: center; justify-content: center;
                ">
                    <i class="bi bi-shield-lock" style="font-size: 2rem; color: #f87171;"></i>
                </div>

                {{-- Código --}}
                <div class="fw-bold mb-1" style="font-size: 4rem; line-height: 1; background: linear-gradient(135deg, #f87171, #ef4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    403
                </div>

                <h4 class="text-white fw-semibold mb-2">Acesso Negado</h4>
                <p class="text-soft mb-4">
                    Você não tem permissão para acessar esta página.<br>
                    Caso acredite que isso é um erro, entre em contato com o administrador.
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">
                        <i class="bi bi-speedometer2 me-1"></i>Ir ao Dashboard
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-1"></i>Voltar
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection