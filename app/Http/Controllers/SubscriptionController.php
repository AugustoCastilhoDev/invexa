<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;

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
        $request->validate(['plan' => 'required|in:pro,pro_launch,business']);

        $company = auth()->user()->company;
        $priceId = config('cashier.prices.' . $request->plan);

        $checkoutParams = [
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('pricing'),
            'metadata'    => [
                'company_id' => $company->id,
                'plan'       => $request->plan,
            ],
        ];

        if (! $company->stripe_id) {
            // Novo customer: define nome e e-mail para aparecer nas faturas
            $checkoutParams['customer_email'] = $company->email;
            $checkoutParams['customer_creation'] = 'always';
            $checkoutParams['customer_update'] = null;

            // Cria o customer no Stripe com nome antes do checkout
            $company->createOrGetStripeCustomer([
                'name'  => $company->name,
                'email' => $company->email,
            ]);
        } else {
            // Customer existente: garante que o nome está atualizado
            $company->updateStripeCustomer([
                'name'  => $company->name,
                'email' => $company->email,
            ]);
        }

        try {
            return $company
                ->newSubscription('default', $priceId)
                ->checkout($checkoutParams);
        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('subscription.index'),
            ]);
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
                    config('cashier.prices.pro')        => 'pro',
                    config('cashier.prices.pro_launch') => 'pro',
                    config('cashier.prices.business')   => 'business',
                ];
                $priceId = $sub->items()->first()?->stripe_price
                         ?? $sub->stripe_price
                         ?? null;
                $plan = $map[$priceId] ?? 'free';
            }
        }

        // Atualiza plano e encerra o trial — assinatura paga substitui o trial
        $company->update([
            'plan'          => $plan,
            'trial_ends_at' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', '\ud83c\udf89 Assinatura ativada com sucesso! Bem-vindo ao ' . ucfirst($plan) . '.');
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
            ->with('warning', 'Assinatura cancelada. Voc\u00ea ter\u00e1 acesso at\u00e9 o fim do per\u00edodo pago.');
    }
}
