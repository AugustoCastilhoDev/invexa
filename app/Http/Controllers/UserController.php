<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('company_id', auth()->user()->company_id)
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'ativo');
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', Rule::in(['admin', 'gerente', 'vendedor'])],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'password'   => $request->password,
            'company_id' => auth()->user()->company_id,
            'active'     => $request->boolean('active', true),
        ]);

        // Envia e-mail de boas-vindas ao novo usuário
        Mail::to($user->email)->send(new WelcomeMail($user));

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $this->ensureSameCompany($user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureSameCompany($user);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role'     => ['required', Rule::in(['admin', 'gerente', 'vendedor'])],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $request->role,
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

    public function toggleActive(User $user)
    {
        $this->ensureSameCompany($user);

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->withErrors(['email' => 'Você não pode desativar seu próprio usuário.']);
        }

        $user->update(['active' => ! $user->active]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Status do usuário atualizado com sucesso.');
    }

    private function ensureSameCompany(User $user): void
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }
}
