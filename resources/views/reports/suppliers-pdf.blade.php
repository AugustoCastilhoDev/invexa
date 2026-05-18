<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Fornecedores</title>
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
  @media print { body { padding: 0; } }
</style>
</head>
<body>
<h1>Relatório de Fornecedores</h1>
<p class="sub">Gerado em {{ now()->format('d/m/Y H:i') }}</p>

<div class="kpis">
  <div class="kpi"><div class="kpi-label">Total de Fornecedores</div><div class="kpi-value">{{ $total }}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>Nome</th><th>CNPJ/CPF</th><th>E-mail</th><th>Telefone</th><th>Cidade/UF</th>
    </tr>
  </thead>
  <tbody>
    @foreach($suppliers as $s)
    <tr>
      <td>{{ $s->name }}</td>
      <td>{{ $s->document ?? '—' }}</td>
      <td>{{ $s->email ?? '—' }}</td>
      <td>{{ $s->phone ?? '—' }}</td>
      <td>{{ $s->city ? $s->city . '/' . $s->state : '—' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<script>window.print();</script>
</body></html>
