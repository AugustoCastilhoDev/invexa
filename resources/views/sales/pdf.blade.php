<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nota de Venda #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* ── Reset ── */
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans', Arial, sans-serif; font-size:11px; color:#222; background:#fff; }

        /* ── Página — margens generosas para o DomPDF não cortar ── */
        .page { width:100%; padding:28px 32px; }

        /* ── Cabeçalho via tabela (DomPDF não suporta flexbox/grid) ── */
        .header-table { width:100%; border-collapse:collapse; border-bottom:2px solid #1a56db;
            padding-bottom:14px; margin-bottom:20px; }
        .header-table td { vertical-align:top; padding-bottom:14px; }
        .header-table td.right { text-align:right; width:42%; }
        .company-name  { font-size:15px; font-weight:bold; color:#1a56db; }
        .company-info  { font-size:10px; color:#555; margin-top:4px; line-height:1.55; }
        .invoice-title { font-size:19px; font-weight:bold; color:#1a56db; }
        .invoice-meta  { font-size:10px; color:#555; margin-top:4px; line-height:1.6; }

        /* ── Badges ── */
        .badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:9px; font-weight:bold; }
        .badge-success { background:#d1fae5; color:#065f46; }
        .badge-warning { background:#fef3c7; color:#92400e; }
        .badge-danger  { background:#fee2e2; color:#991b1b; }

        /* ── Seções ── */
        .section-title { font-size:9px; text-transform:uppercase; color:#888;
            font-weight:bold; letter-spacing:1px; margin-bottom:6px; }
        .customer-block { margin-bottom:20px; }
        .customer-name  { font-size:13px; font-weight:bold; }
        .customer-info  { font-size:10px; color:#555; line-height:1.6; }

        /* ── Tabela de itens ── */
        table { width:100%; border-collapse:collapse; margin-bottom:20px; table-layout:fixed; }
        thead th { background:#eff6ff; color:#1e40af; font-size:10px;
            padding:8px 8px; border-bottom:1px solid #bfdbfe; white-space:nowrap; }
        tbody td { padding:7px 8px; border-bottom:1px solid #f0f0f0; font-size:10px;
            word-break:break-word; overflow-wrap:break-word; }
        tfoot td { padding:8px 8px; background:#eff6ff; font-weight:bold; font-size:12px; }

        /* Larguras fixas para evitar overflow do lado direito */
        col.col-num     { width:5%; }
        col.col-produto { width:48%; }
        col.col-qtd     { width:10%; }
        col.col-unit    { width:18%; }
        col.col-sub     { width:19%; }

        .text-right  { text-align:right; }
        .text-center { text-align:center; }
        .total-row   { font-size:13px; color:#1a56db; }

        /* ── Notas ── */
        .notes-block { background:#f9fafb; border-left:3px solid #d1d5db;
            padding:10px 14px; margin-bottom:20px; font-size:10px; color:#555; }

        /* ── Rodapé ── */
        .footer { text-align:center; font-size:9px; color:#aaa;
            border-top:1px solid #e5e7eb; padding-top:12px; margin-top:10px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Cabeçalho (tabela para DomPDF) --}}
    <table class="header-table">
        <tr>
            <td>
                <div class="company-name">{{ $company->name ?? 'Invexa' }}</div>
                <div class="company-info">
                    @if(!empty($company->cnpj)) CNPJ: {{ $company->cnpj }}<br>@endif
                    @if(!empty($company->address)) {{ $company->address }}<br>@endif
                    @if(!empty($company->phone)) Tel: {{ $company->phone }}<br>@endif
                    @if(!empty($company->email)) {{ $company->email }}@endif
                </div>
            </td>
            <td class="right">
                <div class="invoice-title">NOTA DE VENDA</div>
                <div class="invoice-meta">
                    N&ordm; {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>
                    Data: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}<br>
                    Emitida: {{ now()->format('d/m/Y H:i') }}<br>
                    @php
                        $statusLabels = [
                            'concluida' => ['Concluída', 'success'],
                            'pendente'  => ['Pendente',  'warning'],
                            'cancelada' => ['Cancelada', 'danger'],
                        ];
                        [$label, $cls] = $statusLabels[$sale->status] ?? ['—', 'secondary'];
                    @endphp
                    <span class="badge badge-{{ $cls }}">{{ $label }}</span>
                </div>
            </td>
        </tr>
    </table>

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
        <strong>Observa&ccedil;&otilde;es:</strong> {{ $sale->notes }}
    </div>
    @endif

    {{-- Itens --}}
    <div class="section-title">Itens da Venda</div>
    <table>
        <colgroup>
            <col class="col-num">
            <col class="col-produto">
            <col class="col-qtd">
            <col class="col-unit">
            <col class="col-sub">
        </colgroup>
        <thead>
            <tr>
                <th>#</th>
                <th>Produto</th>
                <th class="text-center">Qtd</th>
                <th class="text-right">Pre&ccedil;o Unit.</th>
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
        Documento gerado eletronicamente pelo sistema Invexa &mdash; {{ now()->format('d/m/Y \\às H:i') }}
    </div>
</div>
</body>
</html>
