@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-white">Novo Produto</h1>
        <p class="text-soft mb-0">Preencha os dados do produto.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-light">Voltar</a>
</div>

<div class="card card-dark-bg shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            <div class="row g-4">

                {{-- Seção: Identificação --}}
                <div class="col-12">
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem;letter-spacing:.08em;">
                        <i class="bi bi-tag me-1"></i>Identificação
                    </h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label text-soft">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Nome do produto">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="sku" class="form-label text-soft">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="sku"
                                   class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku') }}" placeholder="Ex: PROD-001">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="barcode" class="form-label text-soft">Cód. de Barras</label>
                            <input type="text" name="barcode" id="barcode"
                                   class="form-control @error('barcode') is-invalid @enderror"
                                   value="{{ old('barcode') }}" placeholder="EAN-13">
                            @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="category_id" class="form-label text-soft">Categoria</label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Sem categoria</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="supplier_id" class="form-label text-soft">Fornecedor</label>
                            <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">Sem fornecedor</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="unit" class="form-label text-soft">Unidade</label>
                            <select name="unit" id="unit" class="form-select @error('unit') is-invalid @enderror">
                                @foreach(['Un','Kg','g','L','mL','Cx','Pc','Par','m','m²','m³'] as $u)
                                    <option value="{{ $u }}" @selected(old('unit', 'Un') === $u)>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label text-soft">Descrição</label>
                            <textarea name="description" id="description" rows="2"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Descrição opcional do produto">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Seção: Preços --}}
                <div class="col-12">
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem;letter-spacing:.08em;">
                        <i class="bi bi-currency-dollar me-1"></i>Preços
                    </h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="price" class="form-label text-soft">Preço de Venda <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-soft border-secondary">R$</span>
                                <input type="number" name="price" id="price" step="0.01" min="0"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}" placeholder="0,00">
                            </div>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="cost" class="form-label text-soft">Custo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-soft border-secondary">R$</span>
                                <input type="number" name="cost" id="cost" step="0.01" min="0"
                                       class="form-control @error('cost') is-invalid @enderror"
                                       value="{{ old('cost') }}" placeholder="0,00">
                            </div>
                            @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Seção: Estoque --}}
                <div class="col-12">
                    <h6 class="text-soft text-uppercase fw-semibold mb-3" style="font-size:.72rem;letter-spacing:.08em;">
                        <i class="bi bi-archive me-1"></i>Estoque
                    </h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label for="quantity" class="form-label text-soft">Quantidade Inicial <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" min="0"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 0) }}">
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="min_quantity" class="form-label text-soft">Estoque Mínimo <span class="text-danger">*</span></label>
                            <input type="number" name="min_quantity" id="min_quantity" min="0"
                                   class="form-control @error('min_quantity') is-invalid @enderror"
                                   value="{{ old('min_quantity', 5) }}">
                            @error('min_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
                                       {{ old('active', true) ? 'checked' : '' }}>
                                <label class="form-check-label text-soft" for="active">Produto ativo</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Cadastrar Produto
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
