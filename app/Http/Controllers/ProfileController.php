<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password'      => ['required_with:password', 'nullable', 'string'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
        ], [
            'name.required'    => 'O nome é obrigatório.',
            'email.required'   => 'O e-mail é obrigatório.',
            'email.email'      => 'Informe um e-mail válido.',
            'email.unique'     => 'Este e-mail já está em uso.',
            'password.min'     => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ]);

        // Verifica senha atual se o usuário quer trocar a senha
        if ($request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'A senha atual está incorreta.']);
            }
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil atualizado com sucesso.');
    }
}