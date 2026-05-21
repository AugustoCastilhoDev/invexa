<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    // ── Exibe a tela de setup (QR Code)
    public function show()
    {
        $user   = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        // Se já tem secret, usa o existente; senão gera um novo
        if (! $user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $isEnabled = (bool) $user->two_factor_confirmed_at;

        return view('settings.two-factor', compact('qrCodeUrl', 'secret', 'isEnabled'));
    }

    // ── Confirma o código e ativa o 2FA
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Informe o código do aplicativo.',
            'code.size'     => 'O código deve ter 6 dígitos.',
        ]);

        $user      = auth()->user();
        $google2fa = app('pragmarx.google2fa');
        $secret    = decrypt($user->two_factor_secret);

        $valid = $google2fa->verifyKey($secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        return redirect()->route('settings.two-factor')
            ->with('success', '2FA ativado com sucesso! Sua conta está mais segura. 🔐');
    }

    // ── Desativa o 2FA
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required'         => 'Informe sua senha para confirmar.',
            'password.current_password' => 'Senha incorreta.',
        ]);

        auth()->user()->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('settings.two-factor')
            ->with('success', '2FA desativado com sucesso.');
    }

    // ── Tela de verificação ao fazer login
    public function verify()
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    // ── Valida o código na tela de verificação pós-login
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = session('2fa_user_id');
        if (! $userId) return redirect()->route('login');

        $user      = \App\Models\User::findOrFail($userId);
        $google2fa = app('pragmarx.google2fa');
        $secret    = decrypt($user->two_factor_secret);

        $valid = $google2fa->verifyKey($secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'Código inválido. Verifique o app e tente novamente.']);
        }

        session()->forget('2fa_user_id');
        auth()->login($user);

        return redirect()->intended(route('dashboard'));
    }
}
