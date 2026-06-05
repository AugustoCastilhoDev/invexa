<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso nas rotas:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,gerente')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Sua conta foi desativada. Entre em contato com o administrador.']);
        }

        if (! $user->hasRole($roles)) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}