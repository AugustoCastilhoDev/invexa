<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // 1. Cria a empresa
        $company = Company::create([
            'name'   => $request->company_name,
            'slug'   => Company::generateSlug($request->company_name),
            'email'  => $request->email,
            'plan'   => 'free',
            'active' => true,
        ]);

        // 2. Cria o usuário como admin da empresa
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $company->id,
            'role'       => 'admin',    // primeiro usuário sempre é admin
            'active'     => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}