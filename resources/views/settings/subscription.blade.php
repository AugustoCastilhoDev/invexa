@extends('layouts.app')

@section('title', 'Minha Assinatura')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Minha Assinatura</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg">{{ session('warning') }}</div>
    @endif

    {{-- Plano atual --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Plano atual</h2>
        <div class="flex items-center justify-between">
            <div>
                <span class="text-2xl font-bold capitalize">{{ $company->plan }}</span>
                @if($subscription && $subscription->active())
                    <span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Ativo</span>
                @elseif($company->isOnTrial())
                    <span class="ml-2 px-2 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full">Trial — {{ $company->trialDaysLeft() }} dias restantes</span>
                @else
                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Expirado</span>
                @endif
            </div>
            @if($subscription && $subscription->active() && !$subscription->onGracePeriod())
                <div class="text-sm text-slate-500">
                    Renova em {{ date('d/m/Y', $subscription->asStripeSubscription()->current_period_end) }}
                </div>
            @elseif($subscription && $subscription->onGracePeriod())
                <div class="text-sm text-red-500">
                    Acesso até {{ $subscription->ends_at->format('d/m/Y') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Ações --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow p-6 mb-6 flex flex-wrap gap-3">
        <a href="{{ route('pricing') }}" class="py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
            {{ $subscription && $subscription->active() ? 'Mudar plano' : 'Assinar agora' }}
        </a>
        @if($subscription && $subscription->active())
            <a href="{{ route('subscription.billing-portal') }}" class="py-2 px-4 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg hover:bg-slate-300 transition text-sm font-medium">
                Gerenciar pagamento
            </a>
            @if(!$subscription->cancelled())
            <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Cancelar assinatura?')">
                @csrf @method('DELETE')
                <button type="submit" class="py-2 px-4 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                    Cancelar assinatura
                </button>
            </form>
            @endif
        @endif
    </div>

    {{-- Histórico de faturas --}}
    @if($invoices->count())
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Histórico de faturas</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 border-b dark:border-slate-700">
                    <th class="pb-2">Data</th>
                    <th class="pb-2">Valor</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr class="border-b dark:border-slate-700">
                    <td class="py-2">{{ $invoice->date()->format('d/m/Y') }}</td>
                    <td class="py-2">{{ $invoice->total() }}</td>
                    <td class="py-2">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $invoice->paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $invoice->paid ? 'Pago' : 'Pendente' }}
                        </span>
                    </td>
                    <td class="py-2 text-right">
                        <a href="{{ route('subscription.invoice', $invoice->id) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">PDF</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
