<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Se o usuário tem 2FA ativo mas ainda não verificou nesta sessão
        if (
            $user &&
            $user->two_factor_confirmed_at &&
            ! session()->has('2fa_verified')
        ) {
            // Permite acessar a rota de verificação sem loop
            if ($request->routeIs('two-factor.verify', 'two-factor.validate', 'logout')) {
                return $next($request);
            }

            session(['2fa_user_id' => $user->id]);
            auth()->logout();
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }
}
