@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="text-white mb-1"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Relatórios</h4>
        <p class="text-soft mb-0" style="font-size:.85rem;">Visão geral do desempenho financeiro e operacional.</p>
    </div>
</div>

@php
$cards = [
    [
        'title'      => 'Financeiro',
        'desc'       => 'Receitas, despesas e saldo líquido por período',
        'icon'       => 'bi-graph-up-arrow',
        'color'      => '#818cf8',
        'bg'         => 'rgba(99,102,241,.15)',
        'route_view' => 'reports.financial',
        'route_pdf'  => 'reports.financial.pdf',
        'route_csv'  => 'reports.financial.csv',
    ],
    [
        'title'      => 'Vendas',
        'desc'       => 'Histórico e totais de vendas por período',
        'icon'       => 'bi-cart-check',
        'color'      => '#60a5fa',
        'bg'         => 'rgba(59,130,246,.15)',
        'route_view' => 'reports.sales',
        'route_pdf'  => 'reports.sales.pdf',
        'route_csv'  => 'reports.sales.csv',
    ],
    [
        'title'      => 'Devoluções',
        'desc'       => 'Devoluções por período, motivo e valor devolvido',
        'icon'       => 'bi-arrow-return-left',
        'color'      => '#fbbf24',
        'bg'         => 'rgba(251,191,36,.15)',
        'route_view' => 'reports.returns',
        'route_pdf'  => 'reports.returns.pdf',
        'route_csv'  => 'reports.returns.csv',
    ],
    [
        'title'      => 'Contas a Pagar',
        'desc'       => 'Vencidas, pendentes e pagas por período',
        'icon'       => 'bi-credit-card-2-front',
        'color'      => '#f87171',
        'bg'         => 'rgba(239,68,68,.15)',
        'route_view' => 'reports.bills',
        'route_pdf'  => 'reports.bills.pdf',
        'route_csv'  => 'reports.bills.csv',
    ],
    [
        'title'      => 'Pedidos de Compra',
        'desc'       => 'OCs por status, fornecedor e itens mais comprados',
        'icon'       => 'bi-bag-check',
        'color'      => '#fbbf24',
        'bg'         => 'rgba(251,191,36,.15)',
        'route_view' => 'reports.purchases',
        'route_pdf'  => 'reports.purchases.pdf',
        'route_csv'  => 'reports.purchases.csv',
    ],
    [
        'title'      => 'Estoque',
        'desc'       => 'Produtos, saldos e alertas de estoque baixo',
        'icon'       => 'bi-boxes',
        'color'      => '#4ade80',
        'bg'         => 'rgba(34,197,94,.15)',
        'route_view' => 'reports.stock',
        'route_pdf'  => 'reports.stock.pdf',
        'route_csv'  => 'reports.stock.csv',
    ],
    [
        'title'      => 'Fornecedores',
        'desc'       => 'Lista e desempenho de fornecedores cadastrados',
        'icon'       => 'bi-building',
        'color'      => '#c084fc',
        'bg'         => 'rgba(168,85,247,.15)',
        'route_view' => 'reports.suppliers',
        'route_pdf'  => 'reports.suppliers.pdf',
        'route_csv'  => 'reports.suppliers.csv',
    ],
    [
        'title'      => 'Produtos Mais Vendidos',
        'desc'       => 'Ranking dos produtos por quantidade e receita gerada',
        'icon'       => 'bi-trophy',
        'color'      => '#fb923c',
        'bg'         => 'rgba(249,115,22,.15)',
        'route_view' => 'reports.top-products',
        'route_pdf'  => 'reports.top-products.pdf',
        'route_csv'  => 'reports.top-products.csv',
    ],
];
@endphp

<div class="row g-4">
    @foreach($cards as $card)
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-dark-bg border border-secondary h-100">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3" style="background:{{ $card['bg'] }};">
                        <i class="bi {{ $card['icon'] }} fs-3" style="color:{{ $card['color'] }};"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">{{ $card['title'] }}</h6>
                        <p class="text-soft mb-0" style="font-size:.78rem;">{{ $card['desc'] }}</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route($card['route_view']) }}" class="btn btn-sm btn-outline-light flex-grow-1">
                        <i class="bi bi-eye me-1"></i>Ver Relatório
                    </a>
                    <a href="{{ route($card['route_pdf']) }}" target="_blank"
                       class="btn btn-sm btn-outline-danger" title="Exportar PDF">
                        <i class="bi bi-filetype-pdf"></i>
                    </a>
                    <a href="{{ route($card['route_csv']) }}"
                       class="btn btn-sm btn-outline-success" title="Exportar CSV">
                        <i class="bi bi-filetype-csv"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
