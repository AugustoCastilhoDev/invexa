<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Vendas</title>
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
  .totals { margin: 16px 0; padding: 12px 16px; background: #f4f4f4; border-radius: 8px; font-size: 12px; }
  .totals strong { font-size: 14px; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Vendas'])

<p style="color:#555;font-size:11px;margin-bottom:20px;">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>

<div class="totals">
  Total de vendas: <strong>{{ $sales->count() }}</strong> &nbsp;|&nbsp;
  Receita total: <strong>R$ {{ number_format($sales->sum('total'), 2, ',', '.') }}</strong>
</div>

<table>
  <thead>
    <tr>
      <th>#</th><th>Data</th><th>Cliente</th><th>Forma Pgto.</th>
      <th class="text-end">Total</th><th>Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sales as $s)
    @php $sc = ['confirmada'=>'success','pendente'=>'warning','cancelada'=>'danger']; @endphp
    <tr>
      <td>{{ $s->id }}</td>
      <td>{{ $s->created_at->format('d/m/Y') }}</td>
      <td>{{ $s->customer?->name ?? 'Avulso' }}</td>
      <td>{{ $s->payment_method_label ?? $s->payment_method }}</td>
      <td class="text-end">R$ {{ number_format($s->total, 2, ',', '.') }}</td>
      <td><span class="badge badge-{{ $sc[$s->status] ?? 'warning' }}">{{ ucfirst($s->status) }}</span></td>
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
