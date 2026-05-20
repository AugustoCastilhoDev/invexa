<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;}
  .wrapper{max-width:580px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#080D1A 0%,#0D1929 100%);padding:36px 40px;text-align:center;}
  .header svg{width:48px;height:48px;}
  .header h1{color:#F0F9FF;font-size:22px;margin:14px 0 4px;letter-spacing:-.02em;}
  .header p{color:rgba(226,232,240,.65);font-size:14px;margin:0;}
  .body{padding:36px 40px;}
  .body h2{color:#1e293b;font-size:18px;margin:0 0 12px;}
  .body p{color:#475569;font-size:15px;line-height:1.65;margin:0 0 16px;}
  .feature{display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;}
  .feature-icon{width:36px;height:36px;border-radius:8px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;}
  .feature-text{font-size:14px;color:#475569;line-height:1.5;}
  .feature-text strong{color:#1e293b;display:block;margin-bottom:2px;}
  .btn{display:inline-block;background:linear-gradient(135deg,#0EA5E9,#38BDF8);color:#fff!important;text-decoration:none;padding:13px 32px;border-radius:8px;font-weight:700;font-size:15px;margin:8px 0 24px;}
  .trial-badge{background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:14px 18px;color:#1d4ed8;font-size:14px;margin-bottom:24px;}
  .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;}
  .footer p{color:#94a3b8;font-size:12px;margin:0;}
  .footer a{color:#0EA5E9;text-decoration:none;}
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <svg viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="7" fill="#080D1A"/><path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#0EA5E9"/><circle cx="24" cy="10" r="2.2" fill="#38BDF8"/></svg>
    <h1>Bem-vindo ao Invexa! 🚀</h1>
    <p>Sua gestão de estoque e vendas começa agora</p>
  </div>
  <div class="body">
    <h2>Olá, {{ $user->name }}! 👋</h2>
    <p>Sua conta foi criada com sucesso. Você tem acesso completo ao Invexa durante o seu período de teste gratuito.</p>

    <div class="trial-badge">
      🎁 <strong>14 dias de trial gratuito</strong> — explore todas as funcionalidades sem precisar de cartão de crédito.
    </div>

    <p><strong>O que você pode fazer agora:</strong></p>

    <div class="feature">
      <div class="feature-icon">📦</div>
      <div class="feature-text"><strong>Cadastrar produtos e categorias</strong>Organize seu estoque e controle entradas e saídas.</div>
    </div>
    <div class="feature">
      <div class="feature-icon">🛒</div>
      <div class="feature-text"><strong>Registrar vendas</strong>Emita notas, acompanhe pagamentos e histórico de clientes.</div>
    </div>
    <div class="feature">
      <div class="feature-icon">💰</div>
      <div class="feature-text"><strong>Controle financeiro</strong>Contas a pagar, contas a receber e relatórios completos.</div>
    </div>
    <div class="feature">
      <div class="feature-icon">📊</div>
      <div class="feature-text"><strong>Relatórios e gráficos</strong>Tome decisões baseadas em dados reais do seu negócio.</div>
    </div>

    <div style="text-align:center;margin-top:28px;">
      <a href="{{ config('app.url') }}/dashboard" class="btn">Acessar minha conta →</a>
    </div>
  </div>
  <div class="footer">
    <p>Desenvolvido por <a href="https://www.instagram.com/castilho_digital/">Castilho Soluções Digitais</a> &nbsp;·&nbsp; {{ date('Y') }}</p>
    <p style="margin-top:6px;">Se você não criou esta conta, ignore este e-mail.</p>
  </div>
</div>
</body>
</html>
