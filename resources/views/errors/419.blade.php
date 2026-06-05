@extends('layouts.app')

@section('title', 'Sessão expirada')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="card dashboard-card card-dark-bg shadow-sm border-0 text-center">
            <div class="card-body py-5 px-4">

                {{-- Ícone --}}
                <div class="mb-4" style="
                    width: 5rem; height: 5rem; border-radius: 50%;
                    background: rgba(139,92,246,.12);
                    border: 1px solid rgba(139,92,246,.25);
                    display: inline-flex; align-items: center; justify-content: center;
                    margin: 0 auto;
                ">
                    <i class="bi bi-clock-history" style="font-size: 2rem; color: #a78bfa;"></i>
                </div>

                {{-- Código --}}
                <div class="fw-bold mb-1" style="font-size: 4rem; line-height: 1; background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    419
                </div>

                <h4 class="text-white fw-semibold mb-2">Sessão expirada</h4>
                <p class="text-soft mb-4">
                    Sua sessão expirou por inatividade.<br>
                    Volte à página anterior e tente novamente.
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="javascript:history.back()" class="btn btn-primary px-4">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Tentar novamente
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Fazer login
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection