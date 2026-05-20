<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Stripe\StripeClient;

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
                'plan'       => $request->plan,   // guarda o slug do plano para usar no success()
            ],
        ];

        if (! $company->stripe_id) {
            $checkoutParams['customer_email'] = $company->email;
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

    /**
     * Callback de sucesso do Stripe Checkout.
     *
     * Lemos o slug do plano diretamente dos metadata da Session — isso evita
     * race condition com o webhook (que pode ainda não ter chegado quando o
     * usuário é redirecionado de volta ao sistema).
     */
    public function success(Request $request)
    {
        $company = auth()->user()->company;
        $plan    = 'free';

        if ($request->filled('session_id')) {
            try {
                $stripe  = new \Stripe\StripeClient(config('cashier.secret'));
                $session = $stripe->checkout->sessions->retrieve($request->session_id);

                // Pega o slug gravado nos metadata no momento do checkout
                if (! empty($session->metadata->plan)) {
                    $plan = $session->metadata->plan === 'business' ? 'business' : 'pro';
                }
            } catch (\Exception $e) {
                // Silencia — fallback para syncPlanFromSubscription abaixo
            }
        }

        // Se por algum motivo o metadata não veio, tenta via subscription
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

        $company->update(['plan' => $plan]);

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
