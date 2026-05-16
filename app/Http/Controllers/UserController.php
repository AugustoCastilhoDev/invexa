<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Lista os usuários da empresa do usuário autenticado.
     */
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    /**
     * Exibe o formulário de novo usuário.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Salva um novo usuário.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'gerente', 'vendedor'])],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password,
            'company_id' => auth()->user()->company_id,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(User $user)
    {
        $this->ensureSameCompany($user);

        return view('users.edit', compact('user'));
    }

    /**
     * Atualiza um usuário.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureSameCompany($user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'gerente', 'vendedor'])],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'active' => $request->boolean('active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove um usuário.
     */
    public function destroy(User $user)
    {
        $this->ensureSameCompany($user);

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->withErrors(['email' => 'Você não pode excluir seu próprio usuário.']);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    /**
     * Ativa ou desativa um usuário.
     */
    public function toggleActive(User $user)
    {
        $this->ensureSameCompany($user);

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->withErrors(['email' => 'Você não pode desativar seu próprio usuário.']);
        }

        $user->update([
            'active' => !$user->active,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Status do usuário atualizado com sucesso.');
    }

    /**
     * Garante que o usuário pertence à mesma empresa do usuário autenticado.
     */
    private function ensureSameCompany(User $user): void
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }
}