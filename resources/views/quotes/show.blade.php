@extends('layouts.app')

@section('title', 'Orçamento ' . $quote->number)

@section('content')
<div class="card dashboard-card card-dark-bg shadow-sm border-0">
    <div class="card-header card-header-dark border-bottom">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h4 class="mb-1 text-white"><i class="bi bi-file-earmark-text me-2"></i>{{ $quote->number }}</h4>
                <p class="text-soft mb-0">Criado em {{ $quote->created_at->format('d/m/Y \à\s H:i') }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
                <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                    <i class="bi bi-file-pdf me-1"></i> Baixar PDF
                </a>
                @if($quote->whatsapp_url !== '#')
                    <a href="{{ $quote->whatsapp_url }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="bi bi-whatsapp me-1"></i> Enviar WhatsApp
                    </a>
                @endif
                @if(!in_array($quote->status, ['converted','rejected']))
                    <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm"
                            onclick="return confirm('Converter este orçamento em venda?')">
                            <i class="bi bi-arrow-right-circle me-1"></i> Converter em Venda
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-4 mb-4">
            {{-- Info do orçamento --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg h-100">
                    <div class="card-header card-header-dark">
                        <span class="text-white fw-semibold"><i class="bi bi-info-circle me-2"></i>Informações</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-soft" style="font-size:.8rem;">STATUS</span>
                                <p class="mt-1">
                                    <span class="badge bg-{{ $quote->statusBadgeClass() }}">
                                        {{ $quote->statusLabel() }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-6">
                                <span class="text-soft" style="font-size:.8rem;">VALIDADE</span>
                                <p class="text-white mt-1">{{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : '—' }}</p>
                            </div>
                            <div class="col-12">
                                <span class="text-soft" style="font-size:.8rem;">CLIENTE</span>
                                <p class="text-white mt-1">{{ $quote->customer?->name ?? 'Consumidor Final' }}</p>
                            </div>
                            @if($quote->notes)
                            <div class="col-12">
                                <span class="text-soft" style="font-size:.8rem;">OBSERVAÇÕES</span>
                                <p class="text-soft mt-1">{{ $quote->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumo financeiro --}}
            <div class="col-12 col-md-6">
                <div class="card card-dark-bg h-100">
                    <div class="card-header card-header-dark">
                        <span class="text-white fw-semibold"><i class="bi bi-receipt me-2"></i>Resumo Financeiro</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between text-soft mb-2">
                            <span>Subtotal</span>
                            <span>R$ {{ number_format($quote->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-soft mb-3">
                            <span>Desconto</span>
                            <span class="text-warning">- R$ {{ number_format($quote->discount, 2, ',', '.') }}</span>
                        </div>
                        <hr style="border-color:rgba(148,163,184,.15);">
                        <div class="d-flex justify-content-between text-white fw-bold" style="font-size:1.1rem;">
                            <span>Total</span>
                            <span>R$ {{ number_format($quote->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Itens --}}
        <div class="card card-dark-bg">
            <div class="card-header card-header-dark">
                <span class="text-white fw-semibold"><i class="bi bi-list-ul me-2"></i>Itens do Orçamento</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-soft" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">
                                <th>Produto / Descrição</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr>
                                <td>
                                    <p class="mb-0 text-white fw-semibold">{{ $item->description }}</p>
                                    @if($item->product)
                                        <small class="text-soft">{{ $item->product->name }}</small>
                                    @endif
                                </td>
                                <td class="text-center text-soft">{{ $item->quantity }}</td>
                                <td class="text-end text-soft">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="text-end text-white fw-semibold">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Alterar status --}}
        @if($quote->status !== 'converted')
        <div class="mt-4">
            <div class="card card-dark-bg">
                <div class="card-header card-header-dark">
                    <span class="text-white fw-semibold"><i class="bi bi-arrow-repeat me-2"></i>Alterar Status</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('quotes.status', $quote) }}" method="POST" class="d-flex gap-2 flex-wrap align-items-center">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select" style="max-width:220px;">
                            <option value="draft"    @selected($quote->status === 'draft')>Rascunho</option>
                            <option value="sent"     @selected($quote->status === 'sent')>Enviado</option>
                            <option value="accepted" @selected($quote->status === 'accepted')>Aceito</option>
                            <option value="rejected" @selected($quote->status === 'rejected')>Recusado</option>
                            <option value="expired"  @selected($quote->status === 'expired')>Expirado</option>
                        </select>
                        <button type="submit" class="btn btn-outline-light btn-sm">Salvar Status</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
