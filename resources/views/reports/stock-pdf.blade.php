<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Estoque</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  h1 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
  .sub { color: #555; font-size: 11px; margin-bottom: 24px; }
  .kpis { display: flex; gap: 16px; margin-bottom: 24px; }
  .kpi { flex: 1; background: #f4f4f4; border-radius: 8px; padding: 12px 16px; }
  .kpi-label { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #666; }
  .kpi-value { font-size: 18px; font-weight: 700; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .text-center { text-align: center; }
  .low { color: #dc2626; font-weight: 700; }
  @media print { body { padding: 0; } }
</style>
</head>
<body>
<h1>Relatório de Estoque</h1>
<p class="sub">Gerado em {{ now()->format('d/m/Y H:i') }}</p>

<div class="kpis">
  <div class="kpi"><div class="kpi-label">Produtos Ativos</div><div class="kpi-value">{{ $totalActive }}</div></div>
  <div class="kpi"><div class="kpi-label">Estoque Baixo</div><div class="kpi-value">{{ $totalLow }}</div></div>
  <div class="kpi"><div class="kpi-label">Valor Total</div><div class="kpi-value">R$ {{ number_format($totalValue, 2, ',', '.') }}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>Produto</th><th>Categoria</th><th class="text-center">Qtd.</th><th class="text-center">Mín.</th>
      <th class="text-end">Custo</th><th class="text-end">Venda</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $p)
    @php $low = $p->min_quantity > 0 && $p->quantity <= $p->min_quantity; @endphp
    <tr>
      <td>{{ $p->name }}{{ $low ? ' ⚠' : '' }}</td>
      <td>{{ optional($p->category)->name ?? '—' }}</td>
      <td class="text-center {{ $low ? 'low' : '' }}">{{ $p->quantity }}</td>
      <td class="text-center">{{ $p->min_quantity ?? '—' }}</td>
      <td class="text-end">R$ {{ number_format($p->cost_price ?? 0, 2, ',', '.') }}</td>
      <td class="text-end">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<script>window.print();</script>
</body></html>
