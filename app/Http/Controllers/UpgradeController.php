<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpgradeController extends Controller
{
    public function index(Request $request)
    {
        $company     = $request->user()?->company;
        $currentPlan = $company?->plan ?? 'free';
        $trialDaysLeft = null;

        if ($company && $company->isOnTrial()) {
            $trialDaysLeft = $company->trialDaysLeft();
        }

        // Busca via billable_id (coluna padrão do Cashier na tabela subscriptions)
        $subscription = null;
        if ($company) {
            $subscription = DB::table('subscriptions')
                ->where('billable_id', $company->id)
                ->where('name', 'default')
                ->orderByDesc('created_at')
                ->first();
        }

        $hasActiveSubscription = $subscription &&
            in_array($subscription->stripe_status, ['active', 'trialing', 'past_due']) &&
            (is_null($subscription->ends_at) || $subscription->ends_at > now()->toDateTimeString());

        return view('upgrade', compact(
            'company', 'subscription', 'currentPlan', 'trialDaysLeft', 'hasActiveSubscription'
        ));
    }
}
