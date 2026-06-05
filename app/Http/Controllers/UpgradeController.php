<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    public function index(Request $request)
    {
        $company     = $request->user()?->company;
        $currentPlan = $company?->plan ?? 'free';
        $trialDaysLeft = null;
        $hasActiveSubscription = false;

        if ($company) {
            // Verifica trial via coluna da companies (sem depender de subscriptions)
            if ($company->isOnTrial()) {
                $trialDaysLeft = $company->trialDaysLeft();
            }

            // Tenta buscar assinatura apenas se a coluna billable_id existir
            try {
                $subscription = \Illuminate\Support\Facades\DB::table('subscriptions')
                    ->where('billable_id', $company->id)
                    ->where('name', 'default')
                    ->orderByDesc('created_at')
                    ->first();

                $hasActiveSubscription = $subscription &&
                    in_array($subscription->stripe_status, ['active', 'trialing', 'past_due']) &&
                    (is_null($subscription->ends_at) || $subscription->ends_at > now()->toDateTimeString());
            } catch (\Exception $e) {
                // Tabela ainda em migração — ignora
                $subscription = null;
            }
        }

        return view('upgrade', compact(
            'company', 'currentPlan', 'trialDaysLeft', 'hasActiveSubscription'
        ));
    }
}
