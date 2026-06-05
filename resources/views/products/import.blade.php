@extends('layouts.app')

@section('title', 'Importar Produtos')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white"><i class="bi bi-upload me-2"></i>Importar Produtos via CSV</h4>
            <p class="text-soft mb-0">Importe múltiplos produtos de uma só vez usando uma planilha CSV.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-light">Voltar</a>
    </div>
    <div class="card-body">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Instruções --}}
        <div class="row g-4 mb-4">
            <div class="col-md-7">
                <div class="p-3 rounded" style="background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.2);">
                    <h6 class="text-info mb-3"><i class="bi bi-info-circle me-2"></i>Instruções</h6>
                    <ul class="text-soft mb-0" style="font-size:.875rem;line-height:1.8;">
                        <li>O arquivo deve ser <strong style="color:#e2e8f0;">CSV separado por ponto e vírgula (;)</strong></li>
                        <li>Codificação: <strong style="color:#e2e8f0;">UTF-8</strong></li>
                        <li>A primeira linha deve conter os <strong style="color:#e2e8f0;">cabeçalhos</strong></li>
                        <li>Campos obrigatórios: <span class="badge bg-danger">nome</span> e <span class="badge bg-danger">preco_venda</span></li>
                        <li>Categorias inexistentes são <strong style="color:#e2e8f0;">criadas automaticamente</strong></li>
                        <li>SKUs duplicados são <strong style="color:#e2e8f0;">ignorados com erro</strong> — o restante continua</li>
                        <li>Decimais: use vírgula <strong style="color:#e2e8f0;">1.234,56</strong> ou ponto <strong style="color:#e2e8f0;">1234.56</strong></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-5">
                <div class="p-3 rounded h-100 d-flex flex-column justify-content-between" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);">
                    <div>
                        <h6 class="mb-2" style="color:#a5b4fc;"><i class="bi bi-table me-2"></i>Colunas do template</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(['nome','sku','categoria','fornecedor','preco_venda','custo','quantidade','estoque_minimo','unidade','descricao','ativo'] as $col)
                                <span class="badge" style="background:rgba(99,102,241,.2);color:#c7d2fe;font-size:.75rem;">{{ $col }}</span>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('products.import.template') }}" class="btn btn-sm mt-3"
                       style="background:rgba(99,102,241,.2);color:#a5b4fc;border:1px solid rgba(99,102,241,.3);">
                        <i class="bi bi-download me-1"></i>Baixar template CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Upload --}}
        <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="form-label text-soft">Arquivo CSV</label>
                <input type="file" name="csv_file" accept=".csv,text/csv" class="form-control bg-dark text-white border-secondary" required>
                <small class="text-soft">Tamanho máximo: 5 MB</small>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cloud-upload me-2"></i>Enviar e Importar
            </button>
        </form>

        {{-- Histórico --}}
        @if ($imports->count() > 0)
        <hr class="border-secondary my-4">
        <h6 class="text-white mb-3"><i class="bi bi-clock-history me-2"></i>Histórico de Importações</h6>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Arquivo</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Importados</th>
                        <th>Erros</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($imports as $import)
                    <tr>
                        <td class="text-soft" style="font-size:.85rem;">{{ $import->filename }}</td>
                        <td><span class="badge bg-{{ $import->status_badge }}">{{ $import->status_label }}</span></td>
                        <td>{{ $import->total_rows }}</td>
                        <td class="text-success">{{ $import->imported_rows }}</td>
                        <td class="{{ $import->failed_rows > 0 ? 'text-danger' : 'text-soft' }}">{{ $import->failed_rows }}</td>
                        <td class="text-soft" style="font-size:.82rem;">{{ $import->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($import->errors)
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#errorsModal{{ $import->id }}">
                                    <i class="bi bi-exclamation-triangle"></i> Ver erros
                                </button>
                                {{-- Modal de erros --}}
                                <div class="modal fade" id="errorsModal{{ $import->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="background:#0f172a;border:1px solid rgba(148,163,184,.15);">
                                            <div class="modal-header border-secondary">
                                                <h6 class="modal-title text-white">Erros — {{ $import->filename }}</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="max-height:400px;overflow-y:auto;">
                                                <table class="table table-dark table-sm">
                                                    <thead><tr><th style="width:80px;">Linha</th><th>Erro</th></tr></thead>
                                                    <tbody>
                                                        @foreach ($import->errors as $err)
                                                            <tr>
                                                                <td><span class="badge bg-danger">{{ $err['linha'] }}</span></td>
                                                                <td class="text-soft" style="font-size:.85rem;">{{ $err['erro'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>
@endsection
