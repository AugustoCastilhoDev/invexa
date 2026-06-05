<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nota de Venda #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* @page removido — margens controladas exclusivamente pelo DomPDF (SaleController) */
        @page { size: A4 portrait; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #ffffff;
        }

        .page { width: 100%; }

        /* ============================
           CABEÇALHO
        ============================ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #1a56db;
            margin-bottom: 18px;
        }
        .header-table td { vertical-align: top; padding-bottom: 12px; }
        .header-left  { width: 55%; }
        .header-right { width: 45%; text-align: right; }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #1a56db;
            word-break: break-word;
        }
        .company-info {
            font-size: 9.5px;
            color: #555;
            margin-top: 3px;
            line-height: 1.6;
            word-break: break-word;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a56db;
        }
        .invoice-meta {
            font-size: 9.5px;
            color: #555;
            margin-top: 3px;
            line-height: 1.65;
        }

        /* ============================
           BADGES
        ============================ */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .badge-success   { background: #d1fae5; color: #065f46; }
        .badge-warning   { background: #fef3c7; color: #92400e; }
        .badge-danger    { background: #fee2e2; color: #991b1b; }
        .badge-secondary { background: #f1f5f9; color: #475569; }

        /* ============================
           CLIENTE
        ============================ */
        .section-label {
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            font-weight: bold;
            color: #999;
            margin-bottom: 4px;
        }
        .customer-block { margin-bottom: 16px; }
        .customer-name  { font-size: 12px; font-weight: bold; word-break: break-word; }
        .customer-info  { font-size: 9.5px; color: #555; line-height: 1.6; word-break: break-word; }

        /* ============================
           OBSERVAÇÕES
        ============================ */
        .notes-block {
            background: #f9fafb;
            border-left: 3px solid #d1d5db;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9.5px;
            color: #555;
            word-break: break-word;
        }

        /* ============================
           TABELA DE ITENS
           Colunas somam exatamente 100%
        ============================ */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            table-layout: fixed;
        }

        /* #  | Produto | Qtd | Preço Unit. | Subtotal */
        table.items col.c-num  { width:  6%; }
        table.items col.c-prod { width: 44%; }
        table.items col.c-qtd  { width:  9%; }
        table.items col.c-unit { width: 20%; }
        table.items col.c-sub  { width: 21%; }

        table.items thead th {
            background: #eff6ff;
            color: #1e40af;
            font-size: 9.5px;
            font-weight: bold;
            padding: 7px 5px;
            border-bottom: 1px solid #bfdbfe;
            overflow: hidden;
        }
        table.items tbody td {
            padding: 6px 5px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 9.5px;
            word-break: break-word;
            overflow-wrap: break-word;
            overflow: hidden;
        }
        table.items tfoot td {
            padding: 7px 5px;
            background: #eff6ff;
            font-weight: bold;
            font-size: 11px;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .total-value { font-size: 12px; color: #1a56db; font-weight: bold; }

        /* ============================
           RODAPÉ
        ============================ */
        .footer {
            text-align: center;
            font-size: 8.5px;
            color: #bbb;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Cabeçalho --}}
    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="company-name">{{ $company->name ?? 'Invexa' }}</div>
                <div class="company-info">
                    @if(!empty($company->cnpj))    CNPJ: {{ $company->cnpj }}<br>@endif
                    @if(!empty($company->address)) {{ $company->address }}<br>@endif
                    @if(!empty($company->phone))   Tel: {{ $company->phone }}<br>@endif
                    @if(!empty($company->email))   {{ $company->email }}@endif
                </div>
            </td>
            <td class="header-right">
                <div class="invoice-title">NOTA DE VENDA</div>
                <div class="invoice-meta">
                    N&ordm; {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}<br>
                    Data: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}<br>
                    Emitida: {{ now()->format('d/m/Y H:i') }}<br>
                    @php
                        $statusMap = [
                            'concluida' => ['Concluída', 'success'],
                            'pendente'  => ['Pendente',  'warning'],
                            'cancelada' => ['Cancelada', 'danger'],
                        ];
                        [$label, $cls] = $statusMap[$sale->status] ?? [ucfirst($sale->status), 'secondary'];
                    @endphp
                    <span class="badge badge-{{ $cls }}">{{ $label }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Cliente --}}
    <div class="customer-block">
        <div class="section-label">Cliente</div>
        <div class="customer-name">{{ $sale->customer->name ?? $sale->customer_name }}</div>
        <div class="customer-info">
            @if($sale->customer?->cpf_cnpj) CPF/CNPJ: {{ $sale->customer->cpf_cnpj }}<br>@endif
            @if($sale->customer?->email)    {{ $sale->customer->email }}<br>@endif
            @if($sale->customer?->phone)    Tel: {{ $sale->customer->phone }}<br>@endif
            @if($sale->customer?->address)  {{ $sale->customer->address }}@endif
        </div>
    </div>

    {{-- Observações --}}
    @if($sale->notes)
    <div class="notes-block">
        <strong>Observa&ccedil;&otilde;es:</strong> {{ $sale->notes }}
    </div>
    @endif

    {{-- Itens --}}
    <div class="section-label" style="margin-bottom:6px;">Itens da Venda</div>
    <table class="items">
        <colgroup>
            <col class="c-num">
            <col class="c-prod">
            <col class="c-qtd">
            <col class="c-unit">
            <col class="c-sub">
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
                <td class="text-right">R$&nbsp;{{ number_format($item->price, 2, ',', '.') }}</td>
                <td class="text-right">R$&nbsp;{{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Total Geral</td>
                <td class="text-right total-value">R$&nbsp;{{ number_format($sale->total, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Rodapé --}}
    <div class="footer">
        Documento gerado eletronicamente pelo sistema Invexa &mdash; {{ now()->format('d/m/Y \u00e0s H:i') }}
    </div>

</div>
</body>
</html>
