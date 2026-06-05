<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Vendas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 24px;
        }
        h1 {
            font-size: 20px;
            margin: 0 0 8px 0;
        }
        .muted {
            color: #6b7280;
            font-size: 11px;
            margin-bottom: 16px;
        }
        .summary {
            margin-bottom: 16px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .summary p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
        }
        .right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <h1>Relatório de Vendas</h1>

    <div class="muted">
        @if($from || $to)
            Período:
            {{ $from ? \Carbon\Carbon::parse($from)->format('d/m/Y') : 'início' }}
            até
            {{ $to ? \Carbon\Carbon::parse($to)->format('d/m/Y') : 'fim' }}
        @elseif($interval === 'today')
            Período: Hoje
        @elseif($interval === '7d')
            Período: Últimos 7 dias
        @elseif($interval === 'month')
            Período: Mês atual
        @else
            Período: Todos
        @endif
    </div>

    <div class="summary">
        <p><strong>Total de vendas:</strong> {{ $sales->count() }}</p>
        <p><strong>Faturamento total:</strong> R$ {{ number_format($sales->sum('total'), 2, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Status</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ optional($sale->sale_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->customer_name ?? 'Cliente não informado' }}</td>
                    <td>{{ $sale->status }}</td>
                    <td class="right">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhuma venda encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>