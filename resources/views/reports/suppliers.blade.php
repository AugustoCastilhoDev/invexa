@extends('layouts.app')

@section('title', 'Relatório de Fornecedores')

@push('styles')
<style>
    .kpi-card { border-radius:.75rem; padding:1.25rem 1.5rem; }
    .kpi-label { font-size:.72rem; text-transform:uppercase; font-weight:700; letter-spacing:.08em; opacity:.75; }
    .kpi-value { font-size:1.75rem; font-weight:700; line-height:1.2; margin-top:.25rem; }
    .kpi-sub   { font-size:.78rem; opacity:.65; margin-top:.2rem; }
</style>
@endpush

@section('content')
<div class="card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white"><i class="bi bi-building me-2" style="color:#a855f7;"></i>Relatório de Fornecedores</h4>
            <p class="text-soft mb-0">Lista e desempenho de fornecedores cadastrados.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.suppliers.pdf') }}" class="btn btn-sm btn-outline-danger" target="_blank">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.suppliers.csv') }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <div class="card-body">

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="kpi-card text-white" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);">
                    <div class="kpi-label">Total de Fornecedores</div>
                    <div class="kpi-value">{{ $total }}</div>
                    <div class="kpi-sub">cadastrados</div>
                </div>
            </div>
        </div>

        <div class="card card-dark-bg shadow-sm">
            <div class="card-header card-header-dark border-bottom">
                <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                    <i class="bi bi-table me-1"></i>Lista de Fornecedores
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr style="font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
                                       color:rgba(148,163,184,.8);border-bottom:1px solid rgba(148,163,184,.15);">
                                <th class="ps-3 py-2">Nome</th>
                                <th class="py-2">CNPJ/CPF</th>
                                <th class="py-2">E-mail</th>
                                <th class="py-2">Telefone</th>
                                <th class="py-2 pe-3">Cidade/UF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $s)
                            <tr style="border-color:rgba(148,163,184,.07);">
                                <td class="ps-3 py-2 text-white fw-semibold" style="font-size:.85rem;">{{ $s->name }}</td>
                                <td class="py-2 text-soft" style="font-size:.82rem;">{{ $s->document ?? '—' }}</td>
                                <td class="py-2 text-soft" style="font-size:.82rem;">{{ $s->email ?? '—' }}</td>
                                <td class="py-2 text-soft" style="font-size:.82rem;">{{ $s->phone ?? '—' }}</td>
                                <td class="py-2 text-soft pe-3" style="font-size:.82rem;">{{ $s->city ? $s->city . '/' . $s->state : '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-soft">
                                    <i class="bi bi-inbox d-block fs-4 opacity-25 mb-1"></i>
                                    Nenhum fornecedor cadastrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
