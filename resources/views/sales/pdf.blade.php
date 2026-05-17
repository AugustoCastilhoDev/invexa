<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>NF Simplificada #{{ $sale->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            padding: 30px 36px;
        }

        /* ---- Cabeçalho ---- */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 24px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 16px;
        }
        .header-left  { display: table-cell; width: 70%; vertical-align: middle; }
        .header-right { display: table-cell; width: 30%; vertical-align: middle; text-align: right; }

        .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: -.5px;
        }
        .company-info { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.6; }

        .nf-title {
            font-size: 13px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .nf-number { font-size: 22px; font-weight: 700; color: #1a1a2e; }
        .nf-date   { font-size: 10px; color: #777; margin-top: 2px; }

        /* ---- Status badge ---- */
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-top: 6px;
        }
        .status-concluida { background: #dcfce7; color: #166534; }
        .status-pendente  { background: #fef9c3; color: #854d0e; }
        .status-cancelada { background: #fee2e2; color: #991b1b; }

        /* ---- Seção de dados ---- */
        .section {
            margin-bottom: 18px;
        }
        .section-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #4f46e5;
            border-bottom: 1px solid #e0e0f0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 12px;
        }
        .info-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: .4px; }
        .info-value { font-size: 11px; color: #1a1a2e; font-weight: 600; margin-bottom: 6px; }

        /* ---- Tabela de itens ---- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .items-table thead tr {
            background: #4f46e5;
            color: #fff;
        }
        .items-table thead th {
            padding: 7px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .4px;
            font-weight: 700;
        }
        .items-table tbody tr {
            border-bottom: 1px solid #ebebf5;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f7f7fe;
        }
        .items-table tbody td {
            padding: 7px 10px;
            font-size: 11px;
            color: #333;
        }
        .items-table tfoot tr {
            background: #f0f0fc;
        }
        .items-table tfoot td {
            padding: 8px 10px;
            font-size: 11px;
        }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .total-row td { font-size: 13px; font-weight: 700; color: #4f46e5; }

        /* ---- Observações ---- */
        .notes-box {
            background: #f8f8fe;
            border: 1px solid #e0e0f0;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 10px;
            color: #555;
            margin-top: 6px;
        }

        /* ---- Rodapé ---- */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e0e0f0;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #aaa;
        }
        .disclaimer {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 20px;
            font-size: 9px;
            color: #92400e;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Cabeçalho: empresa + número NF --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">{{ strtoupper($company->name ?? 'Empresa') }}</div>
            <div class="company-info">
                @if($company->cnpj) CNPJ: {{ $company->cnpj }}<br>@endif
                @if($company->phone) Tel: {{ $company->phone }}<br>@endif
                @if($company->email) E-mail: {{ $company->email }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="nf-title">Nota Fiscal</div>
            <div class="nf-title">Simplificada</div>
            <div class="nf-number">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="nf-date">
                Emitida em: {{ now()->format('d/m/Y H:i') }}
            </div>
            <div>
                @php
                    $statusClass = match($sale->status) {
                        'concluida' => 'status-concluida',
                        'pendente'  => 'status-pendente',
                        'cancelada' => 'status-cancelada',
                        default     => 'status-pendente',
                    };
                    $statusLabel = match($sale->status) {
                        'concluida' => 'Concluída',
                        'pendente'  => 'Pendente',
                        'cancelada' => 'Cancelada',
                        default     => ucfirst($sale->status),
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
        </div>
    </div>

    {{-- Dados da venda + cliente --}}
    <div class="section">
        <div class="section-title">Dados da Venda</div>
        <div class="info-grid">
            <div class="info-col">
                <div class="info-label">Data da Venda</div>
                <div class="info-value">
                    {{ $sale->sale_date?->timezone(config('app.timezone', 'America/Sao_Paulo'))->format('d/m/Y H:i') ?? '-' }}
                </div>
                <div class="info-label">Número da Venda</div>
                <div class="info-value">#{{ $sale->id }}</div>
            </div>
            <div class="info-col">
                <div class="info-label">Cliente</div>
                <div class="info-value">{{ $sale->customer_name ?? 'Não informado' }}</div>
                @if($sale->customer)
                    @if($sale->customer->document)
                        <div class="info-label">CPF / CNPJ</div>
                        <div class="info-value">{{ $sale->customer->document }}</div>
                    @endif
                    @if($sale->customer->phone)
                        <div class="info-label">Telefone</div>
                        <div class="info-value">{{ $sale->customer->phone }}</div>
                    @endif
                    @if($sale->customer->email)
                        <div class="info-label">E-mail</div>
                        <div class="info-value">{{ $sale->customer->email }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Tabela de itens --}}
    <div class="section">
        <div class="section-title">Itens da Venda</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width:5%">#</th>
                    <th style="width:45%">Produto / Descrição</th>
                    <th class="text-center" style="width:10%">Qtd</th>
                    <th class="text-right" style="width:20%">Preço Unitário</th>
                    <th class="text-right" style="width:20%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sale->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        {{ $item->product->name ?? 'Produto removido' }}
                        @if($item->product?->sku)
                            <br><span style="font-size:9px;color:#888;">SKU: {{ $item->product->sku }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                    <td class="text-right fw-bold">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="color:#aaa;padding:16px;">Nenhum item.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                @php $itemCount = $sale->items->sum('quantity'); @endphp
                <tr>
                    <td colspan="3" class="text-right" style="font-size:10px;color:#888;">Total de itens: {{ $itemCount }}</td>
                    <td class="text-right" style="font-size:10px;color:#888;">Total:</td>
                    <td class="text-right fw-bold" style="font-size:13px;color:#4f46e5;">
                        R$ {{ number_format($sale->total, 2, ',', '.') }}
                    </td>
                </tr>
                @if($sale->total_returned > 0)
                <tr>
                    <td colspan="4" class="text-right" style="font-size:10px;color:#888;">Total devolvido:</td>
                    <td class="text-right" style="font-size:11px;color:#dc2626;font-weight:700;">
                        - R$ {{ number_format($sale->total_returned, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Valor Líquido:</td>
                    <td class="text-right">R$ {{ number_format($sale->net_total, 2, ',', '.') }}</td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>

    {{-- Observações --}}
    @if($sale->notes)
    <div class="section">
        <div class="section-title">Observações</div>
        <div class="notes-box">{{ $sale->notes }}</div>
    </div>
    @endif

    {{-- Aviso legal --}}
    <div class="disclaimer">
        <strong>DOCUMENTO NÃO FISCAL</strong> —
        Este documento é um comprovante interno de venda gerado pelo sistema INVEXA e
        <strong>não substitui uma Nota Fiscal Eletrônica (NF-e) emitida pela SEFAZ</strong>.
    </div>

    {{-- Rodapé --}}
    <div class="footer">
        Gerado por <strong>INVEXA</strong> &mdash; {{ now()->format('d/m/Y H:i:s') }}
        &nbsp;&nbsp;&bull;&nbsp;&nbsp;
        {{ $company->name ?? '' }}
    </div>

</body>
</html>
