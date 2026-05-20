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

        try {
            return $company
                ->newSubscription('default', $priceId)
                ->checkout([
                    'success_url'    => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url'     => route('pricing'),
                    'customer_email' => $company->email,
                    'metadata'       => ['company_id' => $company->id],
                ]);
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
        $company->syncPlanFromSubscription();

        return redirect()->route('dashboard')
            ->with('success', '🎉 Assinatura ativada com sucesso! Bem-vindo ao ' . ucfirst($company->plan) . '.');
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
