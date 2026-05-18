<?php

namespace App\Policies;

use App\Models\Receivable;
use App\Models\User;

class ReceivablePolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Receivable $r): bool { return $user->company_id === $r->company_id; }
    public function create(User $user): bool   { return true; }
    public function update(User $user, Receivable $r): bool  { return $user->company_id === $r->company_id && in_array($user->role, ['admin','gerente']); }
    public function delete(User $user, Receivable $r): bool  { return $user->company_id === $r->company_id && in_array($user->role, ['admin','gerente']); }
}
