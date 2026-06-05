@extends('layouts.app')

@section('title', 'Fornecedor')

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 text-white">{{ $supplier->name }}</h4>
                @if($supplier->trade_name)
                    <p class="text-soft mb-0">{{ $supplier->trade_name }}</p>
                @endif
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(auth()->user()->hasRole(['admin','gerente']))
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                          onsubmit="return confirm('Excluir este fornecedor?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                    </form>
                @endif
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
            </div>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Status badge --}}
        <div class="mb-4">
            @if($supplier->active)
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;
                             font-weight:600;padding:.35rem .8rem;border-radius:999px;
                             background:rgba(34,197,94,.12);color:#4ade80;
                             border:1px solid rgba(34,197,94,.25);">
                    <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;"></span>Fornecedor Ativo
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;
                             font-weight:600;padding:.35rem .8rem;border-radius:999px;
                             background:rgba(148,163,184,.1);color:#94a3b8;
                             border:1px solid rgba(148,163,184,.2);">
                    <span style="width:7px;height:7px;border-radius:50%;background:#94a3b8;"></span>Inativo
                </span>
            @endif
        </div>

        <div class="row g-4">

            {{-- Dados da empresa --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-header card-header-dark py-2">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-building me-1"></i>Dados da Empresa
                        </span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0" style="font-size:.875rem;">
                            <dt class="col-5 text-soft fw-normal">Razão Social</dt>
                            <dd class="col-7 text-white fw-semibold mb-2">{{ $supplier->name }}</dd>

                            @if($supplier->trade_name)
                            <dt class="col-5 text-soft fw-normal">Nome Fantasia</dt>
                            <dd class="col-7 text-white mb-2">{{ $supplier->trade_name }}</dd>
                            @endif

                            <dt class="col-5 text-soft fw-normal">CNPJ / CPF</dt>
                            <dd class="col-7 text-white mb-2" style="font-family:monospace;">{{ $supplier->document_formatted }}</dd>

                            @if($supplier->contact_person)
                            <dt class="col-5 text-soft fw-normal">Contato</dt>
                            <dd class="col-7 text-white mb-2">{{ $supplier->contact_person }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Contato --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg border border-secondary h-100">
                    <div class="card-header card-header-dark py-2">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-telephone me-1"></i>Contato
                        </span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0" style="font-size:.875rem;">
                            <dt class="col-5 text-soft fw-normal">E-mail</dt>
                            <dd class="col-7 mb-2">
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-info">{{ $supplier->email }}</a>
                                @else
                                    <span class="text-soft">&mdash;</span>
                                @endif
                            </dd>

                            <dt class="col-5 text-soft fw-normal">Telefone</dt>
                            <dd class="col-7 text-white mb-2">{{ $supplier->phone ?? '&mdash;' }}</dd>

                            <dt class="col-5 text-soft fw-normal">Endereço</dt>
                            <dd class="col-7 text-white mb-2">{{ $supplier->full_address ?: '&mdash;' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Observações --}}
            @if($supplier->notes)
            <div class="col-12">
                <div class="card card-dark-bg border border-secondary">
                    <div class="card-header card-header-dark py-2">
                        <span class="text-soft text-uppercase fw-semibold" style="font-size:.72rem;letter-spacing:.08em;">
                            <i class="bi bi-chat-text me-1"></i>Observações
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="text-white mb-0" style="white-space:pre-line;">{{ $supplier->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="mt-3 text-soft" style="font-size:.75rem;">
            Cadastrado em {{ $supplier->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
            &bull; Atualizado em {{ $supplier->updated_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
        </div>
    </div>
</div>
@endsection
