<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Invexa</title>
    <style>
        body { margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
        .wrapper { max-width:600px; margin:40px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(15,23,42,.10); }
        .header { background:linear-gradient(135deg,#1d4ed8,#2563eb); padding:36px 40px; text-align:center; }
        .header h1 { margin:0; color:#ffffff; font-size:26px; font-weight:700; letter-spacing:-.02em; }
        .header p { margin:6px 0 0; color:rgba(255,255,255,.75); font-size:14px; }
        .body { padding:36px 40px; }
        .body h2 { font-size:20px; font-weight:700; color:#1e293b; margin-top:0; }
        .body p { font-size:15px; line-height:1.7; color:#475569; margin:0 0 16px; }
        .features { background:#f8fafc; border-radius:8px; padding:20px 24px; margin:24px 0; }
        .features ul { margin:0; padding:0 0 0 20px; }
        .features li { font-size:14px; color:#475569; line-height:2; }
        .btn-wrap { text-align:center; margin:28px 0; }
        .btn { display:inline-block; background:#2563eb; color:#ffffff !important; text-decoration:none; padding:13px 32px; border-radius:8px; font-size:15px; font-weight:600; }
        .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; }
        .footer p { margin:0; font-size:12px; color:#94a3b8; }
        .footer a { color:#2563eb; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📦 Invexa</h1>
        <p>Gestão inteligente para o seu negócio</p>
    </div>
    <div class="body">
        <h2>Olá, {{ $user->name }}! 👋</h2>
        <p>Seja muito bem-vindo ao <strong>Invexa</strong>! Sua conta foi criada com sucesso e você já pode começar a usar o sistema.</p>
        <div class="features">
            <p style="margin:0 0 10px;font-weight:600;color:#1e293b;font-size:14px;">O que você pode fazer agora:</p>
            <ul>
                <li>📦 Gerenciar produtos e estoque</li>
                <li>💰 Registrar vendas e emitir relatórios</li>
                <li>📑 Controlar contas a pagar e a receber</li>
                <li>👥 Cadastrar clientes e fornecedores</li>
                <li>📊 Acompanhar o fluxo de caixa no dashboard</li>
            </ul>
        </div>
        <p>Acesse o painel agora e configure sua empresa:</p>
        <div class="btn-wrap">
            <a href="{{ url('/dashboard') }}" class="btn">Acessar o painel →</a>
        </div>
        <p style="font-size:13px;color:#94a3b8;">Se você não criou esta conta, ignore este e-mail.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} <a href="{{ url('/') }}">Invexa</a>. Todos os direitos reservados.</p>
        <p style="margin-top:4px;">Enviado para <a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
    </div>
</div>
</body>
</html>
