<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    public function index(Request $request)
    {
        $company      = $request->user()?->company;
        $subscription = $company?->subscription('default');
        $currentPlan  = $company?->plan ?? 'free';
        $trialDaysLeft = null;

        if ($company && $company->isOnTrial()) {
            $trialDaysLeft = $company->trialDaysLeft();
        }

        return view('upgrade', compact('company', 'subscription', 'currentPlan', 'trialDaysLeft'));
    }
}
