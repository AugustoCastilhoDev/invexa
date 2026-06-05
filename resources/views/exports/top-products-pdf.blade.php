<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Produtos Mais Vendidos</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 24px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #1e1b4b;
        }
        .header .period {
            color: #6b7280;
            font-size: 10px;
        }
        .brand {
            font-size: 10px;
            color: #6b7280;
            float: right;
            margin-top: -28px;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-spacing: 6px;
        }
        .summary-cell {
            display: table-cell;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            border-radius: 6px;
            width: 33%;
        }
        .summary-label {
            color: #6b7280;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        thead tr {
            background: #4f46e5;
            color: #ffffff;
        }
        th {
            padding: 7px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        tr:nth-child(even) td {
            background: #f9fafb;
        }
        .rank {
            font-weight: bold;
            color: #4f46e5;
            text-align: center;
        }
        .right { text-align: right; }
        .muted { color: #6b7280; }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Produtos Mais Vendidos</h1>
        <div class="period">
            Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}
            &nbsp;&mdash;&nbsp; Gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
        <div class="brand">INVEXA &mdash; Gestão de Estoque e Vendas</div>
    </div>

    {{-- Resumo --}}
    <table style="margin-bottom:12px;">
        <tr>
            <td style="width:33%;background:#f3f4f6;border:1px solid #e5e7eb;padding:8px 12px;">
                <div style="color:#6b7280;font-size:9px;">Produtos únicos vendidos</div>
                <div style="font-size:13px;font-weight:bold;">{{ $products->count() }}</div>
            </td>
            <td style="width:33%;background:#f3f4f6;border:1px solid #e5e7eb;padding:8px 12px;">
                <div style="color:#6b7280;font-size:9px;">Total de unidades vendidas</div>
                <div style="font-size:13px;font-weight:bold;">{{ number_format($products->sum('total_qty'), 0, ',', '.') }}</div>
            </td>
            <td style="width:33%;background:#f3f4f6;border:1px solid #e5e7eb;padding:8px 12px;">
                <div style="color:#6b7280;font-size:9px;">Receita total do período</div>
                <div style="font-size:13px;font-weight:bold;">R$ {{ number_format($products->sum('total_revenue'), 2, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Tabela --}}
    <table>
        <thead>
            <tr>
                <th style="width:3%;text-align:center;">#</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th style="text-align:right;">Qtd. Vendida</th>
                <th style="text-align:right;">Nº Vendas</th>
                <th style="text-align:right;">Receita</th>
                <th style="text-align:right;">Ticket Médio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $i => $product)
            <tr>
                <td class="rank">{{ $i + 1 }}</td>
                <td style="font-weight:{{ $i < 3 ? 'bold' : 'normal' }};">{{ $product->product_name }}</td>
                <td class="muted">{{ $product->category_name ?? 'Sem categoria' }}</td>
                <td class="right">{{ number_format($product->total_qty, 0, ',', '.') }} un.</td>
                <td class="right muted">{{ $product->total_sales }}</td>
                <td class="right">R$ {{ number_format($product->total_revenue, 2, ',', '.') }}</td>
                <td class="right muted">
                    R$ {{ $product->total_sales > 0 ? number_format($product->total_revenue / $product->total_sales, 2, ',', '.') : '0,00' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#6b7280;">Nenhum produto encontrado para o período selecionado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        INVEXA &mdash; Sistema profissional para gestão de estoque e vendas &mdash; Castilho Soluções Digitais
    </div>

</body>
</html>
