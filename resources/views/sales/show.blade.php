@extends('layouts.app')

@section('title', 'Detalhes da Venda')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h4 class="mb-1">Detalhes da Venda</h4>
                <p class="text-muted mb-0">Venda #{{ $sale->id }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary">Editar</a>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Voltar</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="text-muted small">Cliente</div>
                    <div class="fw-semibold">{{ $sale->customer_name ?? 'Sem nome' }}</div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="text-muted small">Data</div>
                    <div class="fw-semibold">{{ $sale->sale_date ? $sale->sale_date->timezone(config('app.timezone'))->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="text-muted small">Status</div>
                    <div class="fw-semibold">{{ ucfirst($sale->status) }}</div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="text-muted small">Total</div>
                    <div class="fw-semibold">R$ {{ number_format($sale->total, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Produto removido' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($sale->notes)
            <div class="mt-4">
                <h6>Observações</h6>
                <p class="mb-0">{{ $sale->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection