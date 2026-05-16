@extends('layouts.app')
@section('title', $customer->name)

@push('styles')
<style>
body { background: radial-gradient(circle at top left,rgba(96,165,250,.10),transparent 20%), radial-gradient(circle at bottom right,rgba(34,197,94,.12),transparent 18%), #08101d; color:#e2e8f0; }
.card-dark-bg { background:rgba(15,23,42,.88); border:1px solid rgba(148,163,184,.14); }
.card-header-dark { background:rgba(15,23,42,.92); border-color:rgba(148,163,184,.12); }
.text-soft { color:rgba(226,232,240,.72) !important; }
.kpi-mini { background:rgba(255,255,255,.04); border:1px solid rgba(148,163,184,.10); border-radius:.65rem; padding:1rem 1.25rem; }
.kpi-mini .label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:rgba(226,232,240,.55); }
.kpi-mini .value { font-size:1.45rem; font-weight:700; color:#f1f5f9; line-height:1.1; margin-top:.2rem; }
.table-dark-custom thead th { font-size:.70rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(148,163,184,.88) !important; border-bottom:1px solid rgba(148,163,184,.28) !important; padding:.9rem .75rem; white-space:nowrap; }
.table-dark-custom tbody td { font-size:.875rem; color:#cbd5e1; border-color:rgba(148,163,184,.07); vertical-align:middle; padding:.7rem .75rem; }
.badge-status { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:600; padding:.28rem .65rem; border-radius:999px; }
.badge-status::before { content:''; width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.badge-concluida { background:rgba(25,135,84,.20); color:#4ade80; border:1px solid rgba(25,135,84,.25); }
.badge-concluida::before { background:#4ade80; }
.badge-pendente  { background:rgba(255,193,7,.16);  color:#facc15; border:1px solid rgba(255,193,7,.24); }
.badge-pendente::before  { background:#facc15; }
.badge-cancelada { background:rgba(220,53,69,.14);  color:#f87171; border:1px solid rgba(220,53,69,.22); }
.badge-cancelada::before { background:#f87171; }
.filter-bar { background:rgba(255,255,255,.03); border:1px solid rgba(148,163,184,.10); border-radius:.65rem; padding:1rem 1.25rem; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-start gap-3 mb-4 flex-wrap">
    <a href="{{ route('customers.index') }}" class="btn btn-outline-light btn-sm mt-1">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h1 class="h3 mb-0 text-white">{{ $customer->name }}</h1>
            @if($customer->active)
                <span style="background:rgba(25,135,84,.20);color:#4ade80;border:1px solid rgba(25,135,84,.25);padding:.2rem .6rem;border-radius:999px;font-size:.72rem;font-weight:600;">Ativo</span>
            @else
                <span style="background:rgba(220,53,69,.14);color:#f87171;border:1px solid rgba(220,53,69,.22);padding:.2rem .6rem;border-radius:999px;font-size:.72rem;font-weight:600;">Inativo</span>
            @endif
        </div>
        <p class="text-soft mb-0 mt-1">{{ $customer->document_formatted ?: 'Sem documento' }}
            @if($customer->city) &nbsp;·&nbsp; {{ $customer->city }}{{ $customer->state ? '/' . $customer->state : '' }} @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-warning">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <form method="POST" action="{{ route('customers.destroy', $customer) }}"
              onsubmit="return confirm('Excluir {{ addslashes($customer->name) }}?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Excluir</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="kpi-mini">
            <div class="label">Compras</div>
            <div class="value">{{ $totalSales }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-mini">
            <div class="label">Total bruto</div>
            <div class="value" style="font-size:1.15rem;">R$ {{ number_format($totalSpent, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-mini">
            <div class="label">Devoluções ({{ $returnsCount }})</div>
            <div class="value" style="font-size:1.15rem; color:#f87171;">
                @if($returnsTotal > 0) &minus; @endif
                R$ {{ number_format($returnsTotal, 2, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-mini" style="border-color:rgba(251,191,36,.2);">
            <div class="label">Total líquido</div>
            <div class="value" style="font-size:1.15rem; color:#fbbf24;">R$ {{ number_format($netSpent, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-mini">
            <div class="label">Ticket médio</div>
            <div class="value" style="font-size:1.15rem;">R$ {{ number_format($avgTicket, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="kpi-mini">
            <div class="label">Última compra</div>
            <div class="value" style="font-size:1rem;">
                {{ $lastSale ? optional($lastSale->sale_date)->format('d/m/Y') : '—' }}
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Informações --}}
    <div class="col-12 col-xl-4">
        <div class="card card-dark-bg shadow-sm h-100">
            <div class="card-header card-header-dark border-bottom">
                <h5 class="mb-0 text-white">Informações</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-5 text-soft">E-mail</dt>
                    <dd class="col-7 text-white">{{ $customer->email ?: '—' }}</dd>

                    <dt class="col-5 text-soft">Telefone</dt>
                    <dd class="col-7 text-white">{{ $customer->phone ?: '—' }}</dd>

                    <dt class="col-5 text-soft">Endereço</dt>
                    <dd class="col-7 text-white">{{ $customer->address ?: '—' }}</dd>

                    <dt class="col-5 text-soft">Cidade/UF</dt>
                    <dd class="col-7 text-white">
                        {{ $customer->city ? $customer->city . ($customer->state ? '/' . $customer->state : '') : '—' }}
                    </dd>

                    <dt class="col-5 text-soft">Observações</dt>
                    <dd class="col-7 text-white">{{ $customer->notes ?: '—' }}</dd>

                    <dt class="col-5 text-soft">Cadastrado em</dt>
                    <dd class="col-7 text-white">{{ $customer->created_at->format('d/m/Y') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Histórico de compras --}}
    <div class="col-12 col-xl-8">
        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 text-white">Histórico de compras</h5>
                    <small class="text-soft">{{ $sales->total() }} venda(s) encontrada(s)</small>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="px-3 pt-3">
                <form method="GET" action="{{ route('customers.show', $customer) }}" class="filter-bar">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="text-soft" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em;">De</label>
                            <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm bg-transparent border-secondary text-white">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="text-soft" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em;">Até</label>
                            <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm bg-transparent border-secondary text-white">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="text-soft" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em;">Status</label>
                            <select name="status" class="form-select form-select-sm bg-transparent border-secondary text-white">
                                <option value="">Todos</option>
                                <option value="concluida" {{ $status === 'concluida' ? 'selected' : '' }}>Concluída</option>
                                <option value="pendente"  {{ $status === 'pendente'  ? 'selected' : '' }}>Pendente</option>
                                <option value="cancelada" {{ $status === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary w-100">Limpar</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0 mt-2">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 table-dark-custom">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Data</th>
                                <th>Itens</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td class="ps-3 text-soft">{{ $sale->id }}</td>
                                    <td>{{ optional($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                    <td class="text-soft" style="font-size:.82rem;">
                                        @if($sale->items->isNotEmpty())
                                            {{ $sale->items->take(2)->map(fn($i) => $i->product->name ?? 'Produto')->join(', ') }}
                                            @if($sale->items->count() > 2)
                                                <span class="text-soft"> +{{ $sale->items->count() - 2 }}</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-white">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $map = [
                                                'concluida' => ['badge-status badge-concluida', 'Concluída'],
                                                'pendente'  => ['badge-status badge-pendente',  'Pendente'],
                                                'cancelada' => ['badge-status badge-cancelada', 'Cancelada'],
                                            ];
                                            [$cls, $lbl] = $map[$sale->status] ?? ['badge-status', ucfirst($sale->status)];
                                        @endphp
                                        <span class="{{ $cls }}">{{ $lbl }}</span>
                                    </td>
                                    <td class="pe-3">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-light">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-soft py-4">Nenhuma compra encontrada para os filtros selecionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                @if($sales->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
