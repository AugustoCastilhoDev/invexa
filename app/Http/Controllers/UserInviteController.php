<?php

namespace App\Http\Controllers;

use App\Mail\InviteUserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserInviteController extends Controller
{
    /**
     * Envia (ou reenvia) o convite para um usuário já criado.
     */
    public function send(User $user)
    {
        $this->ensureSameCompany($user);

        if ($user->invite_accepted_at) {
            return back()->with('error', 'Este usuário já aceitou o convite e possui acesso ao sistema.');
        }

        $token = Str::random(64);

        $user->update([
            'invite_token'   => $token,
            'invite_sent_at' => now(),
        ]);

        $inviteUrl = route('invite.accept', ['token' => $token]);

        try {
            Mail::to($user->email)->send(
                new InviteUserMail($user, auth()->user(), $inviteUrl)
            );
            return back()->with('success', 'Convite enviado para ' . $user->email . '.');
        } catch (\Throwable $e) {
            Log::warning('InviteUserMail falhou para ' . $user->email . ': ' . $e->getMessage());
            return back()->with('error', 'Não foi possível enviar o e-mail. Verifique as configurações de e-mail.');
        }
    }

    /**
     * Exibe o formulário de definição de senha (link do e-mail).
     */
    public function showAccept(string $token)
    {
        $user = User::where('invite_token', $token)
            ->whereNull('invite_accepted_at')
            ->where('invite_sent_at', '>=', now()->subDays(7))
            ->firstOrFail();

        return view('users.invite-accept', compact('user', 'token'));
    }

    /**
     * Processa a definição de senha e ativa o usuário.
     */
    public function accept(Request $request, string $token)
    {
        $user = User::where('invite_token', $token)
            ->whereNull('invite_accepted_at')
            ->where('invite_sent_at', '>=', now()->subDays(7))
            ->firstOrFail();

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password'            => Hash::make($request->password),
            'invite_accepted_at'  => now(),
            'invite_token'        => null,
            'active'              => true,
        ]);

        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Bem-vindo ao ' . config('app.name') . '! Sua conta está ativa.');
    }

    private function ensureSameCompany(User $user): void
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
