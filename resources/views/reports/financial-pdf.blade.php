<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório Financeiro</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  h1 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
  .sub { color: #555; font-size: 11px; margin-bottom: 24px; }
  .kpis { display: flex; gap: 16px; margin-bottom: 24px; }
  .kpi { flex: 1; background: #f4f4f4; border-radius: 8px; padding: 12px 16px; }
  .kpi-label { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #666; }
  .kpi-value { font-size: 18px; font-weight: 700; margin-top: 2px; }
  h2 { font-size: 13px; font-weight: 700; margin: 24px 0 8px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 100px; font-size: 10px; font-weight: 600; }
  .badge-success { background: #d1fae5; color: #065f46; }
  .badge-warning { background: #fef3c7; color: #92400e; }
  .badge-danger  { background: #fee2e2; color: #991b1b; }
  @media print { body { padding: 0; } }
</style>
</head>
<body>
<h1>Relatório Financeiro</h1>
<p class="sub">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }} — Gerado em {{ now()->format('d/m/Y H:i') }}</p>

<div class="kpis">
  <div class="kpi"><div class="kpi-label">Receitas Recebidas</div><div class="kpi-value">R$ {{ number_format($receivablesPaid, 2, ',', '.') }}</div></div>
  <div class="kpi"><div class="kpi-label">Despesas Pagas</div><div class="kpi-value">R$ {{ number_format($billsPaid, 2, ',', '.') }}</div></div>
  <div class="kpi"><div class="kpi-label">Saldo Líquido</div><div class="kpi-value">R$ {{ number_format($netBalance, 2, ',', '.') }}</div></div>
  <div class="kpi"><div class="kpi-label">Saldo Projetado</div><div class="kpi-value">R$ {{ number_format($projectedBalance, 2, ',', '.') }}</div></div>
</div>

<h2>Contas a Receber</h2>
<table>
  <thead><tr><th>Descrição</th><th>Vencimento</th><th class="text-end">Valor</th><th>Status</th></tr></thead>
  <tbody>
    @foreach($receivables as $r)
    @php $sc = ['recebido'=>'success','pendente'=>'warning','cancelado'=>'danger']; @endphp
    <tr>
      <td>{{ $r->description }}</td>
      <td>{{ \Carbon\Carbon::parse($r->due_date)->format('d/m/Y') }}</td>
      <td class="text-end">R$ {{ number_format($r->amount, 2, ',', '.') }}</td>
      <td><span class="badge badge-{{ $sc[$r->status] ?? 'warning' }}">{{ ucfirst($r->status) }}</span></td>
    </tr>
    @endforeach
  </tbody>
</table>

<h2>Contas a Pagar</h2>
<table>
  <thead><tr><th>Descrição</th><th>Vencimento</th><th class="text-end">Valor</th><th>Status</th></tr></thead>
  <tbody>
    @foreach($bills as $b)
    @php $sc = ['pago'=>'success','pendente'=>'warning','cancelado'=>'danger']; @endphp
    <tr>
      <td>{{ $b->description }}</td>
      <td>{{ \Carbon\Carbon::parse($b->due_date)->format('d/m/Y') }}</td>
      <td class="text-end">R$ {{ number_format($b->amount, 2, ',', '.') }}</td>
      <td><span class="badge badge-{{ $sc[$b->status] ?? 'warning' }}">{{ ucfirst($b->status) }}</span></td>
    </tr>
    @endforeach
  </tbody>
</table>

<script>window.print();</script>
</body></html>
