<!DOCTYPE html>
<html lang="pt-BR">
<head>
@include('reports.partials.pdf-head')
<title>Produtos Mais Vendidos — Invexa</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .rank { font-weight: 700; color: #1d4ed8; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Produtos Mais Vendidos'])

<p style="color:#555;font-size:11px;margin-bottom:20px;">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Produto</th>
      <th>Categoria</th>
      <th class="text-end">Qtd. Vendida</th>
      <th class="text-end">Nº de Vendas</th>
      <th class="text-end">Receita Total</th>
      <th class="text-end">Ticket Médio</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $i => $p)
    <tr>
      <td class="rank">{{ $i + 1 }}º</td>
      <td>{{ $p->product_name }}</td>
      <td>{{ $p->category_name ?? '—' }}</td>
      <td class="text-end">{{ number_format($p->total_qty, 0, ',', '.') }}</td>
      <td class="text-end">{{ $p->total_sales }}</td>
      <td class="text-end">R$ {{ number_format($p->total_revenue, 2, ',', '.') }}</td>
      <td class="text-end">R$ {{ $p->total_sales > 0 ? number_format($p->total_revenue / $p->total_sales, 2, ',', '.') : '0,00' }}</td>
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
