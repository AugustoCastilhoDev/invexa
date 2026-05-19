@component('mail::message')
# Bem-vindo ao Invexa, {{ $user->name }}! 🎉

Estamos felizes em ter você aqui. Sua conta foi criada com sucesso e você já pode começar a usar o sistema.

**O que você pode fazer agora:**
- Cadastrar seus produtos e categorias
- Registrar clientes e fornecedores
- Lançar vendas pelo PDV
- Controlar contas a pagar e a receber

@component('mail::button', ['url' => config('app.url') . '/dashboard', 'color' => 'green'])
Acessar o Invexa
@endcomponent

Qualquer dúvida, estamos à disposição.

Equipe Invexa
@endcomponent
