@extends('layouts.app')

@section('title', 'NF-e ' . $nfe->numero_formatado)

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h4 class="mb-1 text-white">
                <i class="bi bi-file-earmark-text me-2"></i>NF-e {{ $nfe->numero_formatado }}
                <span class="badge bg-{{ $nfe->status_badge }} ms-2">{{ $nfe->status_label }}</span>
            </h4>
            <p class="text-soft mb-0">Série {{ $nfe->serie }} · Emitida em {{ $nfe->data_emissao?->format('d/m/Y H:i') ?? '—' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('nfes.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>

            @if($nfe->status === 'pendente')
                <form action="{{ route('nfes.consultar', $nfe) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-info">
                        <i class="bi bi-arrow-clockwise me-1"></i>Consultar SEFAZ
                    </button>
                </form>
            @endif

            @if($nfe->status === 'autorizada')
                <a href="{{ route('nfes.xml', $nfe) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-file-code me-1"></i>XML
                </a>
                <a href="{{ route('nfes.danfe', $nfe) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-file-pdf me-1"></i>DANFE
                </a>
            @endif
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

        <div class="row g-4">

            {{-- Coluna principal --}}
            <div class="col-12 col-lg-8">

                {{-- Chave de Acesso --}}
                @if($nfe->chave_acesso)
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-soft text-uppercase fw-bold mb-3" style="font-size:.7rem;letter-spacing:.08em;">Chave de Acesso</h6>
                        <p class="font-monospace text-white mb-0" style="font-size:.8rem;word-break:break-all;">
                            {{ $nfe->chave_acesso }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Dados da Venda associada --}}
                @if($nfe->sale)
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-soft text-uppercase fw-bold mb-3" style="font-size:.7rem;letter-spacing:.08em;">Venda Associada</h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="text-soft small mb-1">Número</div>
                                <div class="text-white fw-semibold">
                                    <a href="{{ route('sales.show', $nfe->sale_id) }}" class="text-info">
                                        #{{ $nfe->sale->sale_number }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-soft small mb-1">Cliente</div>
                                <div class="text-white fw-semibold">{{ $nfe->sale->customer_name ?? '—' }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-soft small mb-1">Total da Venda</div>
                                <div class="text-white fw-semibold">R$ {{ number_format($nfe->sale->total, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-soft small mb-1">Status da Venda</div>
                                @php
                                    $sBadge = match ($nfe->sale->status) {
                                        'concluida' => 'success',
                                        'pendente'  => 'warning',
                                        'cancelada' => 'danger',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $sBadge }}">{{ ucfirst($nfe->sale->status) }}</span>
                            </div>
                        </div>

                        {{-- Itens da venda --}}
                        @if($nfe->sale->items && $nfe->sale->items->count())
                        <hr class="border-secondary mt-3 mb-3">
                        <h6 class="text-soft text-uppercase fw-bold mb-3" style="font-size:.7rem;letter-spacing:.08em;">Itens</h6>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm mb-0">
                                <thead>
                                    <tr style="font-size:.7rem;color:rgba(148,163,184,.8);">
                                        <th>Produto</th>
                                        <th class="text-end">Qtd</th>
                                        <th class="text-end">Unit.</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nfe->sale->items as $item)
                                    <tr>
                                        <td class="text-white">{{ $item->product->name ?? 'Produto removido' }}</td>
                                        <td class="text-end text-soft">{{ $item->quantity }}</td>
                                        <td class="text-end text-soft">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-end text-white fw-semibold">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Resposta Focus NFe --}}
                @if($nfe->resposta_sefaz)
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-soft text-uppercase fw-bold mb-3" style="font-size:.7rem;letter-spacing:.08em;">Resposta SEFAZ / Focus NFe</h6>
                        <pre class="text-white mb-0" style="font-size:.75rem;max-height:200px;overflow-y:auto;background:rgba(0,0,0,.3);padding:1rem;border-radius:.5rem;">{{ json_encode($nfe->resposta_sefaz, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
                @endif

            </div>

            {{-- Coluna lateral --}}
            <div class="col-12 col-lg-4">

                {{-- Dados da NF-e --}}
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-soft text-uppercase fw-bold mb-3" style="font-size:.7rem;letter-spacing:.08em;">Dados da NF-e</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Número</span>
                                <span class="text-white fw-semibold">{{ str_pad($nfe->numero, 9, '0', STR_PAD_LEFT) }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Série</span>
                                <span class="text-white fw-semibold">{{ $nfe->serie }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Natureza de Op.</span>
                                <span class="text-white fw-semibold">{{ $nfe->natureza_operacao }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Emissão</span>
                                <span class="text-white fw-semibold">{{ $nfe->data_emissao?->format('d/m/Y H:i') ?? '—' }}</span>
                            </li>
                            @if($nfe->protocolo)
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Protocolo</span>
                                <span class="text-white fw-semibold font-monospace" style="font-size:.8rem;">{{ $nfe->protocolo }}</span>
                            </li>
                            @endif
                            @if($nfe->data_autorizacao)
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Autorização</span>
                                <span class="text-white fw-semibold">{{ $nfe->data_autorizacao->format('d/m/Y H:i') }}</span>
                            </li>
                            @endif
                            @if($nfe->data_cancelamento)
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary">
                                <span class="text-soft small">Cancelamento</span>
                                <span class="text-danger fw-semibold">{{ $nfe->data_cancelamento->format('d/m/Y H:i') }}</span>
                            </li>
                            @endif
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-soft small">Emitido por</span>
                                <span class="text-white fw-semibold">{{ $nfe->user?->name ?? '—' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Ações --}}
                @if($nfe->status === 'autorizada')
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-soft text-uppercase fw-bold mb-3" style="font-size:.7rem;letter-spacing:.08em;">Ações</h6>

                        {{-- Carta de Correção --}}
                        <form action="{{ route('nfes.carta-correcao', $nfe) }}" method="POST" class="mb-3">
                            @csrf
                            <label for="correcao" class="form-label text-white small">Carta de Correção (CC-e)</label>
                            <textarea id="correcao" name="correcao" rows="3"
                                      class="form-control bg-dark text-white border-secondary mb-2"
                                      placeholder="Texto da correção (mín. 15 caracteres)" required minlength="15"></textarea>
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="bi bi-pencil-square me-1"></i>Emitir CC-e
                            </button>
                        </form>

                        {{-- Cancelar --}}
                        <form action="{{ route('nfes.cancelar', $nfe) }}" method="POST"
                              onsubmit="return confirm('Tem certeza que deseja cancelar esta NF-e?')">
                            @csrf
                            <input type="hidden" name="justificativa" value="Cancelamento solicitado pelo emitente">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1"></i>Cancelar NF-e
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Mensagem de erro (se houver) --}}
                @if($nfe->mensagem_erro)
                <div class="card border-0 shadow-sm" style="background:rgba(220,38,38,.1);border:1px solid rgba(220,38,38,.3) !important;">
                    <div class="card-body">
                        <h6 class="text-danger text-uppercase fw-bold mb-2" style="font-size:.7rem;letter-spacing:.08em;">
                            <i class="bi bi-exclamation-triangle me-1"></i>Mensagem de Erro
                        </h6>
                        <p class="text-white mb-0 small">{{ $nfe->mensagem_erro }}</p>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
