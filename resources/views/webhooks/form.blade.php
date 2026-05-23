@extends('layouts.app')

@section('title', isset($webhook) ? 'Editar Webhook' : 'Novo Webhook')

@section('content')
<div class="max-w-2xl space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ isset($webhook) ? 'Editar Webhook' : 'Novo Webhook' }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure a URL e os eventos que deseja receber.</p>
    </div>

    <form method="POST"
          action="{{ isset($webhook) ? route('webhooks.update', $webhook) : route('webhooks.store') }}"
          class="space-y-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        @csrf
        @if(isset($webhook)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL do Endpoint <span class="text-red-500">*</span></label>
            <input type="url" name="url"
                   value="{{ old('url', $webhook->url ?? '') }}"
                   placeholder="https://seusite.com.br/webhook"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('url') border-red-500 @enderror" />
            @error('url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição (opcional)</label>
            <input type="text" name="description"
                   value="{{ old('description', $webhook->description ?? '') }}"
                   placeholder="Ex: Sistema ERP principal"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Eventos <span class="text-red-500">*</span></label>
            <div class="space-y-2">
                @foreach($events as $key => $label)
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="events[]" value="{{ $key }}"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ in_array($key, old('events', $webhook->events ?? [])) ? 'checked' : '' }} />
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-indigo-600 dark:text-indigo-400">{{ $key }}</span>
                        <span class="ml-2 text-gray-500">{{ $label }}</span>
                    </span>
                </label>
                @endforeach
            </div>
            @error('events') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        @if(isset($webhook))
        <div class="flex items-center gap-3">
            <input type="hidden" name="active" value="0" />
            <input type="checkbox" id="active" name="active" value="1"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                   {{ $webhook->active ? 'checked' : '' }} />
            <label for="active" class="text-sm text-gray-700 dark:text-gray-300">Webhook ativo</label>
        </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                {{ isset($webhook) ? 'Salvar alterações' : 'Criar Webhook' }}
            </button>
            <a href="{{ route('webhooks.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">Cancelar</a>
        </div>
    </form>
</div>
@endsection
