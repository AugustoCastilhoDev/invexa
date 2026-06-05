@extends('layouts.app')

@section('title', 'Relatório de Lucratividade')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">

    {{-- Header --}}
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white"><i class="bi bi-graph-up me-2"></i>Relatório de Lucratividade</h4>
            <p class="text-soft mb-0">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.profitability.pdf', request()->query()) }}" target="_blank"
               class="btn btn-sm btn-outline-danger">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.profitability.csv', request()->query()) }}"
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
        <form method="GET" action="{{ route('reports.profitability') }}" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-auto">
                    <label class="form-label text-soft" style="font-size:.78rem;">Período</label>
                    <select name="period" id="periodSelect" class="form-select form-select-sm"
                            onchange="toggleCustomDates(this.value)">
                        <option value="7"      {{ $period=='7'     ?'selected':'' }}>Últimos 7 dias</option>
                        <option value="30"     {{ $period=='30'    ?'selected':'' }}>Últimos 30 dias</option>
                        <option value="90"     {{ $period=='90'    ?'selected':'' }}>Últimos 90 dias</option>
                        <option value="365"    {{ $period=='365'   ?'selected':'' }}>Último ano</option>
                        <option value="custom" {{ $period=='custom'?'selected':'' }}>Personalizado</option>
                    </select>
                </div>
                <div class="col-12 col-md-auto" id="customDates"
                     style="{{ $period==='custom' ? '' : 'display:none;' }}">
                    <div class="d-flex gap-2">
                        <div>
                            <label class="form-label text-soft" style="font-size:.78rem;">De</label>
                            <input type="date" name="from" class="form-control form-control-sm"
                                   value="{{ request('from', $from->format('Y-m-d')) }}">
                        </div>
                        <div>
                            <label class="form-label text-soft" style="font-size:.78rem;">Até</label>
                            <input type="date" name="to" class="form-control form-control-sm"
                                   value="{{ request('to', $to->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="form-label text-soft" style="font-size:.78rem;">Categoria</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                </div>
            </div>
        </form>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Receita Total</p>
                        <p class="text-white fw-semibold mb-0 fs-5">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Custo Total</p>
                        <p class="text-white fw-semibold mb-0 fs-5">R$ {{ number_format($totalCost, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Lucro Bruto</p>
                        <p class="fw-semibold mb-0 fs-5 {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($totalProfit, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-body py-3">
                        <p class="text-soft mb-1" style="font-size:.78rem;">Margem Bruta</p>
                        <p class="fw-semibold mb-0 fs-5 {{ $totalMargin >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($totalMargin, 1, ',', '.') }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico --}}
        @if($items->isNotEmpty())
        <div class="card card-dark-bg border border-secondary mb-4">
            <div class="card-header card-header-dark">
                <span class="text-white fw-semibold">Top 10 por Lucro Bruto</span>
            </div>
            <div class="card-body">
                <canvas id="profitChart" height="100"></canvas>
            </div>
        </div>
        @endif

        {{-- Tabela --}}
        <div class="card card-dark-bg border border-secondary">
            <div class="card-header card-header-dark">
                <span class="text-white fw-semibold">Detalhamento por Produto</span>
            </div>
            <div class="table-responsive">
                @if($items->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-graph-up fs-1 text-soft"></i>
                    <p class="text-soft mt-3">Nenhuma venda concluída no período selecionado.</p>
                </div>
                @else
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                   color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Produto</th>
                            <th class="py-3">Categoria</th>
                            <th class="py-3">Qtd. Vendida</th>
                            <th class="py-3">Receita</th>
                            <th class="py-3">Custo Total</th>
                            <th class="py-3">Lucro Bruto</th>
                            <th class="py-3">Margem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i => $item)
                        @php
                            $margin = $item->total_revenue > 0
                                ? ($item->total_profit / $item->total_revenue) * 100
                                : 0;
                        @endphp
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-4 py-3 text-soft">{{ $i + 1 }}</td>
                            <td class="py-3 text-white fw-semibold">{{ $item->product_name }}</td>
                            <td class="py-3" style="color:#94a3b8;">{{ $item->category_name ?? 'Sem categoria' }}</td>
                            <td class="py-3">
                                <span class="badge bg-primary bg-opacity-75">{{ number_format($item->total_qty, 0, ',', '.') }} un.</span>
                            </td>
                            <td class="py-3 text-white">R$ {{ number_format($item->total_revenue, 2, ',', '.') }}</td>
                            <td class="py-3" style="color:#f87171;">R$ {{ number_format($item->total_cost, 2, ',', '.') }}</td>
                            <td class="py-3 fw-semibold {{ $item->total_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($item->total_profit, 2, ',', '.') }}
                            </td>
                            <td class="py-3">
                                <span class="badge {{ $margin >= 20 ? 'bg-success' : ($margin >= 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ number_format($margin, 1, ',', '.') }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($items->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const top10 = @json($items->take(10)->values());
    new Chart(document.getElementById('profitChart'), {
        type: 'bar',
        data: {
            labels: top10.map(p => p.product_name),
            datasets: [
                {
                    label: 'Receita',
                    data: top10.map(p => p.total_revenue),
                    backgroundColor: 'rgba(14,165,233,.5)',
                    borderColor: 'rgba(14,165,233,1)',
                    borderWidth: 1, borderRadius: 4,
                },
                {
                    label: 'Lucro Bruto',
                    data: top10.map(p => p.total_profit),
                    backgroundColor: 'rgba(34,197,94,.5)',
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 1, borderRadius: 4,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#94a3b8' } } },
            scales: {
                x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,.08)' } },
                y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,.08)' } }
            }
        }
    });
})();
</script>
@endif
<script>
function toggleCustomDates(val) {
    document.getElementById('customDates').style.display = val === 'custom' ? '' : 'none';
}
</script>
@endpush
