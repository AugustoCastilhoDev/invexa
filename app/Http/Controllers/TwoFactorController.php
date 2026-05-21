<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    // Janela de tolerância: ±1 período de 30s (cobre dessincronização de relógio)
    private int $window = 1;

    // ── Exibe a tela de setup (QR Code)
    public function show()
    {
        $user      = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        if (! $user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
        } else {
            $secret = $this->decryptSecret($user);
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
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Informe o código do aplicativo.',
            'code.digits'   => 'O código deve ter exatamente 6 dígitos numéricos.',
        ]);

        $user      = auth()->user();
        $google2fa = app('pragmarx.google2fa');
        $secret    = $this->decryptSecret($user);

        $valid = $google2fa->verifyKey($secret, $request->code, $this->window);

        if (! $valid) {
            return back()->withErrors(['code' => 'Código inválido. Aguarde o próximo código e tente novamente.']);
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
        $secret    = $this->decryptSecret($user);

        $valid = $google2fa->verifyKey($secret, $request->code, $this->window);

        if (! $valid) {
            return back()->withErrors(['code' => 'Código inválido. Verifique o app e tente novamente.']);
        }

        session()->forget('2fa_user_id');
        auth()->login($user);

        return redirect()->intended(route('dashboard'));
    }

    // ── Helper: tenta decrypt; se falhar regenera o secret
    private function decryptSecret(\App\Models\User $user): string
    {
        try {
            return decrypt($user->two_factor_secret);
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            $google2fa = app('pragmarx.google2fa');
            $secret    = $google2fa->generateSecretKey();
            $user->update([
                'two_factor_secret'       => encrypt($secret),
                'two_factor_confirmed_at' => null,
            ]);
            return $secret;
        }
    }
}
