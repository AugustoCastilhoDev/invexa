@extends('layouts.app')

@section('title', 'Planos e Preços — Invexa')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 py-16 px-4">
    <div class="max-w-5xl mx-auto">

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-3">Escolha seu plano</h1>
            <p class="text-slate-400 text-lg">Comece grátis. Escale quando precisar.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500 text-green-300 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- FREE --}}
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">Free</span>
                    <div class="mt-2">
                        <span class="text-4xl font-bold text-white">R$ 0</span>
                        <span class="text-slate-400">/mês</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-2">Para quem está começando.</p>
                </div>
                <ul class="space-y-2 text-sm text-slate-300 flex-1">
                    <li>✅ Até 50 produtos</li>
                    <li>✅ Até 100 clientes</li>
                    <li>✅ 2 usuários</li>
                    <li>✅ Relatórios básicos</li>
                    <li>❌ PDV avançado</li>
                    <li>❌ Suporte prioritário</li>
                </ul>
                <a href="{{ route('register') }}" class="mt-6 block text-center py-2 px-4 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Começar grátis
                </a>
            </div>

            {{-- PRO (destaque) --}}
            <div class="bg-indigo-600 border border-indigo-500 rounded-2xl p-6 flex flex-col relative shadow-2xl">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="bg-amber-400 text-slate-900 text-xs font-bold px-3 py-1 rounded-full uppercase">Mais popular</span>
                </div>
                <div class="mb-6">
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Pro</span>
                    <div class="mt-2 flex items-end gap-2">
                        <span class="text-4xl font-bold text-white">R$ 39,90</span>
                        <span class="text-indigo-200 line-through text-lg">R$ 59,90</span>
                    </div>
                    <p class="text-indigo-200 text-sm mt-1">🔥 Oferta de lançamento</p>
                    <p class="text-indigo-200 text-sm mt-2">Para negócios em crescimento.</p>
                </div>
                <ul class="space-y-2 text-sm text-indigo-100 flex-1">
                    <li>✅ Até 500 produtos</li>
                    <li>✅ Até 1.000 clientes</li>
                    <li>✅ 10 usuários</li>
                    <li>✅ Todos os relatórios + PDF/CSV</li>
                    <li>✅ PDV completo</li>
                    <li>✅ Suporte por e-mail</li>
                </ul>
                <form action="{{ route('subscription.checkout') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="plan" value="pro_launch">
                    <button type="submit" class="w-full py-2 px-4 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-indigo-50 transition">
                        Assinar Pro — R$ 39,90/mês
                    </button>
                </form>
            </div>

            {{-- BUSINESS --}}
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">Business</span>
                    <div class="mt-2">
                        <span class="text-4xl font-bold text-white">R$ 119,90</span>
                        <span class="text-slate-400">/mês</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-2">Para empresas sem limites.</p>
                </div>
                <ul class="space-y-2 text-sm text-slate-300 flex-1">
                    <li>✅ Produtos ilimitados</li>
                    <li>✅ Clientes ilimitados</li>
                    <li>✅ Usuários ilimitados</li>
                    <li>✅ Todos os recursos Pro</li>
                    <li>✅ API REST (em breve)</li>
                    <li>✅ Suporte prioritário</li>
                </ul>
                <form action="{{ route('subscription.checkout') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="plan" value="business">
                    <button type="submit" class="w-full py-2 px-4 bg-slate-600 hover:bg-slate-500 text-white font-semibold rounded-lg transition">
                        Assinar Business — R$ 119,90/mês
                    </button>
                </form>
            </div>

        </div>{{-- /grid --}}
    </div>
</div>
@endsection
