<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    /**
     * Bloqueia acesso se o trial expirou e a empresa não tem plano pago ativo.
     * Super-admins (sem company_id) são sempre permitidos.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Usuários sem empresa (super-admin) passam direto
        if (! $user || ! $user->company_id) {
            return $next($request);
        }

        $company = $user->company;

        // Empresa inexistente ou inativa
        if (! $company || ! $company->active) {
            return redirect()->route('upgrade')->with('error', 'Sua conta está inativa. Entre em contato com o suporte.');
        }

        // Trial ou plano expirado — redireciona para upgrade (evita loop)
        if (! $company->isAccessible() && ! $request->routeIs('upgrade', 'logout')) {
            return redirect()->route('upgrade');
        }

        return $next($request);
    }
}
