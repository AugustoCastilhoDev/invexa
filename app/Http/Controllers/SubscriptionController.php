<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Página de assinatura/upgrade.
     */
    public function index(): View
    {
        $company = auth()->user()->company;

        return view('subscription.index', compact('company'));
    }

    /**
     * Inicia o checkout no Stripe e redireciona para a URL de pagamento.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $request->validate([
            'plan'    => ['required', 'in:pro,pro_launch,business'],
            'billing' => ['required', 'in:monthly,annual'],
        ]);

        $company = auth()->user()->company;
        $plan    = $request->input('plan');
        $billing = $request->input('billing');

        $priceId = config('plans.' . $plan . '.' . $billing);

        if (! $priceId) {
            return back()->withErrors(['plan' => 'Plano ou período inválido. Tente novamente.']);
        }

        $session = $company
            ->newSubscription('default', $priceId)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('upgrade'),
            ]);

        return redirect($session->url);
    }

    /**
     * GET intermediário após registro com plano pago.
     * Renderiza uma página que faz auto-submit do formulário para o checkout.
     */
    public function checkoutRedirect(Request $request): View
    {
        $plan    = $request->query('plan', 'pro_launch');
        $billing = $request->query('billing', 'monthly');

        return view('subscription.checkout-redirect', compact('plan', 'billing'));
    }

    /**
     * Página de sucesso pós-pagamento.
     */
    public function success(Request $request): RedirectResponse|View
    {
        $company = auth()->user()->company;

        if ($company->subscribed('default')) {
            $priceId = $company->subscription('default')->stripe_price;

            $planName = collect(config('plans'))
                ->flatMap(fn ($periods, $plan) => collect($periods)->map(fn ($id) => [$id => $plan]))
                ->collapse()
                ->get($priceId, 'pro');

            $company->update(['plan' => $planName]);
        }

        return view('subscription.success', compact('company'));
    }

    /**
     * Redireciona para o portal de cobrança do Stripe.
     */
    public function billingPortal(): RedirectResponse
    {
        return redirect(
            auth()->user()->company->billingPortalUrl(route('upgrade'))
        );
    }

    /**
     * Cancela a assinatura no fim do período.
     */
    public function cancel(): RedirectResponse
    {
        auth()->user()->company->subscription('default')->cancel();

        return redirect()->route('upgrade')
            ->with('success', 'Assinatura cancelada. Você terá acesso até o fim do período pago.');
    }
}
