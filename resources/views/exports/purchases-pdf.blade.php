<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Compras</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
            padding: 24px;
        }

        /* ── Cabeçalho ──────────────────────────────────── */
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header h1 { font-size: 17px; color: #1e1b4b; margin-bottom: 3px; }
        .header .period { color: #6b7280; font-size: 9px; }
        .brand { font-size: 9px; color: #6b7280; float: right; margin-top: -26px; }

        /* ── KPIs ───────────────────────────────────────── */
        .kpi-row { width: 100%; margin-bottom: 14px; border-spacing: 5px; border-collapse: separate; }
        .kpi-cell {
            width: 25%;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            vertical-align: top;
        }
        .kpi-label { color: #6b7280; font-size: 8px; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 2px; }
        .kpi-value { font-size: 12px; font-weight: bold; color: #111827; }

        /* ── Seção ──────────────────────────────────────── */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #4f46e5;
            margin: 14px 0 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ── Tabelas ────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #4f46e5; color: #fff; }
        th {
            padding: 6px 7px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 700;
        }
        td {
            padding: 5px 7px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            font-size: 9.5px;
        }
        tr:nth-child(even) td { background: #fafafa; }
        .right { text-align: right; }
        .center { text-align: center; }
        .muted { color: #6b7280; }
        .bold { font-weight: bold; }
        .mono { font-family: DejaVu Sans Mono, monospace; }

        /* ── Status badges ──────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .badge-rascunho      { background: #f3f4f6; color: #374151; }
        .badge-enviada       { background: #dbeafe; color: #1d4ed8; }
        .badge-recebida_parcial { background: #fef9c3; color: #a16207; }
        .badge-recebida      { background: #dcfce7; color: #166534; }
        .badge-cancelada     { background: #fee2e2; color: #991b1b; }

        /* ── Tfoot totais ───────────────────────────────── */
        tfoot td {
            border-top: 1.5px solid #d1d5db;
            border-bottom: none;
            font-weight: bold;
            background: #f3f4f6;
        }

        /* ── Rodapé ─────────────────────────────────────── */
        .footer {
            margin-top: 18px;
            border-top: 1px solid #e5e7eb;
            padding-top: 7px;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>

{{-- Cabeçalho --}}
<div class="header">
    <h1>Relatório de Compras</h1>
    <div class="period">
        Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}
        @if($supplierId && isset($supplierName))
            &nbsp;&mdash;&nbsp; Fornecedor: {{ $supplierName }}
        @endif
        @if($status)
            &nbsp;&mdash;&nbsp; Status: {{ \App\Models\PurchaseOrder::STATUS_LABELS[$status] ?? $status }}
        @endif
        &nbsp;&mdash;&nbsp; Gerado em {{ now()->format('d/m/Y \à\s H:i') }}
    </div>
    <div class="brand">INVEXA — Gestão de Estoque e Vendas</div>
</div>

{{-- KPIs --}}
<table class="kpi-row">
    <tr>
        <td class="kpi-cell">
            <div class="kpi-label">Total de OCs</div>
            <div class="kpi-value">{{ $totalOrders }}</div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-label">Valor Total</div>
            <div class="kpi-value">R$ {{ number_format($totalValue, 2, ',', '.') }}</div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-label">Recebido</div>
            <div class="kpi-value">R$ {{ number_format($receivedValue, 2, ',', '.') }}</div>
        </td>
        <td class="kpi-cell">
            <div class="kpi-label">Pendente</div>
            <div class="kpi-value">R$ {{ number_format($pendingValue, 2, ',', '.') }}</div>
        </td>
    </tr>
</table>

{{-- Por Fornecedor --}}
@if($bySupplier->count())
<div class="section-title">Compras por Fornecedor</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Fornecedor</th>
            <th class="center">OCs</th>
            <th class="right">Total (R$)</th>
            <th class="right">% do total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bySupplier as $i => $row)
        <tr>
            <td class="center muted">{{ $i + 1 }}</td>
            <td class="bold">{{ $row['name'] }}</td>
            <td class="center">{{ $row['count'] }}</td>
            <td class="right">R$ {{ number_format($row['total'], 2, ',', '.') }}</td>
            <td class="right muted">
                {{ $totalValue > 0 ? number_format(($row['total'] / $totalValue) * 100, 1) : '0,0' }}%
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Produtos mais comprados --}}
@if($topItems->count())
<div class="section-title">Produtos Mais Comprados</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Categoria</th>
            <th class="center">Qtd</th>
            <th class="center">OCs</th>
            <th class="right">Custo Total (R$)</th>
            <th class="right">Custo Médio/un.</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topItems as $i => $item)
        <tr>
            <td class="center muted">{{ $i + 1 }}</td>
            <td class="bold">{{ $item->product_name }}</td>
            <td class="muted">{{ $item->category_name ?? '—' }}</td>
            <td class="center">{{ number_format($item->total_qty, 0, ',', '.') }}</td>
            <td class="center muted">{{ $item->total_orders }}</td>
            <td class="right">R$ {{ number_format($item->total_cost, 2, ',', '.') }}</td>
            <td class="right muted">
                R$ {{ $item->total_qty > 0 ? number_format($item->total_cost / $item->total_qty, 2, ',', '.') : '0,00' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Lista completa de OCs --}}
@if($orders->count())
<div class="section-title">Ordens de Compra no Período</div>
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Fornecedor</th>
            <th class="center">Status</th>
            <th class="center">Emissão</th>
            <th class="center">Recebimento</th>
            <th class="right">Total (R$)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td class="mono bold" style="color:#4f46e5;">{{ $order->number }}</td>
            <td>{{ optional($order->supplier)->name }}</td>
            <td class="center">
                <span class="badge badge-{{ $order->status }}">
                    {{ \App\Models\PurchaseOrder::STATUS_LABELS[$order->status] ?? $order->status }}
                </span>
            </td>
            <td class="center muted">{{ $order->created_at->format('d/m/Y') }}</td>
            <td class="center muted">{{ $order->received_at ? $order->received_at->format('d/m/Y') : '—' }}</td>
            <td class="right bold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="right">Total do período:</td>
            <td class="right">R$ {{ number_format($totalValue, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
@endif

<div class="footer">
    INVEXA — Sistema profissional para gestão de estoque e vendas — Castilho Soluções Digitais
</div>

</body>
</html>
