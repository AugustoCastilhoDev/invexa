@extends('layouts.app')

@section('title', 'Página não encontrada')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="card dashboard-card card-dark-bg shadow-sm border-0 text-center">
            <div class="card-body py-5 px-4">

                {{-- Ícone --}}
                <div class="mb-4" style="
                    width: 5rem; height: 5rem; border-radius: 50%;
                    background: rgba(59,130,246,.12);
                    border: 1px solid rgba(59,130,246,.25);
                    display: inline-flex; align-items: center; justify-content: center;
                    margin: 0 auto;
                ">
                    <i class="bi bi-search" style="font-size: 2rem; color: #60a5fa;"></i>
                </div>

                {{-- Código --}}
                <div class="fw-bold mb-1" style="font-size: 4rem; line-height: 1; background: linear-gradient(135deg, #60a5fa, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    404
                </div>

                <h4 class="text-white fw-semibold mb-2">Página não encontrada</h4>
                <p class="text-soft mb-4">
                    O endereço que você acessou não existe ou foi removido.<br>
                    Verifique o link e tente novamente.
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