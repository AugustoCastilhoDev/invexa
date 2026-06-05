<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;}
  .wrapper{max-width:580px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);}
  .header{background:linear-gradient(135deg,#7c2d12 0%,#c2410c 100%);padding:36px 40px;text-align:center;}
  .header h1{color:#fff;font-size:22px;margin:14px 0 4px;}
  .header p{color:rgba(255,255,255,.75);font-size:14px;margin:0;}
  .body{padding:36px 40px;}
  .body h2{color:#1e293b;font-size:18px;margin:0 0 12px;}
  .body p{color:#475569;font-size:15px;line-height:1.65;margin:0 0 16px;}
  .alert-box{background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:16px 20px;color:#991b1b;font-size:14px;margin-bottom:20px;}
  .plan-card{border:1px solid #e2e8f0;border-radius:10px;padding:18px 20px;margin-bottom:12px;}
  .plan-card h3{color:#1e293b;font-size:15px;margin:0 0 4px;}
  .plan-card p{color:#64748b;font-size:13px;margin:0;}
  .plan-card .price{color:#0EA5E9;font-size:18px;font-weight:700;}
  .btn{display:inline-block;background:linear-gradient(135deg,#EA580C,#F97316);color:#fff!important;text-decoration:none;padding:13px 32px;border-radius:8px;font-weight:700;font-size:15px;margin:8px 0 24px;}
  .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;}
  .footer p{color:#94a3b8;font-size:12px;margin:0;}
  .footer a{color:#0EA5E9;text-decoration:none;}
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <svg width="48" height="48" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="7" fill="rgba(255,255,255,.1)"/><path d="M7 10h5.5L16 16l3.5-6H25L18 22h-4L7 10Z" fill="#fff"/><circle cx="24" cy="10" r="2.2" fill="rgba(255,255,255,.6)"/></svg>
    <h1>⏳ Seu trial está acabando</h1>
    <p>Não perca o acesso ao Invexa</p>
  </div>
  <div class="body">
    <h2>Olá, {{ $user->name }}!</h2>

    <div class="alert-box">
      @if($daysLeft <= 0)
        ⚠️ <strong>Seu período de teste encerrou hoje.</strong> Faça upgrade para continuar acessando.
      @elseif($daysLeft === 1)
        ⚠️ <strong>Último dia!</strong> Seu trial encerra amanhã.
      @else
        ⚠️ Seu trial encerra em <strong>{{ $daysLeft }} dias</strong>.
      @endif
    </div>

    <p>Não perca o trabalho que você já fez. Escolha um plano para continuar com acesso total ao Invexa.</p>

    <div class="plan-card">
      <h3>Plano Pro</h3>
      <p>Até 500 produtos, 1.000 clientes, 10 usuários e relatórios completos.</p>
      <div class="price">R$ 49,90<span style="font-size:13px;color:#64748b;font-weight:400;">/mês</span></div>
    </div>
    <div class="plan-card">
      <h3>Plano Business</h3>
      <p>Produtos, clientes e usuários ilimitados. Suporte prioritário.</p>
      <div class="price">R$ 97,00<span style="font-size:13px;color:#64748b;font-weight:400;">/mês</span></div>
    </div>

    <div style="text-align:center;margin-top:24px;">
      <a href="{{ config('app.url') }}/upgrade" class="btn">Ver planos e fazer upgrade →</a>
    </div>
  </div>
  <div class="footer">
    <p>Desenvolvido por <a href="https://www.instagram.com/castilho_digital/">Castilho Soluções Digitais</a> &nbsp;·&nbsp; {{ date('Y') }}</p>
  </div>
</div>
</body>
</html>
