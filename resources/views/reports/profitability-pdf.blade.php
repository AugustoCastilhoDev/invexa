<!DOCTYPE html>
<html lang="pt-BR">
<head>
@include('reports.partials.pdf-head')
<title>Relatório de Lucratividade — Invexa</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .kpi-row { display: flex; gap: 24px; margin-bottom: 20px; }
  .kpi { flex: 1; background: #f1f5f9; border-radius: 6px; padding: 10px 14px; }
  .kpi label { font-size: 10px; color: #64748b; display: block; margin-bottom: 4px; text-transform: uppercase; }
  .kpi span { font-size: 15px; font-weight: 700; }
  .green { color: #16a34a; } .red { color: #dc2626; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Lucratividade'])

<p style="color:#555;font-size:11px;margin-bottom:16px;">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>

<div class="kpi-row">
  <div class="kpi"><label>Receita Total</label><span>R$ {{ number_format($totalRevenue,2,',','.') }}</span></div>
  <div class="kpi"><label>Custo Total</label><span class="red">R$ {{ number_format($totalCost,2,',','.') }}</span></div>
  <div class="kpi"><label>Lucro Bruto</label><span class="{{ $totalProfit>=0?'green':'red' }}">R$ {{ number_format($totalProfit,2,',','.') }}</span></div>
  <div class="kpi"><label>Margem Bruta</label><span class="{{ $totalMargin>=0?'green':'red' }}">{{ number_format($totalMargin,1,',','.') }}%</span></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Produto</th>
      <th>Categoria</th>
      <th class="text-end">Qtd.</th>
      <th class="text-end">Receita</th>
      <th class="text-end">Custo</th>
      <th class="text-end">Lucro</th>
      <th class="text-end">Margem</th>
    </tr>
  </thead>
  <tbody>
    @foreach($items as $i => $item)
    @php $margin = $item->total_revenue > 0 ? ($item->total_profit / $item->total_revenue) * 100 : 0; @endphp
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{ $item->product_name }}</td>
      <td>{{ $item->category_name ?? '—' }}</td>
      <td class="text-end">{{ number_format($item->total_qty,0,',','.') }}</td>
      <td class="text-end">R$ {{ number_format($item->total_revenue,2,',','.') }}</td>
      <td class="text-end">R$ {{ number_format($item->total_cost,2,',','.') }}</td>
      <td class="text-end {{ $item->total_profit>=0?'green':'red' }}">R$ {{ number_format($item->total_profit,2,',','.') }}</td>
      <td class="text-end {{ $margin>=0?'green':'red' }}">{{ number_format($margin,1,',','.') }}%</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="no-print" style="margin-top:32px;text-align:center;">
  <button onclick="window.print()" style="padding:10px 28px;font-size:14px;cursor:pointer;background:#1d4ed8;color:#fff;border:none;border-radius:6px;">🖨 Imprimir / Salvar como PDF</button>
  <a href="javascript:history.back()" style="margin-left:12px;font-size:13px;color:#555;">Voltar</a>
</div>
</body>
</html>
