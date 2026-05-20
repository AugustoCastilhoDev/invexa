<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Fornecedores</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 32px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  thead th { background: #1a1a2e; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f8f8f8; }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>

@include('reports.partials.pdf-header', ['reportTitle' => 'Relatório de Fornecedores'])

<p style="color:#555;font-size:11px;margin-bottom:20px;">Gerado em: {{ now()->format('d/m/Y H:i') }}</p>

<table>
  <thead>
    <tr><th>Nome</th><th>CNPJ / CPF</th><th>Telefone</th><th>E-mail</th><th>Cidade / UF</th></tr>
  </thead>
  <tbody>
    @foreach($suppliers as $s)
    <tr>
      <td>{{ $s->name }}</td>
      <td>{{ $s->document ?? '—' }}</td>
      <td>{{ $s->phone ?? '—' }}</td>
      <td>{{ $s->email ?? '—' }}</td>
      <td>{{ $s->city ? $s->city . ($s->state ? '/' . $s->state : '') : '—' }}</td>
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
