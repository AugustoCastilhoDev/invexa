@extends('layouts.app')

@section('title', 'NF-e')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0">Notas Fiscais Eletrônicas</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Venda</th>
                        <th>Cliente</th>
                        <th>Emissão</th>
                        <th>Valor Total</th>
                        <th>Ambiente</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nfes as $nfe)
                        <tr>
                            <td>{{ $nfe->numero_formatado ?? '—' }}</td>
                            <td>
                                @if($nfe->sale)
                                    <a href="{{ route('sales.show', $nfe->sale) }}">#{{ $nfe->sale->sale_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $nfe->customer?->name ?? '—' }}</td>
                            <td>{{ $nfe->data_emissao?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>R$ {{ number_format($nfe->valor_total, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $nfe->ambiente === 'producao' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($nfe->ambiente) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $nfe->status_badge }}">
                                    {{ $nfe->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('nfes.show', $nfe) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nenhuma NF-e emitida ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($nfes->hasPages())
            <div class="card-footer">
                {{ $nfes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
