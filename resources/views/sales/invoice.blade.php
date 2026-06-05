@extends('layouts.app')

@section('title', 'Nota de Venda #' . $sale->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Nota de Venda <span class="text-muted">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span></h4>
            <small class="text-muted">Emitida em {{ now()->format('d/m/Y H:i') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.pdf', $sale) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Baixar PDF
            </a>
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            {{-- Cabeçalho --}}
            <div class="row mb-4 pb-3 border-bottom">
                <div class="col-md-6">
                    @php $company = auth()->user()->company; @endphp
                    @if($company)
                        <h5 class="fw-bold">{{ $company->name }}</h5>
                        @if($company->cnpj) <div class="text-muted small">CNPJ: {{ $company->cnpj }}</div> @endif
                        @if($company->address) <div class="text-muted small">{{ $company->address }}</div> @endif
                        @if($company->phone) <div class="text-muted small">Tel: {{ $company->phone }}</div> @endif
                        @if($company->email) <div class="text-muted small">{{ $company->email }}</div> @endif
                    @else
                        <h5 class="fw-bold">Invexa</h5>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="h5 fw-bold text-primary">NOTA DE VENDA</div>
                    <div class="small">Nº {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="small">Data: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</div>
                    <div class="mt-1">
                        @php
                            $statusMap = ['concluida' => 'success', 'pendente' => 'warning', 'cancelada' => 'danger'];
                            $statusLabel = ['concluida' => 'Concluída', 'pendente' => 'Pendente', 'cancelada' => 'Cancelada'];
                        @endphp
                        <span class="badge bg-{{ $statusMap[$sale->status] ?? 'secondary' }}">{{ $statusLabel[$sale->status] ?? $sale->status }}</span>
                    </div>
                </div>
            </div>

            {{-- Cliente --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted fw-semibold small mb-2">Cliente</h6>
                    <div class="fw-bold">{{ $sale->customer->name ?? $sale->customer_name }}</div>
                    @if($sale->customer)
                        @if($sale->customer->cpf_cnpj) <div class="small text-muted">CPF/CNPJ: {{ $sale->customer->cpf_cnpj }}</div> @endif
                        @if($sale->customer->email) <div class="small text-muted">{{ $sale->customer->email }}</div> @endif
                        @if($sale->customer->phone) <div class="small text-muted">{{ $sale->customer->phone }}</div> @endif
                        @if($sale->customer->address) <div class="small text-muted">{{ $sale->customer->address }}</div> @endif
                    @endif
                </div>
                @if($sale->notes)
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted fw-semibold small mb-2">Observações</h6>
                    <p class="small">{{ $sale->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Itens --}}
            <h6 class="text-uppercase text-muted fw-semibold small mb-3">Itens</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th class="text-center">Qtd</th>
                            <th class="text-end">Preço Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ $item->product->name ?? '—' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td class="text-end fw-semibold">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end text-primary fs-5">R$ {{ number_format($sale->total, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Rodapé --}}
            <div class="text-center text-muted small mt-4 pt-3 border-top">
                Documento gerado eletronicamente pelo sistema Invexa em {{ now()->format('d/m/Y \à\s H:i') }}.
            </div>
        </div>
    </div>
</div>
@endsection
