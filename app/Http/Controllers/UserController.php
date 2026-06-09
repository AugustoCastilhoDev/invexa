<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $company = auth()->user()->company;

        if ($company && ! $company->canAddUser()) {
            return redirect()->route('users.index')
                ->with('error', 'Limite de usuários do seu plano atingido. Faça upgrade para continuar.');
        }

        return view('users.create');
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        if ($company && ! $company->canAddUser()) {
            return redirect()->route('users.index')
                ->with('error', 'Limite de usuários do seu plano atingido. Faça upgrade para continuar.');
        }

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

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::warning('WelcomeMail não enviado para ' . $user->email . ': ' . $e->getMessage());
        }
        AuditLogger::action('user.created', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $this->ensureSameCompany($user);
        $this->blockSuperAdmin($user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureSameCompany($user);
        $this->blockSuperAdmin($user);

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
        AuditLogger::action('user.updated', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $this->ensureSameCompany($user);
        $this->blockSuperAdmin($user);

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->withErrors(['email' => 'Você não pode excluir seu próprio usuário.']);
        }

        $user->delete();
        AuditLogger::action('user.deleted', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    public function toggleActive(User $user)
    {
        $this->ensureSameCompany($user);
        $this->blockSuperAdmin($user);

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->withErrors(['email' => 'Você não pode desativar seu próprio usuário.']);
        }

        $user->update(['active' => ! $user->active]);
        AuditLogger::action('user.status_changed', $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Status do usuário atualizado com sucesso.');
    }

    // ── Guards privados ──────────────────────────────────────────

    private function ensureSameCompany(User $user): void
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    private function blockSuperAdmin(User $user): void
    {
        if ($user->isSuperAdmin()) {
            abort(403, 'Usuários Super Admin não podem ser gerenciados pelo painel de empresa.');
        }
    }
}
