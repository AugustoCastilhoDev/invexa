<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Sale $sale): bool { return $user->company_id === $sale->company_id; }
    public function create(User $user): bool   { return true; }
    public function update(User $user, Sale $sale): bool  { return $user->company_id === $sale->company_id && in_array($user->role, ['admin','gerente']); }
    public function delete(User $user, Sale $sale): bool  { return $user->company_id === $sale->company_id && in_array($user->role, ['admin','gerente']); }
    public function restore(User $user, Sale $sale): bool { return $user->company_id === $sale->company_id && in_array($user->role, ['admin','gerente']); }
    public function forceDelete(User $user, Sale $sale): bool { return $user->company_id === $sale->company_id && $user->role === 'admin'; }
}
