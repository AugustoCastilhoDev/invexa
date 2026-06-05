<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convite — {{ config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); padding: 40px 40px 32px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,.8); font-size: 14px; }
        .body { padding: 36px 40px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .body strong { color: #1e293b; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #0ea5e9, #6366f1); color: #fff !important; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: .3px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .info-box p { margin: 4px 0; font-size: 14px; color: #64748b; }
        .info-box strong { color: #334155; }
        .url-fallback { word-break: break-all; color: #6366f1; font-size: 13px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; }
        .footer p { margin: 0; color: #94a3b8; font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Sistema de Gestão Comercial</p>
    </div>
    <div class="body">
        <p>Olá! 👋</p>
        <p>
            <strong>{{ $invitedBy->name }}</strong> convidou você para fazer parte da empresa
            <strong>{{ $invitedUser->company->name }}</strong> no <strong>{{ config('app.name') }}</strong>
            com o papel de <strong>{{ ucfirst($invitedUser->role) }}</strong>.
        </p>
        <p>Clique no botão abaixo para aceitar o convite e definir sua senha de acesso.</p>
        <div class="btn-wrap">
            <a href="{{ $inviteUrl }}" class="btn">Aceitar convite</a>
        </div>
        <div class="info-box">
            <p><strong>Empresa:</strong> {{ $invitedUser->company->name }}</p>
            <p><strong>Seu e-mail:</strong> {{ $invitedUser->email }}</p>
            <p><strong>Seu papel:</strong> {{ ucfirst($invitedUser->role) }}</p>
        </div>
        <p style="font-size:13px;color:#94a3b8;">Se o botão não funcionar, copie e cole o link abaixo no seu navegador:</p>
        <p class="url-fallback">{{ $inviteUrl }}</p>
        <p style="font-size:13px;color:#ef4444;">⚠️ Este link expira em <strong>7 dias</strong>. Se você não esperava este convite, pode ignorar este e-mail.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>
