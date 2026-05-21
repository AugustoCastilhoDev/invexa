<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Estoque</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 100px; font-size: 10px; font-weight: 600; }
  .badge-ok      { background: #d1fae5; color: #065f46; }
  .badge-low     { background: #fef3c7; color: #92400e; }
  .badge-out     { background: #fee2e2; color: #991b1b; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Estoque'])

<p style="color:#555;font-size:11px;margin-bottom:20px;">Gerado em: {{ now()->format('d/m/Y H:i') }}</p>

<table>
  <thead>
    <tr>
      <th>Produto</th>
      <th>Categoria</th>
      <th class="text-end">Qtd. Atual</th>
      <th class="text-end">Est. Mínimo</th>
      <th>Situação</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $p)
    @php
      $qty = (int) ($p->quantity ?? 0);
      $min = (int) ($p->min_stock ?? 0);
      if ($qty <= 0)        { $cls = 'out'; $lbl = 'Sem Estoque'; }
      elseif ($qty <= $min) { $cls = 'low'; $lbl = 'Estoque Baixo'; }
      else                  { $cls = 'ok';  $lbl = 'Normal'; }
    @endphp
    <tr>
      <td>{{ $p->name }}</td>
      <td>{{ $p->category?->name ?? '—' }}</td>
      <td class="text-end">{{ $qty }}</td>
      <td class="text-end">{{ $min }}</td>
      <td><span class="badge badge-{{ $cls }}">{{ $lbl }}</span></td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="no-print" style="margin-top:32px;text-align:center;">
  <button onclick="window.print()" style="padding:10px 28px;font-size:14px;cursor:pointer;background:#1d4ed8;color:#fff;border:none;border-radius:6px;">&#128438; Imprimir / Salvar como PDF</button>
  <a href="javascript:history.back()" style="margin-left:12px;font-size:13px;color:#555;">Voltar</a>
</div>
</body>
</html>
