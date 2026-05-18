@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Relatórios</h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">Visão geral do desempenho financeiro e operacional.</p>
    </div>
</div>

<div class="row g-4">

    {{-- Financeiro --}}
    <div class="col-12 col-md-6">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:rgba(99,102,241,.15);">
                        <i class="bi bi-graph-up-arrow fs-3" style="color:#818cf8;"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">Financeiro</h6>
                        <p class="text-soft mb-0" style="font-size:.8rem;">Receitas, despesas e saldo líquido por período</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('reports.financial') }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver Relatório
                    </a>
                    <a href="{{ route('reports.financial') }}?export=pdf" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-filetype-pdf me-1"></i>PDF
                    </a>
                    <a href="{{ route('reports.financial') }}?export=csv" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-filetype-csv me-1"></i>CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Compras --}}
    <div class="col-12 col-md-6">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:rgba(251,191,36,.15);">
                        <i class="bi bi-bag-check fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">Pedidos de Compra</h6>
                        <p class="text-soft mb-0" style="font-size:.8rem;">OCs por status, fornecedor e produtos mais comprados</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('reports.purchases') }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver Relatório
                    </a>
                    <a href="{{ route('reports.purchases.pdf') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-filetype-pdf me-1"></i>PDF
                    </a>
                    <a href="{{ route('reports.purchases.csv') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-filetype-csv me-1"></i>CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Vendas --}}
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:rgba(59,130,246,.15);">
                        <i class="bi bi-cart-check fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">Vendas</h6>
                        <p class="text-soft mb-0" style="font-size:.8rem;">Histórico e totais de vendas</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Contas a Pagar --}}
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:rgba(239,68,68,.15);">
                        <i class="bi bi-credit-card-2-front fs-3 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">Contas a Pagar</h6>
                        <p class="text-soft mb-0" style="font-size:.8rem;">Vencidas, pendentes e pagas</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('bills.index') }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Estoque --}}
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:rgba(34,197,94,.15);">
                        <i class="bi bi-boxes fs-3 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">Estoque</h6>
                        <p class="text-soft mb-0" style="font-size:.8rem;">Produtos, saldos e alertas</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Fornecedores --}}
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:rgba(168,85,247,.15);">
                        <i class="bi bi-building fs-3" style="color:#a855f7;"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">Fornecedores</h6>
                        <p class="text-soft mb-0" style="font-size:.8rem;">Lista e desempenho de fornecedores</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
