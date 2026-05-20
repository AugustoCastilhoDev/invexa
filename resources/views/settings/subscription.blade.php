@extends('layouts.app')

@section('title', 'Minha Assinatura')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="fw-bold text-white mb-4">Minha Assinatura</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        {{-- Plano atual --}}
        <div class="card card-dark-bg mb-4">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Plano atual</h5>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4 fw-bold text-capitalize">{{ $company->plan }}</span>
                        @if($subscription && $subscription->active())
                            <span class="badge bg-success">Ativo</span>
                        @elseif($company->isOnTrial())
                            <span class="badge bg-warning text-dark">Trial — {{ $company->trialDaysLeft() }} dias restantes</span>
                        @else
                            <span class="badge bg-danger">Expirado</span>
                        @endif
                    </div>
                    @if($subscription && $subscription->active())
                        <div class="text-soft small">
                            @if($subscription->onGracePeriod())
                                Acesso até {{ $subscription->ends_at->format('d/m/Y') }}
                            @else
                                Renova em {{ date('d/m/Y', $subscription->asStripeSubscription()->current_period_end) }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ações --}}
        <div class="card card-dark-bg mb-4">
            <div class="card-body p-4 d-flex flex-wrap gap-2">
                <a href="{{ route('pricing') }}" class="btn btn-primary">
                    {{ $subscription && $subscription->active() ? 'Mudar plano' : 'Assinar agora' }}
                </a>
                @if($subscription && $subscription->active())
                    <a href="{{ route('subscription.billing-portal') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-credit-card me-1"></i>Gerenciar pagamento
                    </a>
                    @if(!$subscription->cancelled())
                    <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Cancelar assinatura?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i>Cancelar assinatura
                        </button>
                    </form>
                    @endif
                @endif
            </div>
        </div>

        {{-- Histórico de faturas --}}
        @if($invoices->count())
        <div class="card card-dark-bg">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Histórico de faturas</h5>
                <table class="table table-dark table-hover table-sm mb-0">
                    <thead>
                        <tr class="text-soft">
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->date()->format('d/m/Y') }}</td>
                            <td>{{ $invoice->total() }}</td>
                            <td>
                                <span class="badge {{ $invoice->paid ? 'bg-success' : 'bg-danger' }}">
                                    {{ $invoice->paid ? 'Pago' : 'Pendente' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('subscription.invoice', $invoice->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0">PDF</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
