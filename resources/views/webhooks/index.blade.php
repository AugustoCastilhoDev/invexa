@extends('layouts.app')

@section('title', 'Webhooks')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Webhooks</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Integre eventos do Invexa com seus sistemas externos.</p>
        </div>
        <a href="{{ route('webhooks.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Novo Webhook
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if($endpoints->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
            <x-heroicon-o-arrow-path class="mx-auto w-10 h-10 text-gray-400" />
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Nenhum webhook configurado ainda.</p>
            <a href="{{ route('webhooks.create') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Criar o primeiro webhook</a>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">URL</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Eventos</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($endpoints as $endpoint)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white truncate max-w-xs">{{ $endpoint->url }}</p>
                            @if($endpoint->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $endpoint->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($endpoint->events as $ev)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                                        {{ $ev }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($endpoint->active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inativo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('webhooks.show', $endpoint) }}" class="text-gray-400 hover:text-indigo-600 transition" title="Ver detalhes">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>
                                <a href="{{ route('webhooks.edit', $endpoint) }}" class="text-gray-400 hover:text-indigo-600 transition" title="Editar">
                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('webhooks.destroy', $endpoint) }}" onsubmit="return confirm('Remover este webhook?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Remover">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
