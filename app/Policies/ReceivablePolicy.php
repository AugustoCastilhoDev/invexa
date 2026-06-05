<?php

namespace App\Policies;

use App\Models\Receivable;
use App\Models\User;

class ReceivablePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'gerente', 'financeiro', 'vendedor']);
    }

    public function view(User $user, Receivable $receivable): bool
    {
        return $user->company_id === $receivable->company_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'gerente', 'financeiro']);
    }

    public function update(User $user, Receivable $receivable): bool
    {
        return $user->company_id === $receivable->company_id
            && in_array($user->role, ['admin', 'gerente', 'financeiro']);
    }

    public function delete(User $user, Receivable $receivable): bool
    {
        return $user->company_id === $receivable->company_id
            && in_array($user->role, ['admin', 'gerente']);
    }
}
