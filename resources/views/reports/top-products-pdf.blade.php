<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Produtos Mais Vendidos</title>
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
  .text-right { text-align: right; }
  .badge-pos { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px; background: #e5e7eb; color: #374151; }
  .badge-pos.gold   { background: #fef3c7; color: #92400e; }
  .badge-pos.silver { background: #f1f5f9; color: #334155; }
  .badge-pos.bronze { background: #fef0e4; color: #7c3d12; }
  tfoot td { font-weight: 700; border-top: 2px solid #111; background: #f9fafb; }
  @media print {
    body { padding: 0; }
    .no-print { display: none; }
  }
</style>
</head>
<body>

<h1>Produtos Mais Vendidos</h1>
<p class="sub">
  Per&iacute;odo: {{ $from->format('d/m/Y') }} &ndash; {{ $to->format('d/m/Y') }}
  &nbsp;&bull;&nbsp; Gerado em {{ now()->format('d/m/Y H:i') }}
</p>

<div class="kpis">
  <div class="kpi">
    <div class="kpi-label">Produtos &uacute;nicos</div>
    <div class="kpi-value">{{ $products->count() }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Total de unidades</div>
    <div class="kpi-value">{{ number_format($products->sum('total_qty'), 0, ',', '.') }}</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Receita total</div>
    <div class="kpi-value">R$ {{ number_format($products->sum('total_revenue'), 2, ',', '.') }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th style="width:3rem;">#</th>
      <th>Produto</th>
      <th>Categoria</th>
      <th class="text-right">Qtd. Vendida</th>
      <th class="text-right">N&ordm; Vendas</th>
      <th class="text-right">Receita</th>
      <th class="text-right">Ticket M&eacute;dio</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $i => $product)
    <tr>
      <td>
        @if($i === 0)
          <span class="badge-pos gold">1&ordm;</span>
        @elseif($i === 1)
          <span class="badge-pos silver">2&ordm;</span>
        @elseif($i === 2)
          <span class="badge-pos bronze">3&ordm;</span>
        @else
          <span class="badge-pos">{{ $i + 1 }}&ordm;</span>
        @endif
      </td>
      <td><strong>{{ $product->product_name }}</strong></td>
      <td>{{ $product->category_name ?? 'Sem categoria' }}</td>
      <td class="text-right">{{ number_format($product->total_qty, 0, ',', '.') }} un.</td>
      <td class="text-right">{{ $product->total_sales }}</td>
      <td class="text-right">R$ {{ number_format($product->total_revenue, 2, ',', '.') }}</td>
      <td class="text-right">
        R$ {{ $product->total_sales > 0 ? number_format($product->total_revenue / $product->total_sales, 2, ',', '.') : '0,00' }}
      </td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" class="text-right">Total:</td>
      <td class="text-right">{{ number_format($products->sum('total_qty'), 0, ',', '.') }} un.</td>
      <td></td>
      <td class="text-right">R$ {{ number_format($products->sum('total_revenue'), 2, ',', '.') }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>

<div class="no-print" style="margin-top:32px;text-align:center;">
  <button onclick="window.print()" style="padding:10px 28px;font-size:14px;cursor:pointer;background:#1d4ed8;color:#fff;border:none;border-radius:6px;">
    &#128438; Imprimir / Salvar como PDF
  </button>
  <a href="javascript:history.back()" style="margin-left:12px;font-size:13px;color:#555;">Voltar</a>
</div>

</body>
</html>
