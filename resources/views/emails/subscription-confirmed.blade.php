<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinatura Confirmada</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #4f46e5; padding: 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .header p { color: #c7d2fe; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; color: #374151; }
        .body h2 { color: #1f2937; margin-top: 0; }
        .plan-badge { display: inline-block; background: #4f46e5; color: #fff; padding: 6px 18px; border-radius: 20px; font-size: 14px; font-weight: bold; margin: 16px 0; text-transform: uppercase; letter-spacing: 1px; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Invexa</h1>
            <p>Sistema de Gestão Empresarial</p>
        </div>
        <div class="body">
            <h2>Assinatura confirmada!</h2>
            <p>Olá, <strong>{{ $company->name }}</strong>!</p>
            <p>Seu pagamento foi processado com sucesso. Você agora tem acesso completo ao plano:</p>
            <div><span class="plan-badge">{{ ucfirst($plan) }}</span></div>
            <p>Todos os recursos do seu plano já estão disponíveis. Acesse o painel agora e aproveite ao máximo o Invexa!</p>
            <a href="{{ config('app.url') }}/dashboard" class="btn">Acessar o painel →</a>
            <p style="margin-top: 32px; font-size: 13px; color: #6b7280;">
                Você pode gerenciar sua assinatura, visualizar faturas e cancelar a qualquer momento em
                <a href="{{ config('app.url') }}/settings/subscription" style="color: #4f46e5;">Configurações → Assinatura</a>.
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Invexa. Todos os direitos reservados.<br>
            Enviado para {{ $company->email }}
        </div>
    </div>
</body>
</html>
