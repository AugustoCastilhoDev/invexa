@extends('layouts.app')

@section('title', 'Relatório de Estoque')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white"><i class="bi bi-boxes me-2"></i>Relatório de Estoque</h4>
            <p class="text-soft mb-0">Posição atual de estoque por produto.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.stock.pdf', request()->query()) }}" target="_blank"
               class="btn btn-sm btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.stock.csv', request()->query()) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('reports.stock') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <label class="form-label text-soft" style="font-size:.78rem;">Filtrar por estoque</label>
                <select name="filter" class="form-select bg-dark text-white border-secondary" onchange="this.form.submit()">
                    <option value="all"      @selected(($filter ?? 'all') === 'all')>Todos os produtos ativos</option>
                    <option value="low"      @selected(($filter ?? '') === 'low')>Apenas estoque baixo</option>
                    <option value="inactive" @selected(($filter ?? '') === 'inactive')>Apenas produtos inativos</option>
                </select>
            </div>
        </form>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Produtos</p>
                        <p class="text-white fw-semibold mb-0 fs-5">{{ $totalActive }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Estoque baixo</p>
                        <p class="text-{{ $totalLow > 0 ? 'warning' : 'white' }} fw-semibold mb-0 fs-5">{{ $totalLow }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Valor em estoque</p>
                        <p class="text-white fw-semibold mb-0 fs-5">R$ {{ number_format($totalValue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card card-dark-bg shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                       color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                <th class="ps-3 py-2">Produto</th>
                                <th class="py-2">Categoria</th>
                                <th class="py-2 text-center">Qtd.</th>
                                <th class="py-2 text-center">Mín.</th>
                                <th class="py-2 text-end">Custo</th>
                                <th class="py-2 text-end">Venda</th>
                                <th class="py-2 text-center pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $p)
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-3 py-2 text-white" style="font-size:.85rem;">{{ $p->name }}</td>
                                <td class="py-2 text-soft" style="font-size:.82rem;">{{ optional($p->category)->name ?? '—' }}</td>
                                <td class="py-2 text-center
                                    {{ ($p->min_quantity > 0 && $p->quantity <= $p->min_quantity) ? 'text-warning fw-semibold' : 'text-white' }}">
                                    {{ $p->quantity }}
                                </td>
                                <td class="py-2 text-center text-soft">{{ $p->min_quantity ?? 0 }}</td>
                                <td class="py-2 text-end text-soft">R$ {{ number_format($p->cost_price ?? 0, 2, ',', '.') }}</td>
                                <td class="py-2 text-end text-white">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
                                <td class="py-2 text-center pe-3">
                                    @if(!$p->active)
                                        <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25" style="font-size:.72rem;">Inativo</span>
                                    @elseif($p->min_quantity > 0 && $p->quantity <= $p->min_quantity)
                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25" style="font-size:.72rem;">Estoque Baixo</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25" style="font-size:.72rem;">OK</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-soft">
                                    <i class="bi bi-inbox d-block fs-4 opacity-25 mb-1"></i>
                                    Nenhum produto encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /card-body --}}
</div>{{-- /card --}}
@endsection
