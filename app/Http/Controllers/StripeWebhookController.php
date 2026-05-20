<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhook;

class StripeWebhookController extends CashierWebhook
{
    public function handleInvoicePaid(array $payload): void
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if (!$customerId) return;
        Company::where('stripe_id', $customerId)->first()?->syncPlanFromSubscription();
    }

    public function handleInvoicePaymentFailed(array $payload): void
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if (!$customerId) return;

        $company = Company::where('stripe_id', $customerId)->first();
        if (!$company) return;

        $admin = $company->users()->where('role', 'admin')->first();
        $admin?->notify(new \App\Notifications\PaymentFailed($company));
    }

    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if (!$customerId) return;
        Company::where('stripe_id', $customerId)->first()?->update(['plan' => 'free']);
    }

    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        $customerId = $payload['data']['object']['customer'] ?? null;
        if (!$customerId) return;
        Company::where('stripe_id', $customerId)->first()?->syncPlanFromSubscription();
    }
}
