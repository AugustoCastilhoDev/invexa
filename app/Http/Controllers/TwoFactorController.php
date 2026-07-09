<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    private int $window = 1;

    // ── Exibe a tela de setup (QR Code) ou o status, se já ativo
    public function show()
    {
        $user      = auth()->user();
        $isEnabled = (bool) $user->two_factor_confirmed_at;

        if ($isEnabled) {
            return view('settings.two-factor', compact('isEnabled'));
        }

        $google2fa = app('pragmarx.google2fa');
        $secret    = $google2fa->generateSecretKey();

        $user->update(['two_factor_secret' => encrypt($secret)]);
        session(['2fa_setup_secret' => $secret]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

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
        $secret    = session('2fa_setup_secret');

        if (! $secret) {
            return redirect()->route('two-factor.index')
                ->withErrors(['code' => 'Sessão expirada. Escaneie o QR Code novamente.']);
        }

        $valid = $google2fa->verifyKey($secret, $request->code, $this->window);

        if (! $valid) {
            return back()->withErrors(['code' => 'Código inválido. Aguarde o próximo código e tente novamente.']);
        }

        $user->update([
            'two_factor_secret'       => encrypt($secret),
            'two_factor_confirmed_at' => now(),
        ]);
        session()->forget('2fa_setup_secret');

        return redirect()->route('dashboard')
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
        session()->forget('2fa_setup_secret');

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

        try {
            $secret = decrypt($user->two_factor_secret);
        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
            return redirect()->route('login')
                ->withErrors(['code' => 'Erro de configuração do 2FA. Contate o suporte.']);
        }

        $valid = $google2fa->verifyKey($secret, $request->code, $this->window);

        if (! $valid) {
            return back()->withErrors(['code' => 'Código inválido. Verifique o app e tente novamente.']);
        }

        session()->forget('2fa_user_id');
        auth()->login($user);

        return redirect()->intended(route('dashboard'));
    }
}
