<!DOCTYPE html>
<html lang="pt-BR">
<head>
@include('reports.partials.pdf-head')
<title>Contas a Pagar — Invexa</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  .text-end { text-align: right; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Contas a Pagar'])

<p style="color:#555;font-size:11px;margin-bottom:12px;">Período: {{ $from->format('d/m/Y') }} até {{ $to->format('d/m/Y') }}</p>

<table style="width:auto;margin-bottom:20px;">
  <tr>
    <td style="padding:4px 16px 4px 0;"><strong>Pagas:</strong> R$ {{ number_format($totalPaid,2,',','.') }}</td>
    <td style="padding:4px 16px 4px 0;"><strong>Pendentes:</strong> R$ {{ number_format($totalPending,2,',','.') }}</td>
    <td style="padding:4px 0;"><strong>Vencidas:</strong> R$ {{ number_format($totalOverdue,2,',','.') }}</td>
  </tr>
</table>

<table>
  <thead>
    <tr><th>Descrição</th><th>Fornecedor</th><th>Vencimento</th><th>Status</th><th class="text-end">Valor</th></tr>
  </thead>
  <tbody>
    @foreach($bills as $b)
    <tr>
      <td>{{ $b->description }}</td>
      <td>{{ optional($b->supplier)->name ?? '—' }}</td>
      <td>{{ \Carbon\Carbon::parse($b->due_date)->format('d/m/Y') }}</td>
      <td>{{ ucfirst($b->status) }}</td>
      <td class="text-end">R$ {{ number_format($b->amount,2,',','.') }}</td>
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
