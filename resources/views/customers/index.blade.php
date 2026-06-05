@extends('layouts.app')
@section('title', 'Clientes')

@push('styles')
<style>
body {
    background: radial-gradient(circle at top left, rgba(96,165,250,.10), transparent 20%),
                radial-gradient(circle at bottom right, rgba(34,197,94,.12), transparent 18%),
                #08101d;
    color: #e2e8f0;
}
.card-dark-bg  { background: rgba(15,23,42,.88); border: 1px solid rgba(148,163,184,.14); }
.card-header-dark { background: rgba(15,23,42,.92); border-color: rgba(148,163,184,.12); }
.text-soft { color: rgba(226,232,240,.72) !important; }
.table-dark-custom thead th {
    font-size:.70rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
    color:rgba(148,163,184,.88) !important; border-bottom:1px solid rgba(148,163,184,.28) !important;
    padding-top:.9rem; padding-bottom:.9rem; white-space:nowrap;
}
.table-dark-custom tbody td {
    font-size:.875rem; color:#cbd5e1; border-color:rgba(148,163,184,.07);
    vertical-align:middle; padding-top:.7rem; padding-bottom:.7rem;
}
.table-dark-custom tbody tr:last-child td { border-bottom:0; }
.badge-active   { background:rgba(25,135,84,.20);  color:#4ade80; border:1px solid rgba(25,135,84,.25);  font-size:.72rem; padding:.3rem .65rem; border-radius:999px; }
.badge-inactive { background:rgba(220,53,69,.14);  color:#f87171; border:1px solid rgba(220,53,69,.22);  font-size:.72rem; padding:.3rem .65rem; border-radius:999px; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 mb-1 text-white">Clientes</h1>
        <p class="text-soft mb-0">Gerencie sua base de clientes.</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Novo Cliente
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filtros --}}
<div class="card card-dark-bg shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label text-soft small mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control bg-dark text-white border-secondary"
                       placeholder="Nome, documento, e-mail ou telefone...">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label text-soft small mb-1">Status</label>
                <select name="status" class="form-select bg-dark text-white border-secondary">
                    <option value="">Todos</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-light ms-1">Limpar</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabela --}}
<div class="card card-dark-bg shadow-sm">
    <div class="card-header card-header-dark border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">Lista de clientes</h5>
        <small class="text-soft">{{ $customers->total() }} encontrado(s)</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 table-dark-custom">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Nome</th>
                        <th>Documento</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Compras</th>
                        <th>Total gasto</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="ps-3 text-soft">{{ $customer->id }}</td>
                            <td class="fw-semibold text-white">
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="text-white text-decoration-none">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="text-soft">{{ $customer->document_formatted ?: '—' }}</td>
                            <td class="text-soft">{{ $customer->phone ?: '—' }}</td>
                            <td class="text-soft">{{ $customer->email ?: '—' }}</td>
                            <td>{{ $customer->sales_count }}</td>
                            <td class="fw-semibold" style="color:#4ade80;">
                                R$ {{ number_format($customer->sales_sum_total ?? 0, 2, ',', '.') }}
                            </td>
                            <td>
                                @if($customer->active)
                                    <span class="badge-active">Ativo</span>
                                @else
                                    <span class="badge-inactive">Inativo</span>
                                @endif
                            </td>
                            <td class="pe-3">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('customers.show', $customer) }}"
                                       class="btn btn-sm btn-outline-light" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                          onsubmit="return confirm('Excluir cliente {{ addslashes($customer->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-soft py-5">Nenhum cliente encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
        <div class="card-footer card-header-dark border-top">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
