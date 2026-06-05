<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;

class BillPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'gerente', 'financeiro']);
    }

    public function view(User $user, Bill $bill): bool
    {
        return $user->company_id === $bill->company_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'gerente', 'financeiro']);
    }

    public function update(User $user, Bill $bill): bool
    {
        return $user->company_id === $bill->company_id
            && in_array($user->role, ['admin', 'gerente', 'financeiro']);
    }

    public function delete(User $user, Bill $bill): bool
    {
        return $user->company_id === $bill->company_id
            && in_array($user->role, ['admin', 'gerente']);
    }
}
