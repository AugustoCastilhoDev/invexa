<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser válido',
            'password.required' => 'A senha é obrigatória',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Bem-vindo ao sistema!');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle registration request.
     * Creates user + company (plan=free, trial 14 days) and sends WelcomeMail.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required'      => 'O nome é obrigatório',
            'name.max'           => 'O nome não pode ter mais de 255 caracteres',
            'email.required'     => 'O email é obrigatório',
            'email.email'        => 'O email deve ser válido',
            'email.unique'       => 'Este email já está registrado',
            'password.required'  => 'A senha é obrigatória',
            'password.confirmed' => 'As senhas não correspondem',
        ]);

        // Cria empresa com trial de 14 dias
        $companyName = $validated['company_name'] ?? $validated['name'];
        $company = Company::create([
            'name'          => $companyName,
            'slug'          => Company::generateSlug($companyName),
            'email'         => $validated['email'],
            'plan'          => 'free',
            'active'        => true,
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Cria usuário como gerente da empresa
        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'company_id' => $company->id,
            'role'       => 'gerente',
        ]);

        Auth::login($user);

        // Dispara e-mail de boas-vindas (falha silenciosa para não bloquear o cadastro)
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            Log::warning('WelcomeMail falhou para ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('dashboard')
            ->with('success', 'Conta criada! Você tem 14 dias de avaliação gratuita. 🚀');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Você foi desconectado com sucesso.');
    }
}
