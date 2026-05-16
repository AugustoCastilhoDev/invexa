@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Detalhes do Produto</h2>
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">Voltar</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">Nome</p>
            <p class="font-semibold">{{ $product->name }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Categoria</p>
            <p class="font-semibold">{{ $product->category->name ?? 'Sem categoria' }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">SKU</p>
            <p class="font-semibold">{{ $product->sku }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Código de barras</p>
            <p class="font-semibold">{{ $product->barcode ?? '—' }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Preço de venda</p>
            <p class="font-semibold">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Custo</p>
            <p class="font-semibold">R$ {{ number_format($product->cost, 2, ',', '.') }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Unidade</p>
            <p class="font-semibold">{{ $product->unit }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Quantidade em estoque</p>
            <p class="font-semibold">{{ $product->quantity }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Estoque mínimo</p>
            <p class="font-semibold">{{ $product->min_quantity }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-semibold">
                @if($product->active)
                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Ativo</span>
                @else
                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Inativo</span>
                @endif
            </p>
        </div>

        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Descrição</p>
            <p class="font-semibold">{{ $product->description ?? 'Sem descrição' }}</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded">
            Editar
        </a>

        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">
                Excluir
            </button>
        </form>
    </div>
</div>
@endsection