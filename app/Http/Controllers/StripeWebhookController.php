<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Notifications\PaymentFailed;
use App\Notifications\SubscriptionTrialEnding;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhook;

class StripeWebhookController extends CashierWebhook
{
    /**
     * Fatura paga com sucesso → garante que o plano está ativo.
     */
    public function handleInvoicePaid(array $payload): void
    {
        $company = $this->companyFromPayload($payload['data']['object']['customer'] ?? null);
        if (! $company) return;

        $company->syncPlanFromSubscription();
        Log::info('[Webhook] invoice.paid → empresa #' . $company->id . ' sincronizada.');
    }

    /**
     * Falha no pagamento → notifica o admin.
     */
    public function handleInvoicePaymentFailed(array $payload): void
    {
        $company = $this->companyFromPayload($payload['data']['object']['customer'] ?? null);
        if (! $company) return;

        $admin = $company->users()->where('role', 'admin')->first();
        $admin?->notify(new PaymentFailed($company));
        Log::warning('[Webhook] invoice.payment_failed → empresa #' . $company->id);
    }

    /**
     * Assinatura cancelada/expirada → rebaixa para free.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        $company = $this->companyFromPayload($payload['data']['object']['customer'] ?? null);
        if (! $company) return;

        $company->update(['plan' => 'free']);
        Log::info('[Webhook] subscription.deleted → empresa #' . $company->id . ' rebaixada para free.');
    }

    /**
     * Assinatura atualizada (upgrade/downgrade/renovação) → sincroniza plano.
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        $company = $this->companyFromPayload($payload['data']['object']['customer'] ?? null);
        if (! $company) return;

        $status = $payload['data']['object']['status'] ?? null;

        // Se ficou inadimplente, rebaixa imediatamente
        if (in_array($status, ['past_due', 'unpaid', 'canceled'])) {
            $company->update(['plan' => 'free']);
            Log::warning('[Webhook] subscription.updated status=' . $status . ' → empresa #' . $company->id . ' rebaixada.');
            return;
        }

        $company->syncPlanFromSubscription();
        Log::info('[Webhook] subscription.updated → empresa #' . $company->id . ' sincronizada.');
    }

    /**
     * Trial terminando em 3 dias → notifica o admin.
     */
    public function handleCustomerSubscriptionTrialWillEnd(array $payload): void
    {
        $company = $this->companyFromPayload($payload['data']['object']['customer'] ?? null);
        if (! $company) return;

        $admin = $company->users()->where('role', 'admin')->first();
        $admin?->notify(new SubscriptionTrialEnding($company));
        Log::info('[Webhook] trial_will_end → empresa #' . $company->id . ' notificada.');
    }

    // ── Helper

    private function companyFromPayload(?string $stripeCustomerId): ?Company
    {
        if (! $stripeCustomerId) return null;
        return Company::where('stripe_id', $stripeCustomerId)->first();
    }
}
