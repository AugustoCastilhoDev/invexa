<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a2e; background:#fff; }

    .header { background:#080D1A; color:#fff; padding:24px 32px; }
    .header-inner { display:flex; justify-content:space-between; align-items:center; }
    .header h1 { font-size:22px; color:#38BDF8; letter-spacing:.05em; }
    .header .sub { font-size:10px; color:rgba(255,255,255,.55); margin-top:3px; }
    .header .doc-type { font-size:18px; font-weight:700; color:#f87171; text-align:right; }
    .header .doc-id { font-size:13px; color:rgba(255,255,255,.8); margin-top:2px; text-align:right; }

    .status-bar { padding:10px 32px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; text-align:center; }
    .status-pendente  { background:#fef3c7; color:#92400e; }
    .status-paga      { background:#dcfce7; color:#166534; }
    .status-vencida   { background:#fee2e2; color:#991b1b; }
    .status-cancelada { background:#e5e7eb; color:#374151; }

    .info-block { padding:20px 32px; display:flex; gap:32px; border-bottom:2px solid #e5e7eb; }
    .info-col { flex:1; }
    .info-col h3 { font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:.08em; margin-bottom:6px; border-bottom:1px solid #e5e7eb; padding-bottom:4px; }
    .info-col p { font-size:11px; color:#111827; line-height:1.8; margin:0; }
    .info-col p strong { color:#111827; }
    .info-col p span { color:#4b5563; }

    .charge-box { margin:24px 32px; border:2px solid #f87171; border-radius:6px; overflow:hidden; }
    .charge-box-header { background:#ef4444; color:#fff; padding:10px 16px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
    .charge-box-body { padding:16px; }
    .charge-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:12px; }
    .charge-row:last-child { border-bottom:none; }
    .charge-row .label { color:#6b7280; }
    .charge-row .value { font-weight:600; color:#111827; }
    .charge-total { display:flex; justify-content:space-between; padding:12px 16px; background:#fff5f5; border-top:2px solid #f87171; }
    .charge-total .label { font-size:13px; font-weight:700; color:#b91c1c; text-transform:uppercase; }
    .charge-total .value { font-size:18px; font-weight:700; color:#b91c1c; }

    .due-highlight { margin:0 32px 24px; padding:12px 16px; background:#fef3c7; border-left:4px solid #f59e0b; border-radius:0 4px 4px 0; }
    .due-highlight p { font-size:11px; color:#92400e; margin:0; }
    .due-highlight strong { font-size:13px; }

    .due-paid { margin:0 32px 24px; padding:12px 16px; background:#dcfce7; border-left:4px solid #22c55e; border-radius:0 4px 4px 0; }
    .due-paid p { font-size:11px; color:#166534; margin:0; }

    .notes-block { margin:0 32px 20px; font-size:10px; color:#4b5563; }
    .notes-block strong { color:#1a1a2e; font-size:10px; }

    .footer { margin-top:32px; padding:14px 32px; border-top:1px solid #e5e7eb; font-size:9px; color:#9ca3af; text-align:center; }
</style>
</head>
<body>

{{-- Cabeçalho --}}
<div class="header">
    <div class="header-inner">
        <div>
            @if($company->logo_path)
                <img src="{{ public_path('storage/' . $company->logo_path) }}" style="height:40px;" alt="Logo">
            @else
                <h1>INVEXA</h1>
            @endif
            <div class="sub">{{ $company->name }}</div>
        </div>
        <div>
            <div class="doc-type">DESPESA</div>
            <div class="doc-id">#{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
</div>

{{-- Faixa de status --}}
<div class="status-bar status-{{ $bill->status }}">
    {{ $bill->status_label }}
    @if($bill->status === 'vencida') &bull; VENCIDA EM {{ $bill->due_date->format('d/m/Y') }} @endif
    @if($bill->status === 'paga') &bull; PAGA EM {{ \Carbon\Carbon::parse($bill->paid_at)->format('d/m/Y') }} @endif
</div>

{{-- Bloco de informações --}}
<div class="info-block">
    <div class="info-col">
        <h3>Empresa</h3>
        <p><strong>{{ $company->name }}</strong></p>
        @if($company->document)<p><span>CNPJ/CPF:</span> {{ $company->document }}</p>@endif
        @if($company->email)<p><span>E-mail:</span> {{ $company->email }}</p>@endif
        @if($company->phone)<p><span>Telefone:</span> {{ $company->phone }}</p>@endif
    </div>
    <div class="info-col">
        <h3>Fornecedor</h3>
        @if($bill->supplier)
            <p><strong>{{ $bill->supplier->name }}</strong></p>
            @if($bill->supplier->document)<p><span>CNPJ/CPF:</span> {{ $bill->supplier->document }}</p>@endif
            @if($bill->supplier->email)<p><span>E-mail:</span> {{ $bill->supplier->email }}</p>@endif
            @if($bill->supplier->phone)<p><span>Telefone:</span> {{ $bill->supplier->phone }}</p>@endif
        @else
            <p><strong>Não informado</strong></p>
        @endif
    </div>
    <div class="info-col">
        <h3>Dados da Despesa</h3>
        <p><strong>Emissão:</strong> {{ now()->format('d/m/Y') }}</p>
        <p><strong>Vencimento:</strong> {{ $bill->due_date->format('d/m/Y') }}</p>
        <p><strong>Categoria:</strong> {{ $bill->category_label }}</p>
        @if($bill->status === 'paga' && $bill->paid_at)
            <p><strong>Pago em:</strong> {{ \Carbon\Carbon::parse($bill->paid_at)->format('d/m/Y') }}</p>
        @endif
    </div>
</div>

{{-- Caixa de despesa --}}
<div class="charge-box">
    <div class="charge-box-header">Detalhes da Despesa</div>
    <div class="charge-box-body">
        <div class="charge-row">
            <span class="label">Descrição</span>
            <span class="value">{{ $bill->description }}</span>
        </div>
        @if(!empty($bill->payment_method))
        <div class="charge-row">
            <span class="label">Forma de Pagamento</span>
            <span class="value">{{ $bill->payment_method }}</span>
        </div>
        @endif
        @if($bill->status === 'paga' && $bill->amount_paid && abs($bill->amount_paid - $bill->amount) > 0.01)
        <div class="charge-row">
            <span class="label">Valor Original</span>
            <span class="value">R$ {{ number_format($bill->amount, 2, ',', '.') }}</span>
        </div>
        <div class="charge-row">
            <span class="label">Valor Pago</span>
            <span class="value" style="color:#166534;">R$ {{ number_format($bill->amount_paid, 2, ',', '.') }}</span>
        </div>
        @endif
    </div>
    <div class="charge-total">
        <span class="label">{{ $bill->status === 'paga' ? 'TOTAL PAGO' : 'VALOR A PAGAR' }}</span>
        <span class="value">R$ {{ number_format($bill->status === 'paga' ? ($bill->amount_paid ?? $bill->amount) : $bill->amount, 2, ',', '.') }}</span>
    </div>
</div>

{{-- Destaque de vencimento / pagamento --}}
@if($bill->status === 'pendente' || $bill->status === 'vencida')
<div class="due-highlight">
    <p>
        @if($bill->isOverdue())
            ⚠ <strong>Esta despesa está vencida desde {{ $bill->due_date->format('d/m/Y') }}.</strong>
            Regularize o quanto antes para evitar inconvenientes.
        @else
            <strong>Vencimento: {{ $bill->due_date->format('d/m/Y') }}</strong>
            &nbsp;&bull;&nbsp; Efetue o pagamento até a data de vencimento.
        @endif
    </p>
</div>
@elseif($bill->status === 'paga')
<div class="due-paid">
    <p>✓ <strong>Pagamento confirmado em {{ \Carbon\Carbon::parse($bill->paid_at)->format('d/m/Y') }}</strong>
    @if(!empty($bill->payment_method)) via {{ $bill->payment_method }}@endif</p>
</div>
@endif

{{-- Observações --}}
@if($bill->notes)
<div class="notes-block">
    <strong>Observações:</strong> {{ $bill->notes }}
</div>
@endif

<div class="footer">
    Documento gerado pelo sistema Invexa &bull; {{ now()->format('d/m/Y H:i') }}
    &bull; {{ $company->name }}
</div>

</body>
</html>
