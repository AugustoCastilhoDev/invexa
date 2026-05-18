@extends('layouts.app')

@section('title', 'Busca: ' . $q)

@section('content')
<div class="container py-4">
    <h5 class="mb-4">
        <i class="fas fa-search me-2 text-muted"></i>
        Resultados para: <strong>{{ $q }}</strong>
    </h5>

    @if(strlen($q) < 2)
        <div class="alert alert-warning">Digite ao menos 2 caracteres para buscar.</div>
    @elseif($customers->isEmpty() && $products->isEmpty() && $suppliers->isEmpty() && $sales->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-search fa-2x mb-3"></i>
            <div>Nenhum resultado encontrado para "{{ $q }}".</div>
        </div>
    @else

    {{-- Clientes --}}
    @if($customers->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
            <h6 class="fw-semibold"><i class="fas fa-users me-2 text-primary"></i>Clientes ({{ $customers->count() }})</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($customers as $c)
            <a href="{{ route('customers.show', $c) }}" class="list-group-item list-group-item-action px-4 py-3">
                <div class="fw-semibold">{{ $c->name }}</div>
                <div class="small text-muted">{{ $c->email }} @if($c->phone) · {{ $c->phone }} @endif</div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Produtos --}}
    @if($products->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
            <h6 class="fw-semibold"><i class="fas fa-box me-2 text-success"></i>Produtos ({{ $products->count() }})</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($products as $p)
            <a href="{{ route('products.show', $p) }}" class="list-group-item list-group-item-action px-4 py-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $p->name }}</div>
                        <div class="small text-muted">{{ $p->sku ?? '—' }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">R$ {{ number_format($p->price, 2, ',', '.') }}</div>
                        <div class="small text-muted">Estoque: {{ $p->quantity }}</div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Fornecedores --}}
    @if($suppliers->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
            <h6 class="fw-semibold"><i class="fas fa-truck me-2 text-warning"></i>Fornecedores ({{ $suppliers->count() }})</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($suppliers as $s)
            <a href="{{ route('suppliers.show', $s) }}" class="list-group-item list-group-item-action px-4 py-3">
                <div class="fw-semibold">{{ $s->name }}</div>
                <div class="small text-muted">{{ $s->cnpj ?? '—' }} @if($s->email) · {{ $s->email }} @endif</div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Vendas --}}
    @if($sales->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
            <h6 class="fw-semibold"><i class="fas fa-receipt me-2 text-info"></i>Vendas ({{ $sales->count() }})</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($sales as $sale)
            <a href="{{ route('sales.show', $sale) }}" class="list-group-item list-group-item-action px-4 py-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }} — {{ $sale->customer_name }}</div>
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="fw-bold">R$ {{ number_format($sale->total, 2, ',', '.') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
@endsection
