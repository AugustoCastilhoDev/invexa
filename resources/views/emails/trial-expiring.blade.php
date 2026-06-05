@component('mail::message')
# Seu período de teste está acabando, {{ $user->name }}! ⏳

Seu trial gratuito no Invexa **expira em {{ $trialEndsAt }}**.

Não perca o acesso ao sistema — faça o upgrade agora e continue gerenciando seu negócio sem interrupções.

**Com o plano pago você continua tendo:**
- Acesso completo ao PDV e estoque
- Relatórios financeiros em PDF e CSV
- Alertas automáticos por e-mail
- Suporte prioritário

@component('mail::button', ['url' => config('app.url') . '/upgrade', 'color' => 'green'])
Ver planos e fazer upgrade
@endcomponent

Equipe Invexa
@endcomponent
