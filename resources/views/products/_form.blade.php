<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="name" class="form-label">Nome do Produto</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $product->name ?? '') }}"
            placeholder="Ex: Smartphone, Geladeira, Fone Bluetooth"
            required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="category_id" class="form-label">Categoria</label>
        <select
            id="category_id"
            name="category_id"
            class="form-select @error('category_id') is-invalid @enderror"
            required>
            <option value="">Selecione</option>
            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="sku" class="form-label">SKU</label>
        <input
            type="text"
            id="sku"
            name="sku"
            class="form-control @error('sku') is-invalid @enderror"
            value="{{ old('sku', $product->sku ?? '') }}"
            placeholder="Código interno do produto">
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="price" class="form-label">Preço de Venda (R$)</label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="price"
            name="price"
            class="form-control @error('price') is-invalid @enderror"
            value="{{ old('price', $product->price ?? '') }}"
            placeholder="0,00"
            required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="cost" class="form-label">Custo (R$)</label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="cost"
            name="cost"
            class="form-control @error('cost') is-invalid @enderror"
            value="{{ old('cost', $product->cost ?? '') }}"
            placeholder="0,00">
        @error('cost')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="quantity" class="form-label">
            {{ isset($product) ? 'Quantidade Atual' : 'Quantidade Inicial' }}
        </label>
        <input
            type="number"
            min="0"
            id="quantity"
            name="quantity"
            class="form-control @error('quantity') is-invalid @enderror"
            value="{{ old('quantity', $product->quantity ?? 0) }}"
            required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="min_quantity" class="form-label">Estoque Mínimo</label>
        <input
            type="number"
            min="0"
            id="min_quantity"
            name="min_quantity"
            class="form-control @error('min_quantity') is-invalid @enderror"
            value="{{ old('min_quantity', $product->min_quantity ?? 0) }}">
        @error('min_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="unit" class="form-label">Unidade</label>
        <input
            type="text"
            id="unit"
            name="unit"
            class="form-control @error('unit') is-invalid @enderror"
            value="{{ old('unit', $product->unit ?? 'UN') }}"
            placeholder="Ex: UN, KG, CX">
        @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="barcode" class="form-label">Código de Barras</label>
        <input
            type="text"
            id="barcode"
            name="barcode"
            class="form-control @error('barcode') is-invalid @enderror"
            value="{{ old('barcode', $product->barcode ?? '') }}"
            placeholder="Opcional">
        @error('barcode')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Descrição</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
            placeholder="Descreva o produto, características ou observações...">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="active" value="0">
        <div class="form-check form-switch mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="active"
                name="active"
                value="1"
                @checked(old('active', $product->active ?? true))>
            <label class="form-check-label text-white" for="active">Produto Ativo</label>
        </div>
        <div class="text-soft small mt-2">
            Produtos inativos não aparecerão como opção principal em fluxos operacionais. [web:1417][web:1431]
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ isset($product) ? 'Salvar Alterações' : 'Salvar Produto' }}
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-light">Cancelar</a>
</div>