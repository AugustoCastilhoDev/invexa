@extends('layouts.app')

@section('title', 'Redirecionando para o pagamento...')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:60vh;">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <h5 class="text-light mb-1">Redirecionando para o pagamento...</h5>
        <p class="text-muted small">Aguarde, você será direcionado ao checkout seguro do Stripe.</p>
    </div>

    {{-- Formulário oculto que faz auto-POST para o checkout --}}
    <form id="checkout-form" method="POST" action="{{ route('subscription.checkout') }}" class="d-none">
        @csrf
        <input type="hidden" name="plan"    value="{{ $plan }}">
        <input type="hidden" name="billing" value="{{ $billing }}">
    </form>
</div>

@push('scripts')
<script>
    // Auto-submete após 300ms para garantir que o DOM carregou
    setTimeout(() => document.getElementById('checkout-form').submit(), 300);
</script>
@endpush
@endsection
