@extends('layouts.app')

@section('title', 'Fornecedores')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">Fornecedores</h4>
            <p class="text-soft mb-0">Gerencie seus fornecedores e contatos comerciais.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Dashboard</a>
            @if(auth()->user()->hasRole(['admin','gerente']))
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Novo Fornecedor
                </a>
            @endif
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Cards de resumo --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm"
                     style="background:linear-gradient(135deg,#1d4ed8,#2563eb);">
                    <div class="card-body py-3">
                        <div class="text-soft small text-uppercase fw-semibold mb-1">Total</div>
                        <h3 class="mb-0">{{ $totalSuppliers }}</h3>
                        <div class="text-white-75 small">Fornecedores cadastrados</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm"
                     style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                    <div class="card-body py-3">
                        <div class="text-soft small text-uppercase fw-semibold mb-1">Ativos</div>
                        <h3 class="mb-0">{{ $activeSuppliers }}</h3>
                        <div class="text-white-75 small">Fornecedores ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card dashboard-card text-white border-0 shadow-sm"
                     style="background:linear-gradient(135deg,#64748b,#475569);">
                    <div class="card-body py-3">
                        <div class="text-soft small text-uppercase fw-semibold mb-1">Inativos</div>
                        <h3 class="mb-0">{{ $totalSuppliers - $activeSuppliers }}</h3>
                        <div class="text-white-75 small">Fornecedores inativos</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('suppliers.index') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary"
                       placeholder="Buscar por nome, razão social ou CNPJ..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3">
                <select name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos os status</option>
                    <option value="active"   @selected(request('status') === 'active')>Ativos</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inativos</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-light flex-grow-1">Limpar</a>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                               color:rgba(148,163,184,.85);border-bottom:1px solid rgba(148,163,184,.15);">
                        <th class="ps-3 py-3">Fornecedor</th>
                        <th class="py-3">Documento</th>
                        <th class="py-3">Contato</th>
                        <th class="py-3">Cidade / UF</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr style="border-color:rgba(148,163,184,.07);">
                            <td class="ps-3 py-3">
                                <div class="fw-semibold text-white">{{ $supplier->name }}</div>
                                @if($supplier->trade_name)
                                    <div class="text-soft small">{{ $supplier->trade_name }}</div>
                                @endif
                            </td>
                            <td class="py-3" style="color:#94a3b8;font-size:.85rem;font-family:monospace;">
                                {{ $supplier->document_formatted }}
                            </td>
                            <td class="py-3" style="font-size:.85rem;">
                                @if($supplier->email)
                                    <div class="text-soft"><i class="bi bi-envelope me-1"></i>{{ $supplier->email }}</div>
                                @endif
                                @if($supplier->phone)
                                    <div class="text-soft"><i class="bi bi-telephone me-1"></i>{{ $supplier->phone }}</div>
                                @endif
                                @if(!$supplier->email && !$supplier->phone)
                                    <span class="text-soft">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3" style="color:#94a3b8;font-size:.85rem;">
                                {{ $supplier->city }}{{ $supplier->city && $supplier->state ? '/' : '' }}{{ $supplier->state }}
                                @if(!$supplier->city && !$supplier->state) &mdash; @endif
                            </td>
                            <td class="py-3">
                                @if($supplier->active)
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;
                                                 font-weight:600;padding:.28rem .65rem;border-radius:999px;
                                                 background:rgba(34,197,94,.12);color:#4ade80;
                                                 border:1px solid rgba(34,197,94,.25);">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;"></span>Ativo
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;
                                                 font-weight:600;padding:.28rem .65rem;border-radius:999px;
                                                 background:rgba(148,163,184,.1);color:#94a3b8;
                                                 border:1px solid rgba(148,163,184,.2);">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#94a3b8;"></span>Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-end pe-3">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasRole(['admin','gerente']))
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                              onsubmit="return confirm('Excluir o fornecedor {{ addslashes($supplier->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-soft">
                                <i class="bi bi-truck fs-2 d-block mb-2 opacity-25"></i>
                                Nenhum fornecedor encontrado.
                                @if(auth()->user()->hasRole(['admin','gerente']))
                                    <div class="mt-2">
                                        <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">Cadastrar primeiro fornecedor</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $suppliers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
