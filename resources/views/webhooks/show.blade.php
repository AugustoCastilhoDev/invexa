@extends('layouts.app')

@section('title', 'Detalhes do Webhook')

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detalhes do Webhook</h1>
        <a href="{{ route('webhooks.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">← Voltar</a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">

        <div class="px-6 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">URL</p>
            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white break-all">{{ $webhook->url }}</p>
        </div>

        @if($webhook->description)
        <div class="px-6 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Descrição</p>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $webhook->description }}</p>
        </div>
        @endif

        <div class="px-6 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Eventos</p>
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($webhook->events as $ev)
                    <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">{{ $ev }}</span>
                @endforeach
            </div>
        </div>

        <div class="px-6 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Secret (HMAC-SHA256)</p>
            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white break-all">{{ $webhook->secret }}</p>
            <p class="mt-1 text-xs text-gray-400">Use este secret para validar a assinatura <code class="font-mono">X-Invexa-Signature</code> no cabeçalho da requisição.</p>
            <form method="POST" action="{{ route('webhooks.regenerate-secret', $webhook) }}" class="mt-3" onsubmit="return confirm('Regenerar o secret invalida integrações existentes. Continuar?')">
                @csrf
                <button type="submit" class="text-xs text-amber-600 hover:text-amber-700 font-medium">↻ Regenerar secret</button>
            </form>
        </div>

        <div class="px-6 py-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status</p>
            <p class="mt-1">
                @if($webhook->active)
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Ativo
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inativo
                    </span>
                @endif
            </p>
        </div>

    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('webhooks.edit', $webhook) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            Editar
        </a>
        <form method="POST" action="{{ route('webhooks.test', $webhook) }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Disparar teste
            </button>
        </form>
        <form method="POST" action="{{ route('webhooks.destroy', $webhook) }}" onsubmit="return confirm('Remover este webhook?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium">Remover</button>
        </form>
    </div>

</div>
@endsection
