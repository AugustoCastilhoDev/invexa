<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a2e; background:#fff; }
    .header { background:#080D1A; color:#fff; padding:24px 32px; display:flex; justify-content:space-between; align-items:center; }
    .header h1 { font-size:22px; color:#38BDF8; letter-spacing:.05em; }
    .header .sub { font-size:10px; color:rgba(255,255,255,.55); margin-top:3px; }
    .info-block { padding:20px 32px; display:flex; gap:32px; border-bottom:2px solid #e5e7eb; }
    .info-col { flex:1; }
    .info-col h3 { font-size:9px; text-transform:uppercase; color:#6b7280; letter-spacing:.08em; margin-bottom:6px; }
    .info-col p { font-size:11px; color:#111827; line-height:1.6; }
    table { width:100%; border-collapse:collapse; margin:0 32px; width:calc(100% - 64px); }
    thead tr { background:#0EA5E9; color:#fff; }
    thead th { padding:8px 10px; font-size:10px; text-transform:uppercase; letter-spacing:.06em; text-align:left; }
    thead th.r { text-align:right; }
    tbody tr { border-bottom:1px solid #f3f4f6; }
    tbody tr:nth-child(even) { background:#f9fafb; }
    tbody td { padding:7px 10px; font-size:11px; }
    tbody td.r { text-align:right; }
    .totals { margin:16px 32px 0; }
    .totals table { width:260px; margin-left:auto; margin-right:0; }
    .totals td { padding:4px 6px; font-size:11px; }
    .totals .grand { font-weight:700; font-size:14px; color:#0EA5E9; border-top:2px solid #e5e7eb; padding-top:6px; }
    .notes { margin:20px 32px 0; font-size:10px; color:#4b5563; }
    .notes strong { font-size:10px; color:#1a1a2e; }
    .footer { margin-top:40px; padding:14px 32px; border-top:1px solid #e5e7eb;
              font-size:9px; color:#9ca3af; text-align:center; }
    .badge { display:inline-block; padding:2px 10px; border-radius:999px; font-size:10px;
             font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
    .badge-draft     { background:#e5e7eb; color:#374151; }
    .badge-sent      { background:#dbeafe; color:#1d4ed8; }
    .badge-accepted  { background:#dcfce7; color:#166534; }
    .badge-rejected  { background:#fee2e2; color:#991b1b; }
    .badge-expired   { background:#fef3c7; color:#92400e; }
    .badge-converted { background:#ede9fe; color:#5b21b6; }
</style>
</head>
<body>

<div class="header">
    <div>
        <h1>INVEXA</h1>
        <div class="sub">{{ $quote->company->name ?? 'Minha Empresa' }}</div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:18px;font-weight:700;color:#38BDF8;">ORÇAMENTO</div>
        <div style="font-size:13px;color:rgba(255,255,255,.8);margin-top:2px;">{{ $quote->number }}</div>
        <div style="margin-top:6px;">
            <span class="badge badge-{{ $quote->status }}">{{ $quote->statusLabel() }}</span>
        </div>
    </div>
</div>

<div class="info-block">
    <div class="info-col">
        <h3>Cliente</h3>
        <p><strong>{{ $quote->customer?->name ?? 'Consumidor Final' }}</strong></p>
        @if($quote->customer?->email)<p>{{ $quote->customer->email }}</p>@endif
        @if($quote->customer?->phone)<p>{{ $quote->customer->phone }}</p>@endif
        @if($quote->customer?->document)<p>CPF/CNPJ: {{ $quote->customer->document }}</p>@endif
    </div>
    <div class="info-col">
        <h3>Dados do Orçamento</h3>
        <p><strong>Emissão:</strong> {{ $quote->created_at->format('d/m/Y') }}</p>
        @if($quote->valid_until)
        <p><strong>Válido até:</strong> {{ $quote->valid_until->format('d/m/Y') }}</p>
        @endif
    </div>
</div>

<div style="margin-top:20px;">
<table>
    <thead>
        <tr>
            <th>Descrição</th>
            <th class="r" style="width:80px;">Qtd</th>
            <th class="r" style="width:100px;">Preço Unit.</th>
            <th class="r" style="width:100px;">Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach($quote->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="r">{{ number_format($item->quantity, 2, ',', '.') }}</td>
            <td class="r">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
            <td class="r">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>

<div class="totals">
    <table>
        <tr>
            <td style="color:#6b7280;">Subtotal</td>
            <td class="r">R$ {{ number_format($quote->subtotal, 2, ',', '.') }}</td>
        </tr>
        @if($quote->discount > 0)
        <tr>
            <td style="color:#dc2626;">Desconto</td>
            <td class="r" style="color:#dc2626;">- R$ {{ number_format($quote->discount, 2, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand">
            <td>TOTAL</td>
            <td class="r">R$ {{ number_format($quote->total, 2, ',', '.') }}</td>
        </tr>
    </table>
</div>

@if($quote->notes)
<div class="notes">
    <strong>Observações:</strong> {{ $quote->notes }}
</div>
@endif

<div class="footer">
    Documento gerado pelo sistema Invexa &bull; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
