@extends('layouts.app')

@section('title', 'Sem conexão')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5 text-center py-5">
        <div class="mb-4" style="width:5rem;height:5rem;border-radius:50%;background:rgba(14,165,233,.1);border:1px solid rgba(14,165,233,.2);display:inline-flex;align-items:center;justify-content:center;">
            <i class="bi bi-wifi-off" style="font-size:2rem;color:#38BDF8;"></i>
        </div>
        <h4 class="text-white fw-bold mb-2">Você está offline</h4>
        <p class="text-soft mb-4">Verifique sua conexão e tente novamente.<br>Suas últimas páginas visitadas podem estar disponíveis.</p>
        <button onclick="window.location.reload()" class="btn btn-primary px-4">
            <i class="bi bi-arrow-clockwise me-2"></i>Tentar novamente
        </button>
    </div>
</div>
@endsection
