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

        // Super-admin (sem empresa) passa direto
        if (! $user || ! $user->company_id) {
            return $next($request);
        }

        // Modo suporte (impersonate) — SuperAdmin nunca é bloqueado por trial/plano
        if (session()->has('impersonator_id')) {
            return $next($request);
        }

        // fresh() garante dados atualizados do banco, sem cache de relacionamento
        $company = $user->company()->first();

        // Empresa inexistente ou inativa
        if (! $company || ! $company->active) {
            return redirect()->route('upgrade')
                ->with('error', 'Sua conta está inativa. Entre em contato com o suporte.');
        }

        // Rotas que nunca devem ser bloqueadas
        if ($request->routeIs($this->except)) {
            return $next($request);
        }

        // Plano free sempre tem acesso
        if ($company->plan === 'free') {
            return $next($request);
        }

        // Trial ativo: passa
        if ($company->isOnTrial()) {
            return $next($request);
        }

        // Assinatura paga ativa: passa
        if ($company->hasActiveSubscription()) {
            return $next($request);
        }

        // Nenhuma condição satisfeita: upgrade
        return redirect()->route('upgrade');
    }
}
