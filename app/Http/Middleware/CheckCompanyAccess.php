<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    protected array $except = [
        'upgrade', 'logout', 'pricing',
        'subscription.index', 'subscription.checkout',
        'subscription.success', 'subscription.billing-portal',
        'subscription.cancel', 'subscription.invoice', 'cashier.webhook',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) return redirect()->route('login');

        // SuperAdmin direto (sem impersonate)
        if ($user->role === 'superadmin') return $next($request);

        // Modo suporte ativo: acesso irrestrito à empresa impersonada,
        // independente do plano ou status do trial.
        if (session()->has('impersonator_id')) return $next($request);

        if (! $user->company_id) {
            return redirect()->route('login')->withErrors(['email' => 'Usuario sem empresa vinculada.']);
        }

        $company = $user->company()->first();

        if (! $company || ! $company->active) return redirect()->route('upgrade');
        if ($request->routeIs($this->except)) return $next($request);

        try {
            $hasSubscription = $company->hasActiveSubscription();
        } catch (\Throwable $e) {
            $hasSubscription = false;
        }

        if ($company->isOnTrial() || $hasSubscription) return $next($request);

        return redirect()->route('upgrade');
    }
}
