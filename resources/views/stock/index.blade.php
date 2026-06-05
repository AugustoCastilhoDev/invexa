@extends('layouts.app')

@section('title', 'Movimentações de Estoque')

@section('content')
<div class="card card-dark-bg shadow-sm border-0">

    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h4 class="mb-1 text-white">Movimentações de Estoque</h4>
                <p class="text-soft mb-0">Histórico completo de entradas, saídas e ajustes.</p>
            </div>
            <a href="{{ route('stock.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Nova Entrada
            </a>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert"
                 style="background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.3);color:#4ade80;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('stock.index') }}" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label text-soft" style="font-size:.78rem;">Produto</label>
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">Todos os produtos</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label text-soft" style="font-size:.78rem;">Tipo</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('type')=='entrada'?'selected':'' }}>Entrada</option>
                        <option value="saida"   {{ request('type')=='saida'  ?'selected':'' }}>Saída</option>
                        <option value="ajuste"  {{ request('type')=='ajuste' ?'selected':'' }}>Ajuste</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label text-soft" style="font-size:.78rem;">De</label>
                    <input type="date" name="from" class="form-control form-control-sm"
                           value="{{ request('from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label text-soft" style="font-size:.78rem;">Até</label>
                    <input type="date" name="to" class="form-control form-control-sm"
                           value="{{ request('to') }}">
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Limpar</a>
                </div>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="table-responsive">
            @if($movements->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-archive fs-1 text-soft"></i>
                    <p class="text-soft mt-3">Nenhuma movimentação encontrada.</p>
                </div>
            @else
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-4 py-3">Data</th>
                        <th class="py-3">Produto</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Motivo</th>
                        <th class="py-3">Qtd. Antes</th>
                        <th class="py-3">Movimento</th>
                        <th class="py-3">Qtd. Depois</th>
                        <th class="py-3">Usuário</th>
                        <th class="py-3">Obs.</th>
                        <th class="py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $mov)
                    <tr style="border-color:rgba(148,163,184,.07);">
                        <td class="ps-4 py-3" style="color:#94a3b8;font-size:.82rem;">
                            {{ $mov->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3">
                            @if($mov->product)
                                <a href="{{ route('products.show', $mov->product_id) }}"
                                   class="text-white fw-semibold text-decoration-none">
                                    {{ $mov->product->name }}
                                </a>
                            @else
                                <span class="text-soft">Produto removido</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="badge bg-{{ $mov->type_badge }}">
                                {{ $mov->type_label }}
                            </span>
                        </td>
                        <td class="py-3" style="color:#94a3b8;">{{ $mov->reason_label }}</td>
                        <td class="py-3" style="color:#94a3b8;">{{ $mov->quantity_before }} un.</td>
                        <td class="py-3 fw-semibold">
                            @if($mov->quantity > 0)
                                <span class="text-success">+{{ $mov->quantity }}</span>
                            @elseif($mov->quantity < 0)
                                <span class="text-danger">{{ $mov->quantity }}</span>
                            @else
                                <span class="text-soft">0</span>
                            @endif
                        </td>
                        <td class="py-3 text-white fw-semibold">{{ $mov->quantity_after }} un.</td>
                        <td class="py-3" style="color:#94a3b8;font-size:.82rem;">
                            {{ $mov->user->name ?? 'Sistema' }}
                        </td>
                        <td class="py-3" style="color:#94a3b8;font-size:.82rem;">
                            {{ Str::limit($mov->notes, 40) ?: '-' }}
                        </td>
                        <td class="py-3 pe-3">
                            @if(is_null($mov->source_type))
                                <form method="POST"
                                      action="{{ route('stock.destroy', $mov) }}"
                                      onsubmit="return confirm('Excluir esta movimentação e estornar o estoque?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir e estornar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-soft" style="font-size:.72rem;" title="Vinculado a uma venda">
                                    <i class="bi bi-lock"></i>
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Paginação --}}
        @if($movements->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Anterior --}}
                        @if($movements->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" style="background:rgba(15,23,42,.8);border-color:rgba(148,163,184,.2);color:rgba(148,163,184,.4);">«</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $movements->previousPageUrl() }}"
                                   style="background:rgba(15,23,42,.8);border-color:rgba(148,163,184,.2);color:#94a3b8;">«</a>
                            </li>
                        @endif

                        {{-- Páginas --}}
                        @foreach($movements->getUrlRange(1, $movements->lastPage()) as $page => $url)
                            @if($page == $movements->currentPage())
                                <li class="page-item active">
                                    <span class="page-link"
                                          style="background:rgba(99,102,241,.8);border-color:rgba(99,102,241,.5);color:#fff;">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}"
                                       style="background:rgba(15,23,42,.8);border-color:rgba(148,163,184,.2);color:#94a3b8;">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Próxima --}}
                        @if($movements->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $movements->nextPageUrl() }}"
                                   style="background:rgba(15,23,42,.8);border-color:rgba(148,163,184,.2);color:#94a3b8;">»</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link" style="background:rgba(15,23,42,.8);border-color:rgba(148,163,184,.2);color:rgba(148,163,184,.4);">»</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif

    </div>
</div>
@endsection
