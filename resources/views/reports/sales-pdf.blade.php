<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Vendas</title>
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
  .badge { display: inline-block; padding: 2px 8px; border-radius: 100px; font-size: 10px; font-weight: 600; }
  .badge-success { background: #d1fae5; color: #065f46; }
  .badge-warning { background: #fef3c7; color: #92400e; }
  .badge-danger  { background: #fee2e2; color: #991b1b; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>
<h1>Relat&oacute;rio de Vendas</h1>
<p class="sub">Per&iacute;odo: {{ $from->format('d/m/Y') }} at&eacute; {{ $to->format('d/m/Y') }} &mdash; Gerado em {{ now()->format('d/m/Y H:i') }}</p>

<div class="kpis">
  <div class="kpi"><div class="kpi-label">Total de Vendas</div><div class="kpi-value">{{ $totalSales }}</div></div>
  <div class="kpi"><div class="kpi-label">Receita Total</div><div class="kpi-value">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div></div>
  <div class="kpi"><div class="kpi-label">Canceladas</div><div class="kpi-value">{{ $totalCanceled }}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th><th>Cliente</th><th>Data</th><th class="text-end">Total</th><th>Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sales as $s)
    @php $sc = ['concluida'=>'success','pendente'=>'warning','cancelada'=>'danger']; @endphp
    <tr>
      <td>#{{ $s->id }}</td>
      <td>{{ optional($s->customer)->name ?? 'Consumidor' }}</td>
      <td>{{ $s->created_at->format('d/m/Y') }}</td>
      <td class="text-end">R$ {{ number_format($s->total, 2, ',', '.') }}</td>
      <td><span class="badge badge-{{ $sc[$s->status] ?? 'warning' }}">{{ ucfirst($s->status) }}</span></td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="no-print" style="margin-top:32px;text-align:center;">
  <button onclick="window.print()" style="padding:10px 28px;font-size:14px;cursor:pointer;background:#1d4ed8;color:#fff;border:none;border-radius:6px;">
    &#128438; Imprimir / Salvar como PDF
  </button>
  <a href="javascript:history.back()" style="margin-left:12px;font-size:13px;color:#555;">Voltar</a>
</div>
</body>
</html>
