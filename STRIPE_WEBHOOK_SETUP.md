# Configurando Webhooks do Stripe

## Ambiente Local (Stripe CLI)

1. Instale o Stripe CLI: https://stripe.com/docs/stripe-cli

2. Faça login:
```bash
stripe login
```

3. Inicie o listener (deixe rodando em um terminal separado):
```bash
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

4. Copie o `whsec_...` exibido no terminal e coloque no `.env`:
```
STRIPE_WEBHOOK_SECRET=whsec_...
```

5. Teste disparando um evento:
```bash
stripe trigger invoice.paid
stripe trigger customer.subscription.deleted
```

## Ambiente de Produção

1. No painel do Stripe: https://dashboard.stripe.com/webhooks
2. Clique em **Add endpoint**
3. URL: `https://seudominio.com.br/stripe/webhook`
4. Eventos a escutar:
   - `invoice.paid`
   - `invoice.payment_failed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `customer.subscription.trial_will_end`
5. Copie o **Signing secret** (`whsec_...`) para a variável `STRIPE_WEBHOOK_SECRET` do servidor

## Verificar se o webhook está funcionando

```bash
# Sincronizar manualmente todas as empresas com o Stripe
php artisan invexa:sync-subscriptions

# Expirar trials manualmente
php artisan invexa:expire-trials

# Verificar logs do webhook
tail -f storage/logs/laravel.log | grep Webhook
```

## Configuração do Scheduler (produção)

Adicione ao crontab do servidor:
```
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```
