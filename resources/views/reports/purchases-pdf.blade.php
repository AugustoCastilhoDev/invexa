<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Compras</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 100px; font-size: 10px; font-weight: 600; }
  .badge-success { background: #d1fae5; color: #065f46; }
  .badge-warning { background: #fef3c7; color: #92400e; }
  .badge-danger  { background: #fee2e2; color: #991b1b; }
  .totals { margin: 16px 0; padding: 12px 16px; background: #f4f4f4; border-radius: 8px; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Compras'])

<p style="color:#555;font-size:11px;margin-bottom:20px;">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>

<div class="totals">
  Total: <strong>{{ $orders->count() }} ordem(ns)</strong> &nbsp;|&nbsp;
  Valor total: <strong>R$ {{ number_format($orders->sum('total'), 2, ',', '.') }}</strong>
</div>

<table>
  <thead>
    <tr><th>#</th><th>Fornecedor</th><th>Data</th><th class="text-end">Total</th><th>Status</th></tr>
  </thead>
  <tbody>
    @foreach($orders as $o)
    @php $sc = ['recebido'=>'success','pendente'=>'warning','cancelado'=>'danger']; @endphp
    <tr>
      <td>{{ $o->id }}</td>
      <td>{{ $o->supplier?->name ?? '—' }}</td>
      <td>{{ $o->created_at->format('d/m/Y') }}</td>
      <td class="text-end">R$ {{ number_format($o->total, 2, ',', '.') }}</td>
      <td><span class="badge badge-{{ $sc[$o->status] ?? 'warning' }}">{{ ucfirst($o->status) }}</span></td>
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
