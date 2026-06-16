@extends('layouts.app')

@section('title', 'NF-e')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white"><i class="bi bi-file-earmark-text me-2"></i>Notas Fiscais Eletrônicas</h4>
            <p class="text-soft mb-0">Gerencie a emissão, consulta e cancelamento de NF-e.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Dashboard</a>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1d4ed8, #2563eb);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Total</div>
                        <h3 class="mb-1">{{ $totals['total'] }}</h3>
                        <div class="text-white-75 small">Todas as NF-e</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #16a34a, #22c55e);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Autorizadas</div>
                        <h3 class="mb-1">{{ $totals['autorizada'] }}</h3>
                        <div class="text-white-75 small">Emitidas com sucesso</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b, #eab308);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Pendentes</div>
                        <h3 class="mb-1">{{ $totals['pendente'] }}</h3>
                        <div class="text-white-75 small">Aguardando retorno</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card dashboard-card text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6b7280, #9ca3af);">
                    <div class="card-body">
                        <div class="text-soft small text-uppercase fw-semibold mb-2">Canceladas</div>
                        <h3 class="mb-1">{{ $totals['cancelada'] }}</h3>
                        <div class="text-white-75 small">Canceladas / rejeitadas</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('nfes.index') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <label for="search" class="form-label text-white">Buscar</label>
                <input type="text" id="search" name="search" class="form-control bg-dark text-white border-secondary"
                       value="{{ request('search') }}" placeholder="Número NF-e, chave ou cliente">
            </div>
            <div class="col-12 col-md-2">
                <label for="status" class="form-label text-white">Status</label>
                <select id="status" name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos</option>
                    <option value="pendente"   @selected(request('status') === 'pendente')>Pendente</option>
                    <option value="processando" @selected(request('status') === 'processando')>Processando</option>
                    <option value="autorizada" @selected(request('status') === 'autorizada')>Autorizada</option>
                    <option value="rejeitada"  @selected(request('status') === 'rejeitada')>Rejeitada</option>
                    <option value="cancelada"  @selected(request('status') === 'cancelada')>Cancelada</option>
                    <option value="erro"       @selected(request('status') === 'erro')>Erro</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label for="from" class="form-label text-white">De</label>
                <input type="date" id="from" name="from" class="form-control bg-dark text-white border-secondary" value="{{ request('from') }}">
            </div>
            <div class="col-12 col-md-2">
                <label for="to" class="form-label text-white">Até</label>
                <input type="date" id="to" name="to" class="form-control bg-dark text-white border-secondary" value="{{ request('to') }}">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('nfes.index') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-3 py-3">Número / Série</th>
                        <th class="py-3">Venda</th>
                        <th class="py-3">Emissão</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Chave de Acesso</th>
                        <th class="py-3 text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nfes as $nfe)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-3 py-3">
                                <div class="fw-semibold text-white">{{ $nfe->numero_formatado }}</div>
                                <div class="text-soft small">Série {{ $nfe->serie }}</div>
                            </td>
                            <td class="py-3">
                                @if($nfe->sale)
                                    <a href="{{ route('sales.show', $nfe->sale_id) }}" class="text-info small">
                                        Venda #{{ $nfe->sale->sale_number }}
                                    </a>
                                    <div class="text-soft small">{{ $nfe->sale->customer_name ?? '—' }}</div>
                                @else
                                    <span class="text-soft">—</span>
                                @endif
                            </td>
                            <td class="py-3" style="color:#94a3b8;font-size:.875rem;">
                                {{ $nfe->data_emissao ? $nfe->data_emissao->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="py-3">
                                <span class="badge bg-{{ $nfe->status_badge }}">
                                    {{ $nfe->status_label }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($nfe->chave_acesso)
                                    <span class="text-soft font-monospace" style="font-size:.7rem;">
                                        {{ substr($nfe->chave_acesso, 0, 20) }}…
                                    </span>
                                @else
                                    <span class="text-soft">—</span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-3">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    <a href="{{ route('nfes.show', $nfe) }}" class="btn btn-sm btn-outline-light"
                                       title="Detalhes"><i class="bi bi-eye"></i></a>

                                    @if($nfe->status === 'pendente')
                                        <form action="{{ route('nfes.consultar', $nfe) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Consultar SEFAZ">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($nfe->status === 'autorizada')
                                        <a href="{{ route('nfes.xml', $nfe) }}" class="btn btn-sm btn-outline-secondary" title="Baixar XML">
                                            <i class="bi bi-file-code"></i>
                                        </a>
                                        <a href="{{ route('nfes.danfe', $nfe) }}" class="btn btn-sm btn-outline-secondary" title="Baixar DANFE">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('nfes.cancelar', $nfe) }}" method="POST"
                                              onsubmit="return confirm('Informe a justificativa:\n' + (document.getElementById('just_{{ $nfe->id }}').value || '') + '\n\nConfirmar cancelamento?')">
                                            @csrf
                                            <input type="hidden" id="just_{{ $nfe->id }}" name="justificativa"
                                                   value="Cancelamento solicitado pelo emitente">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar NF-e">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-soft">
                                <i class="bi bi-file-earmark-text d-block mb-2" style="font-size:2rem;"></i>
                                Nenhuma NF-e encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $nfes->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
