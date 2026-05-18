<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Venda #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #222; background: #fff; }
        .page { width: 100%; padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #1a56db; padding-bottom: 16px; margin-bottom: 20px; }
        .company-name { font-size: 16px; font-weight: bold; color: #1a56db; }
        .company-info { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.5; }
        .invoice-title { font-size: 20px; font-weight: bold; color: #1a56db; text-align: right; }
        .invoice-meta { text-align: right; font-size: 10px; color: #555; margin-top: 4px; line-height: 1.6; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .section-title { font-size: 9px; text-transform: uppercase; color: #888; font-weight: bold; letter-spacing: 1px; margin-bottom: 6px; }
        .customer-block { margin-bottom: 20px; }
        .customer-name { font-size: 13px; font-weight: bold; }
        .customer-info { font-size: 10px; color: #555; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #eff6ff; color: #1e40af; font-size: 10px; padding: 8px 10px; border-bottom: 1px solid #bfdbfe; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        tfoot td { padding: 8px 10px; background: #eff6ff; font-weight: bold; font-size: 12px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-size: 14px; color: #1a56db; }
        .notes-block { background: #f9fafb; border-left: 3px solid #d1d5db; padding: 10px 14px; margin-bottom: 20px; font-size: 10px; color: #555; }
        .footer { text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #e5e7eb; padding-top: 12px; margin-top: 10px; }
        .row { display: flex; gap: 30px; margin-bottom: 20px; }
        .col { flex: 1; }
    </style>
</head>
<body>
<div class="page">

    {{-- Cabeçalho --}}
    <div class="header">
        <div>
            <div class="company-name">{{ $company->name ?? 'Invexa' }}</div>
            <div class="company-info">
                @if(!empty($company->cnpj)) CNPJ: {{ $company->cnpj }}<br>@endif
                @if(!empty($company->address)) {{ $company->address }}<br>@endif
                @if(!empty($company->phone)) Tel: {{ $company->phone }}<br>@endif
                @if(!empty($company->email)) {{ $company->email }}@endif
            </div>
        </div>
        <div>
            <div class="invoice-title">NOTA DE VENDA</div>
            <div class="invoice-meta">
                Nº {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>
                Data: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}<br>
                Emitida: {{ now()->format('d/m/Y H:i') }}<br>
                @php
                    $statusLabels = ['concluida' => ['Concluída','success'], 'pendente' => ['Pendente','warning'], 'cancelada' => ['Cancelada','danger']];
                    [$label, $cls] = $statusLabels[$sale->status] ?? ['—','secondary'];
                @endphp
                <span class="badge badge-{{ $cls }}">{{ $label }}</span>
            </div>
        </div>
    </div>

    {{-- Cliente --}}
    <div class="customer-block">
        <div class="section-title">Cliente</div>
        <div class="customer-name">{{ $sale->customer->name ?? $sale->customer_name }}</div>
        <div class="customer-info">
            @if($sale->customer?->cpf_cnpj) CPF/CNPJ: {{ $sale->customer->cpf_cnpj }}<br>@endif
            @if($sale->customer?->email) {{ $sale->customer->email }}<br>@endif
            @if($sale->customer?->phone) Tel: {{ $sale->customer->phone }}<br>@endif
            @if($sale->customer?->address) {{ $sale->customer->address }}@endif
        </div>
    </div>

    @if($sale->notes)
    <div class="notes-block">
        <strong>Observações:</strong> {{ $sale->notes }}
    </div>
    @endif

    {{-- Itens --}}
    <div class="section-title">Itens da venda</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produto</th>
                <th class="text-center">Qtd</th>
                <th class="text-right">Preço Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product->name ?? '—' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                <td class="text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Total Geral</td>
                <td class="text-right total-row">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Documento gerado eletronicamente pelo sistema Invexa &mdash; {{ now()->format('d/m/Y \à\s H:i') }}
    </div>
</div>
</body>
</html>
