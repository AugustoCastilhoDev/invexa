<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\InvalidRequestException as StripeInvalidRequestException;

class SubscriptionController extends Controller
{
    public function index()
    {
        $company      = auth()->user()->company;
        $subscription = $company->subscription('default');
        $invoices     = $company->invoices();

        return view('settings.subscription', compact('company', 'subscription', 'invoices'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan'    => 'required|in:pro,pro_launch,business',
            'billing' => 'nullable|in:monthly,annual',
        ]);

        $billing = $request->input('billing', 'monthly');
        $plan    = $request->input('plan');
        $company = auth()->user()->company;

        // Resolve price ID: procura plan_annual primeiro, cai em plan
        $priceKey = ($billing === 'annual')
            ? config('cashier.prices.' . $plan . '_annual',
              config('cashier.prices.' . $plan))
            : config('cashier.prices.' . $plan);

        $this->resolveStripeCustomer($company);

        $company->createOrGetStripeCustomer([
            'name'  => $company->name,
            'email' => $company->email,
        ]);

        $company->refresh();

        $checkoutParams = [
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('pricing'),
            'metadata'    => [
                'company_id' => $company->id,
                'plan'       => $plan,
                'billing'    => $billing,
            ],
        ];

        try {
            return $company
                ->newSubscription('default', $priceKey)
                ->checkout($checkoutParams);
        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('subscription.index'),
            ]);
        }
    }

    private function resolveStripeCustomer(Company $company): void
    {
        if (! $company->stripe_id) {
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $stripe->customers->retrieve($company->stripe_id);
        } catch (StripeInvalidRequestException $e) {
            if (str_contains($e->getMessage(), 'a similar object exists in')) {
                $company->forceFill(['stripe_id' => null])->save();
            } else {
                throw $e;
            }
        }
    }

    public function success(Request $request)
    {
        $company = auth()->user()->company;
        $plan    = 'free';

        if ($request->filled('session_id')) {
            try {
                $stripe  = new \Stripe\StripeClient(config('cashier.secret'));
                $session = $stripe->checkout->sessions->retrieve($request->session_id);

                if (! empty($session->metadata->plan)) {
                    $plan = $session->metadata->plan === 'business' ? 'business' : 'pro';
                }
            } catch (\Exception $e) {
                // fallback abaixo
            }
        }

        if ($plan === 'free') {
            $company->refresh();
            $sub = $company->subscription('default');
            if ($sub) {
                $map = [
                    config('cashier.prices.pro')             => 'pro',
                    config('cashier.prices.pro_launch')      => 'pro',
                    config('cashier.prices.pro_annual')      => 'pro',
                    config('cashier.prices.business')        => 'business',
                    config('cashier.prices.business_annual') => 'business',
                ];
                $priceId = $sub->items()->first()?->stripe_price
                         ?? $sub->stripe_price
                         ?? null;
                $plan = $map[$priceId] ?? 'free';
            }
        }

        $company->update([
            'plan'          => $plan,
            'trial_ends_at' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', '🎉 Assinatura ativada com sucesso! Bem-vindo ao ' . ucfirst($plan) . '.');
    }

    public function billingPortal()
    {
        return auth()->user()->company->redirectToBillingPortal(route('subscription.index'));
    }

    public function cancel()
    {
        $company = auth()->user()->company;
        $company->subscription('default')?->cancel();

        return redirect()->route('subscription.index')
            ->with('warning', 'Assinatura cancelada. Você terá acesso até o fim do período pago.');
    }
}
