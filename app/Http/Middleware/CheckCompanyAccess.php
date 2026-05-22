<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    protected array $except = [
        'upgrade',
        'logout',
        'pricing',
        'subscription.index',
        'subscription.checkout',
        'subscription.success',
        'subscription.billing-portal',
        'subscription.cancel',
        'subscription.invoice',
        'cashier.webhook',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // SuperAdmin nunca é bloqueado por empresa, trial ou plano
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Modo suporte (impersonate)
        if (session()->has('impersonator_id')) {
            return $next($request);
        }

        // Usuário sem empresa
        if (! $user->company_id) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Seu usuário não está vinculado a nenhuma empresa. Contate o administrador.']);
        }

        $company = $user->company()->first();

        if (! $company || ! $company->active) {
            return redirect()->route('upgrade')
                ->with('error', 'Sua conta está inativa. Entre em contato com o suporte.');
        }

        if ($request->routeIs($this->except)) {
            return $next($request);
        }

        if ($company->isOnTrial()) {
            return $next($request);
        }

        if ($company->hasActiveSubscription()) {
            return $next($request);
        }

        return redirect()->route('upgrade')
            ->with('error', 'Seu período de avaliação encerrou. Escolha um plano para continuar usando o Invexa.');
    }
}
