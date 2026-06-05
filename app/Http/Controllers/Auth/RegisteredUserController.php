<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'min:2', 'max:100'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'     => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()],
            'plan'         => ['nullable', 'in:free,pro,pro_launch,business'],
            'billing'      => ['nullable', 'in:monthly,annual'],
        ], [
            'company_name.required' => 'Informe o nome da empresa.',
            'company_name.min'      => 'O nome da empresa deve ter pelo menos 2 caracteres.',
            'name.required'         => 'Informe seu nome.',
            'email.required'        => 'Informe seu e-mail.',
            'email.email'           => 'Informe um e-mail válido.',
            'email.unique'          => 'Este e-mail já está em uso.',
            'password.required'     => 'Informe uma senha.',
            'password.confirmed'    => 'As senhas não conferem.',
            'password.min'          => 'A senha deve ter pelo menos 8 caracteres com letras e números.',
        ]);

        $plan    = $request->input('plan', 'free');
        $billing = $request->input('billing', 'monthly');
        $isPaid  = in_array($plan, ['pro', 'pro_launch', 'business']);

        // 1. Cria a empresa — sempre free inicialmente (plano é ativado após pagamento)
        $company = Company::create([
            'name'                 => $request->company_name,
            'slug'                 => Company::generateSlug($request->company_name),
            'email'                => $request->email,
            'plan'                 => 'free',
            'active'               => true,
            'trial_ends_at'        => Carbon::now()->addDays(14),
            'onboarding_completed' => false,
        ]);

        // 2. Cria o usuário admin
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $company->id,
            'role'       => 'admin',
            'active'     => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // 3. E-mail de boas-vindas
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::warning('WelcomeMail não enviado para ' . $user->email . ': ' . $e->getMessage());
        }

        // 4. Se plano pago foi selecionado, redireciona direto para checkout
        if ($isPaid) {
            return redirect()->route('subscription.checkout.redirect', [
                'plan'    => $plan,
                'billing' => $billing,
            ]);
        }

        // 5. Plano free — wizard de onboarding normal
        return redirect()->route('onboarding.show');
    }
}
