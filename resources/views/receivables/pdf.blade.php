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
    .header .doc-type { font-size:18px; font-weight:700; color:#38BDF8; text-align:right; }
    .header .doc-id { font-size:13px; color:rgba(255,255,255,.8); margin-top:2px; text-align:right; }

    .status-bar { padding:10px 32px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; text-align:center; }
    .status-pendente  { background:#fef3c7; color:#92400e; }
    .status-recebida  { background:#dcfce7; color:#166534; }
    .status-vencida   { background:#fee2e2; color:#991b1b; }
    .status-cancelada { background:#e5e7eb; color:#374151; }

    .info-block { padding:20px 32px; display:flex; gap:32px; border-bottom:2px solid #e5e7eb; }
    .info-col { flex:1; }
    .info-col h3 { font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:.08em; margin-bottom:6px; border-bottom:1px solid #e5e7eb; padding-bottom:4px; }
    .info-col p { font-size:11px; color:#111827; line-height:1.8; margin:0; }
    .info-col p strong { color:#111827; }
    .info-col p span { color:#4b5563; }

    .charge-box { margin:24px 32px; border:2px solid #0EA5E9; border-radius:6px; overflow:hidden; }
    .charge-box-header { background:#0EA5E9; color:#fff; padding:10px 16px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
    .charge-box-body { padding:16px; }
    .charge-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:12px; }
    .charge-row:last-child { border-bottom:none; }
    .charge-row .label { color:#6b7280; }
    .charge-row .value { font-weight:600; color:#111827; }
    .charge-total { display:flex; justify-content:space-between; padding:12px 16px; background:#f0f9ff; border-top:2px solid #0EA5E9; }
    .charge-total .label { font-size:13px; font-weight:700; color:#0369a1; text-transform:uppercase; }
    .charge-total .value { font-size:18px; font-weight:700; color:#0369a1; }

    .due-highlight { margin:0 32px 24px; padding:12px 16px; background:#fef3c7; border-left:4px solid #f59e0b; border-radius:0 4px 4px 0; }
    .due-highlight p { font-size:11px; color:#92400e; margin:0; }
    .due-highlight strong { font-size:13px; }

    .due-received { margin:0 32px 24px; padding:12px 16px; background:#dcfce7; border-left:4px solid #22c55e; border-radius:0 4px 4px 0; }
    .due-received p { font-size:11px; color:#166534; margin:0; }

    .installments-table { margin:0 32px 24px; }
    .installments-table h3 { font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:.08em; margin-bottom:8px; }
    table.inst { width:100%; border-collapse:collapse; }
    table.inst thead tr { background:#0EA5E9; color:#fff; }
    table.inst thead th { padding:7px 10px; font-size:10px; text-align:left; }
    table.inst thead th.r { text-align:right; }
    table.inst tbody tr { border-bottom:1px solid #f3f4f6; }
    table.inst tbody tr:nth-child(even) { background:#f9fafb; }
    table.inst tbody td { padding:6px 10px; font-size:11px; }
    table.inst tbody td.r { text-align:right; }
    .st-p { color:#92400e; font-weight:600; }
    .st-r { color:#166534; font-weight:600; }
    .st-v { color:#991b1b; font-weight:600; }
    .st-c { color:#374151; }

    .notes-block { margin:0 32px 20px; font-size:10px; color:#4b5563; }
    .notes-block strong { color:#1a1a2e; font-size:10px; }

    .footer { margin-top:32px; padding:14px 32px; border-top:1px solid #e5e7eb; font-size:9px; color:#9ca3af; text-align:center; }

    .company-info { margin:16px 32px 0; padding:12px 16px; background:#f9fafb; border-radius:4px; border:1px solid #e5e7eb; }
    .company-info p { font-size:10px; color:#4b5563; margin:0; line-height:1.8; }
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
                <h1>{{ strtoupper(substr($company->name, 0, 6)) }}</h1>
            @endif
            <div class="sub">{{ $company->name }}</div>
        </div>
        <div>
            <div class="doc-type">COBRANÇA</div>
            <div class="doc-id">#{{ str_pad($receivable->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
</div>

{{-- Faixa de status --}}
<div class="status-bar status-{{ $receivable->status }}">
    {{ $receivable->status_label }}
    @if($receivable->status === 'vencida') &bull; VENCIDA EM {{ $receivable->due_date->format('d/m/Y') }} @endif
    @if($receivable->status === 'recebida') &bull; RECEBIDA EM {{ $receivable->received_at?->format('d/m/Y') }} @endif
</div>

{{-- Bloco de informações: Emissor × Cliente --}}
<div class="info-block">
    <div class="info-col">
        <h3>Emitido por</h3>
        <p><strong>{{ $company->name }}</strong></p>
        @if($company->document)<p><span>CNPJ/CPF:</span> {{ $company->document }}</p>@endif
        @if($company->email)<p><span>E-mail:</span> {{ $company->email }}</p>@endif
        @if($company->phone)<p><span>Telefone:</span> {{ $company->phone }}</p>@endif
        @if($company->address)<p><span>End.:</span> {{ $company->address }}{{ $company->city ? ', '.$company->city : '' }}</p>@endif
    </div>
    <div class="info-col">
        <h3>Destinatário</h3>
        @if($receivable->customer)
            <p><strong>{{ $receivable->customer->name }}</strong></p>
            @if($receivable->customer->document)<p><span>CPF/CNPJ:</span> {{ $receivable->customer->document }}</p>@endif
            @if($receivable->customer->email)<p><span>E-mail:</span> {{ $receivable->customer->email }}</p>@endif
            @if($receivable->customer->phone)<p><span>Telefone:</span> {{ $receivable->customer->phone }}</p>@endif
        @else
            <p><strong>Consumidor Final</strong></p>
        @endif
    </div>
    <div class="info-col">
        <h3>Dados da Cobrança</h3>
        <p><strong>Emissão:</strong> {{ now()->format('d/m/Y') }}</p>
        <p><strong>Vencimento:</strong> {{ $receivable->due_date->format('d/m/Y') }}</p>
        <p><strong>Categoria:</strong> {{ $receivable->category_label }}</p>
        @if($receivable->installment_number && $receivable->installments)
            <p><strong>Parcela:</strong> {{ $receivable->installment_number }}/{{ $receivable->installments }}</p>
        @endif
        @if($receivable->recurrence && $receivable->installment_number)
            <p><strong>Recorrência:</strong> {{ $receivable->installment_number }}/{{ $receivable->recurrence }}</p>
        @endif
    </div>
</div>

{{-- Caixa de cobrança --}}
<div class="charge-box">
    <div class="charge-box-header">Detalhes da Cobrança</div>
    <div class="charge-box-body">
        <div class="charge-row">
            <span class="label">Descrição</span>
            <span class="value">{{ $receivable->description }}</span>
        </div>
        @if($receivable->payment_method)
        <div class="charge-row">
            <span class="label">Forma de Pagamento</span>
            <span class="value">{{ $receivable->payment_method_label }}</span>
        </div>
        @endif
        @if($receivable->amount_received > 0 && $receivable->amount_received < $receivable->amount)
        <div class="charge-row">
            <span class="label">Valor Original</span>
            <span class="value">R$ {{ number_format($receivable->amount, 2, ',', '.') }}</span>
        </div>
        <div class="charge-row">
            <span class="label">Já Recebido</span>
            <span class="value" style="color:#166534;">R$ {{ number_format($receivable->amount_received, 2, ',', '.') }}</span>
        </div>
        @endif
    </div>
    <div class="charge-total">
        <span class="label">{{ $receivable->status === 'recebida' ? 'TOTAL RECEBIDO' : 'VALOR A RECEBER' }}</span>
        <span class="value">R$ {{ number_format($receivable->status === 'recebida' ? $receivable->amount_received : $receivable->amount, 2, ',', '.') }}</span>
    </div>
</div>

{{-- Destaque de vencimento --}}
@if($receivable->status === 'pendente' || $receivable->status === 'vencida')
<div class="due-highlight">
    <p>
        @if($receivable->status === 'vencida')
            ⚠ <strong>Esta cobrança está vencida desde {{ $receivable->due_date->format('d/m/Y') }}.</strong>
            Regularize o quanto antes para evitar inconvenientes.
        @else
            <strong>Vencimento: {{ $receivable->due_date->format('d/m/Y') }}</strong>
            &nbsp;&bull;&nbsp; Realize o pagamento até a data de vencimento.
        @endif
    </p>
</div>
@elseif($receivable->status === 'recebida')
<div class="due-received">
    <p>✓ <strong>Pagamento confirmado em {{ $receivable->received_at?->format('d/m/Y') }}</strong>
    @if($receivable->payment_method) via {{ $receivable->payment_method_label }}@endif</p>
</div>
@endif

{{-- Tabela de parcelas (se for pai agrupador ou parcelado com filhos) --}}
@if($installments->isNotEmpty())
<div class="installments-table">
    <h3>Parcelas / Recorrências</h3>
    <table class="inst">
        <thead>
            <tr>
                <th>#</th>
                <th>Descrição</th>
                <th>Vencimento</th>
                <th class="r">Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($installments as $inst)
            <tr>
                <td>{{ $inst->installment_number }}</td>
                <td>{{ $inst->description }}</td>
                <td>{{ $inst->due_date->format('d/m/Y') }}</td>
                <td class="r">R$ {{ number_format($inst->amount, 2, ',', '.') }}</td>
                <td class="st-{{ $inst->status }}">{{ $inst->status_label }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Observações --}}
@if($receivable->notes)
<div class="notes-block">
    <strong>Observações:</strong> {{ $receivable->notes }}
</div>
@endif

{{-- Dados bancários (se existir campo no Company) --}}
@if(!empty($company->bank_info))
<div class="company-info">
    <p><strong>Dados para Pagamento:</strong> {{ $company->bank_info }}</p>
</div>
@endif

<div class="footer">
    Documento gerado pelo sistema Invexa &bull; {{ now()->format('d/m/Y H:i') }}
    &bull; {{ $company->name }}
</div>

</body>
</html>
