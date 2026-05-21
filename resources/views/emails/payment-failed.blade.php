<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falha no Pagamento</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #dc2626; padding: 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .header p { color: #fecaca; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; color: #374151; }
        .body h2 { color: #1f2937; margin-top: 0; }
        .alert { background: #fef2f2; border-left: 4px solid #dc2626; padding: 16px; border-radius: 4px; margin: 20px 0; color: #991b1b; font-size: 14px; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #dc2626; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Invexa</h1>
            <p>Sistema de Gestão Empresarial</p>
        </div>
        <div class="body">
            <h2>Falha no pagamento</h2>
            <p>Olá, <strong>{{ $company->name }}</strong>!</p>
            <div class="alert">
                Não conseguimos processar o pagamento da sua assinatura Invexa. Sem ação, seu acesso pode ser suspenso em breve.
            </div>
            <p>Isso pode acontecer por alguns motivos:</p>
            <ul style="color: #4b5563; line-height: 1.8;">
                <li>Cartão expirado ou com dados desatualizados</li>
                <li>Limite insuficiente no cartão</li>
                <li>Cartão bloqueado pelo banco</li>
            </ul>
            <p>Acesse o painel de assinatura para atualizar seu método de pagamento:</p>
            <a href="{{ config('app.url') }}/settings/subscription" class="btn">Atualizar pagamento →</a>
            <p style="margin-top: 32px; font-size: 13px; color: #6b7280;">
                Se precisar de ajuda, entre em contato pelo e-mail
                <a href="mailto:suporte@offerjetshop.net" style="color: #dc2626;">suporte@offerjetshop.net</a>.
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Invexa. Todos os direitos reservados.<br>
            Enviado para {{ $company->email }}
        </div>
    </div>
</body>
</html>
