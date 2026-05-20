<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Devoluções</title>
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
  .badge-info    { background: #dbeafe; color: #1e40af; }
  .kpis { display: flex; gap: 14px; margin-bottom: 24px; }
  .kpi { flex: 1; background: #f4f4f4; border-radius: 8px; padding: 10px 14px; }
  .kpi-label { font-size: 10px; text-transform: uppercase; color: #666; }
  .kpi-value { font-size: 16px; font-weight: 700; margin-top: 2px; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Devoluções'])

<p style="color:#555;font-size:11px;margin-bottom:18px;">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>

<div class="kpis">
  <div class="kpi"><div class="kpi-label">Total Devoluções</div><div class="kpi-value">{{ $totalReturns }}</div></div>
  <div class="kpi"><div class="kpi-label">Itens Devolvidos</div><div class="kpi-value">{{ $totalItems }}</div></div>
  <div class="kpi"><div class="kpi-label">Valor Devolvido</div><div class="kpi-value">R$ {{ number_format($totalValue, 2, ',', '.') }}</div></div>
  <div class="kpi"><div class="kpi-label">Ticket Médio</div><div class="kpi-value">R$ {{ $totalReturns > 0 ? number_format($totalValue / $totalReturns, 2, ',', '.') : '0,00' }}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th><th>Data</th><th>Venda</th><th>Cliente</th><th>Motivo</th><th>Itens</th><th class="text-end">Valor</th><th>Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($returns as $r)
    @php
      $statusMap = ['pendente'=>'warning','aprovada'=>'success','rejeitada'=>'danger','concluida'=>'info'];
      $sc = $statusMap[$r->status] ?? 'warning';
      $val = $r->items->sum(fn($i) => $i->quantity * $i->unit_price);
    @endphp
    <tr>
      <td>#{{ $r->id }}</td>
      <td>{{ $r->created_at->format('d/m/Y') }}</td>
      <td>{{ $r->sale_id ? '#'.$r->sale_id : '—' }}</td>
      <td>{{ $r->sale?->customer?->name ?? 'Consumidor' }}</td>
      <td>{{ ucfirst($r->reason ?? '—') }}</td>
      <td>{{ $r->items->sum('quantity') }}</td>
      <td class="text-end">R$ {{ number_format($val, 2, ',', '.') }}</td>
      <td><span class="badge badge-{{ $sc }}">{{ ucfirst($r->status) }}</span></td>
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
