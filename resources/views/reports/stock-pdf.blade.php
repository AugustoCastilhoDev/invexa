<!DOCTYPE html>
<html lang="pt-BR">
<head>
@include('reports.partials.pdf-head')
<title>Relatório de Estoque — Invexa</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .low { color: #b91c1c; font-weight: 700; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Estoque'])

<table style="width:auto;margin-bottom:20px;">
  <tr>
    <td style="padding:4px 16px 4px 0;"><strong>Produtos ativos:</strong> {{ $totalActive }}</td>
    <td style="padding:4px 16px 4px 0;"><strong>Estoque baixo:</strong> {{ $totalLow }}</td>
    <td style="padding:4px 0;"><strong>Valor total:</strong> R$ {{ number_format($totalValue,2,',','.') }}</td>
  </tr>
</table>

<table>
  <thead>
    <tr><th>Produto</th><th>Categoria</th><th class="text-end">Qtd.</th><th class="text-end">Mín.</th><th class="text-end">Custo</th><th class="text-end">Venda</th><th>Status</th></tr>
  </thead>
  <tbody>
    @foreach($products as $p)
    @php
      if (!$p->active) $status = 'Inativo';
      elseif ($p->min_quantity > 0 && $p->quantity <= $p->min_quantity) $status = 'Baixo';
      else $status = 'OK';
    @endphp
    <tr>
      <td>{{ $p->name }}</td>
      <td>{{ optional($p->category)->name ?? '—' }}</td>
      <td class="text-end {{ $status === 'Baixo' ? 'low' : '' }}">{{ $p->quantity }}</td>
      <td class="text-end">{{ $p->min_quantity ?? 0 }}</td>
      <td class="text-end">R$ {{ number_format($p->cost_price ?? 0,2,',','.') }}</td>
      <td class="text-end">R$ {{ number_format($p->price,2,',','.') }}</td>
      <td class="{{ $status === 'Baixo' ? 'low' : '' }}">{{ $status }}</td>
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
