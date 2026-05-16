@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-gray-900 shadow-2xl rounded-xl overflow-hidden border border-gray-800">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-gray-800/50">
            <h2 class="text-2xl font-bold text-white">Novo Produto</h2>
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white transition-colors">Voltar</a>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nome do Produto</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Categoria</label>
                    <select name="category_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all @error('sku') border-red-500 @enderror">
                    @error('sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Preço -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Preço de Venda (R$)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all @error('price') border-red-500 @enderror">
                    @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Custo -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Custo (R$)</label>
                    <input type="number" step="0.01" name="cost" value="{{ old('cost') }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                </div>

                <!-- Quantidade -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Quantidade Inicial</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 0) }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all @error('quantity') border-red-500 @enderror">
                    @error('quantity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Estoque Mínimo -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Estoque Mínimo</label>
                    <input type="number" name="min_quantity" value="{{ old('min_quantity', 5) }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                </div>

                <!-- Unidade -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Unidade (UN, KG, etc)</label>
                    <input type="text" name="unit" value="{{ old('unit', 'UN') }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                </div>

                <!-- Código de Barras -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Código de Barras</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Descrição</label>
                    <textarea name="description" rows="3" 
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">{{ old('description') }}</textarea>
                </div>

                <!-- Status Ativo -->
                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="active" id="active" value="1" @checked(old('active', true))
                        class="w-5 h-5 bg-gray-800 border-gray-700 rounded text-blue-600 focus:ring-offset-gray-900">
                    <label for="active" class="text-sm font-medium text-gray-300 cursor-pointer">Produto Ativo</label>
                </div>
            </div>

            <div class="pt-6 flex justify-end space-x-4">
                <button type="reset" class="px-6 py-2.5 rounded-lg border border-gray-700 text-gray-400 hover:bg-gray-800 hover:text-white transition-all">
                    Limpar
                </button>
                <button type="submit" class="px-8 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-900 transition-all">
                    Cadastrar Produto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection