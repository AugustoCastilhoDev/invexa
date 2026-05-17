@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
    <div>
        <h1 class="h3 mb-1 text-white">Editar Produto</h1>
        <p class="text-soft mb-0">Altere os dados do produto abaixo.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-light">Voltar</a>
    </div>
</div>

@php
    $company = auth()->user()->company;
    $limits  = $company ? $company->limits() : ['products' => 50];
@endphp
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#93c5fd;border-radius:.6rem;">
    <i class="bi bi-box-seam fs-5"></i>
    <div>
        Plano <strong>{{ $company?->plan_label ?? 'Gratuito' }}</strong> &mdash;
        {{ $totalProducts }} de {{ $limits['products'] }} produto(s) utilizados.
    </div>
</div>

<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <h5 class="mb-0 text-white">Dados do Produto</h5>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('products.update', $product) }}" method="POST" id="product-form">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-12 col-md-8">
                    <label for="name" class="form-label text-soft fw-semibold">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="category_id" class="form-label text-soft fw-semibold">Categoria</label>
                    <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Sem categoria</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                @selected(old('category_id', $product->category_id) == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-3">
                    <label for="sku" class="form-label text-soft fw-semibold">SKU <span class="text-danger">*</span></label>
                    <input type="text" id="sku" name="sku"
                           class="form-control @error('sku') is-invalid @enderror"
                           value="{{ old('sku', $product->sku) }}" required>
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-3">
                    <label for="barcode" class="form-label text-soft fw-semibold">Código de Barras</label>
                    <input type="text" id="barcode" name="barcode"
                           class="form-control @error('barcode') is-invalid @enderror"
                           value="{{ old('barcode', $product->barcode) }}">
                    @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-2">
                    <label for="unit" class="form-label text-soft fw-semibold">Unidade</label>
                    <input type="text" id="unit" name="unit"
                           class="form-control @error('unit') is-invalid @enderror"
                           value="{{ old('unit', $product->unit) }}" placeholder="UN">
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label text-soft fw-semibold d-block">Ativo</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="active" name="active"
                               @checked(old('active', $product->active))>
                        <label class="form-check-label text-soft" for="active">Sim</label>
                    </div>
                </div>

                <div class="col-12"><hr class="border-secondary"></div>

                <div class="col-6 col-md-3">
                    <label for="price" class="form-label text-soft fw-semibold">Preço de Venda <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                        <input type="number" step="0.01" min="0" id="price" name="price"
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $product->price) }}" required>
                    </div>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-3">
                    <label for="cost" class="form-label text-soft fw-semibold">Preço de Custo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-soft">R$</span>
                        <input type="number" step="0.01" min="0" id="cost" name="cost"
                               class="form-control @error('cost') is-invalid @enderror"
                               value="{{ old('cost', $product->cost) }}">
                    </div>
                    @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-3">
                    <label for="quantity" class="form-label text-soft fw-semibold">Quantidade em Estoque <span class="text-danger">*</span></label>
                    <input type="number" min="0" id="quantity" name="quantity"
                           class="form-control @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity', $product->quantity) }}" required>
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-6 col-md-3">
                    <label for="min_quantity" class="form-label text-soft fw-semibold">Estoque Mínimo <span class="text-danger">*</span></label>
                    <input type="number" min="0" id="min_quantity" name="min_quantity"
                           class="form-control @error('min_quantity') is-invalid @enderror"
                           value="{{ old('min_quantity', $product->min_quantity) }}" required>
                    @error('min_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label text-soft fw-semibold">Descrição</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
