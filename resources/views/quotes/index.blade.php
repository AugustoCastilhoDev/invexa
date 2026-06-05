@extends('layouts.app')

@section('title', 'Orçamentos')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white"><i class="bi bi-file-earmark-text me-2"></i>Orçamentos</h4>
                <p class="text-soft mb-0">Gerencie os orçamentos enviados aos clientes.</p>
            </div>
            <a href="{{ route('quotes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Novo Orçamento
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('quotes.index') }}" class="row g-2 mb-4">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Buscar por número ou cliente..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="draft"     @selected(request('status')==='draft')>Rascunho</option>
                    <option value="sent"      @selected(request('status')==='sent')>Enviado</option>
                    <option value="accepted"  @selected(request('status')==='accepted')>Aceito</option>
                    <option value="rejected"  @selected(request('status')==='rejected')>Recusado</option>
                    <option value="expired"   @selected(request('status')==='expired')>Expirado</option>
                    <option value="converted" @selected(request('status')==='converted')>Convertido</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-light">Filtrar</button>
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary ms-1">Limpar</a>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr class="text-soft" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Validade</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotes as $quote)
                        @php
                            $expired = $quote->valid_until && $quote->valid_until->isPast()
                                && !in_array($quote->status, ['converted','rejected']);
                        @endphp
                        <tr>
                            <td class="fw-semibold text-white">{{ $quote->number }}</td>
                            <td class="text-soft">{{ $quote->customer?->name ?? '—' }}</td>
                            <td class="text-white">R$ {{ number_format($quote->total, 2, ',', '.') }}</td>
                            <td class="{{ $expired ? 'text-warning' : 'text-soft' }}">
                                {{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : '—' }}
                                @if($expired) <i class="bi bi-exclamation-triangle-fill ms-1"></i> @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $quote->statusBadgeClass() }}" style="font-size:.7rem; letter-spacing:.05em;">
                                    {{ $quote->statusLabel() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('quotes.show', $quote) }}" class="btn btn-sm btn-outline-light" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($quote->status !== 'converted')
                                    <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-sm btn-outline-secondary ms-1" target="_blank" title="PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-file-earmark-text text-soft" style="font-size:2.5rem;"></i>
                                <p class="text-soft mt-2 mb-1">Nenhum orçamento encontrado.</p>
                                <a href="{{ route('quotes.create') }}" class="text-primary">Criar primeiro orçamento</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotes->hasPages())
            <div class="mt-3">
                {{ $quotes->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
