<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Compras</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; background: #fff; padding: 32px; }
  h1 { font-size: 18px; margin-bottom: 4px; }
  .sub { font-size: 11px; color: #555; margin-bottom: 24px; }
  .kpis { display: flex; gap: 16px; margin-bottom: 24px; }
  .kpi { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 12px 16px; }
  .kpi-label { font-size: 10px; text-transform: uppercase; font-weight: 700; color: #666; letter-spacing: .06em; }
  .kpi-value { font-size: 20px; font-weight: 700; margin-top: 4px; }
  table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  thead tr { background: #f3f4f6; }
  th { text-align: left; padding: 7px 10px; font-size: 10px; text-transform: uppercase; font-weight: 700; color: #555; letter-spacing: .06em; border-bottom: 2px solid #e5e7eb; }
  td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
  tfoot td { font-weight: 700; border-top: 2px solid #111; background: #f9fafb; }
  .text-right { text-align: right; }
  @media print {
    body { padding: 0; }
    .no-print { display: none; }
  }
</style>
</head>
<body>

<h1>Relatório de Compras</h1>
<p class="sub">
  Período: {{ $from->format('d/m/Y') }} – {{ $to->format('d/m/Y') }}
  &nbsp;&bull;&nbsp; Gerado em {{ now()->format('d/m/Y H:i') }}
</p>

<div class="kpis">
  <div class="kpi">
    <div class="kpi-label">Total de OCs</div>
    <div class="kpi-value">{{ $orders->count() }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Valor Total</div>
    <div class="kpi-value">R$ {{ number_format($totalValue, 2, ',', '.') }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Recebido</div>
    <div class="kpi-value">R$ {{ number_format($receivedValue, 2, ',', '.') }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Pendente</div>
    <div class="kpi-value">R$ {{ number_format($pendingValue, 2, ',', '.') }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>Número</th>
      <th>Fornecedor</th>
      <th>Status</th>
      <th>Emissão</th>
      <th>Recebimento</th>
      <th class="text-right">Total</th>
    </tr>
  </thead>
  <tbody>
    @foreach($orders as $o)
    <tr>
      <td><strong>{{ $o->number }}</strong></td>
      <td>{{ optional($o->supplier)->name ?? '&mdash;' }}</td>
      <td>{{ $o->status_label ?? $o->status }}</td>
      <td>{{ $o->created_at->format('d/m/Y') }}</td>
      <td>{{ $o->received_at ? $o->received_at->format('d/m/Y') : '&mdash;' }}</td>
      <td class="text-right">R$ {{ number_format($o->total, 2, ',', '.') }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" class="text-right">Total:</td>
      <td class="text-right">R$ {{ number_format($totalValue, 2, ',', '.') }}</td>
    </tr>
  </tfoot>
</table>

<div class="no-print" style="margin-top:32px;text-align:center;">
  <button onclick="window.print()" style="padding:10px 28px;font-size:14px;cursor:pointer;background:#1d4ed8;color:#fff;border:none;border-radius:6px;">
    🖨️ Imprimir / Salvar como PDF
  </button>
  <a href="javascript:history.back()" style="margin-left:12px;font-size:13px;color:#555;">Voltar</a>
</div>

</body>
</html>
