<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'cnpj',
        'phone',
        'email',
        'logo',
        'plan',
        'active',
        'trial_ends_at',
        'plan_expires_at',
    ];

    protected $casts = [
        'active'          => 'boolean',
        'trial_ends_at'   => 'datetime',
        'plan_expires_at' => 'datetime',
    ];

    // ── Relacionamentos ────────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Trial & Plano ───────────────────────────────────────────

    /** Empresa ainda está no período de trial ativo */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at !== null
            && Carbon::now()->lessThanOrEqualTo($this->trial_ends_at);
    }

    /** Trial já expirou (e não tem plano pago ativo) */
    public function trialExpired(): bool
    {
        return $this->trial_ends_at !== null
            && Carbon::now()->greaterThan($this->trial_ends_at)
            && ! $this->hasPaidPlan();
    }

    /** Tem plano pago vigente */
    public function hasPaidPlan(): bool
    {
        if ($this->plan === 'free') {
            return false;
        }

        // Se não tem data de expiração, considera válido (vitalicio/manual)
        if ($this->plan_expires_at === null) {
            return true;
        }

        return Carbon::now()->lessThanOrEqualTo($this->plan_expires_at);
    }

    /** Empresa pode acessar o sistema (trial ativo OU plano pago vigente) */
    public function isAccessible(): bool
    {
        return $this->active && ($this->isOnTrial() || $this->hasPaidPlan());
    }

    /** Dias restantes de trial (0 se já expirou) */
    public function trialDaysLeft(): int
    {
        if (! $this->trial_ends_at) {
            return 0;
        }

        $days = (int) Carbon::now()->diffInDays($this->trial_ends_at, false);
        return max(0, $days);
    }

    // ── Limites por plano ────────────────────────────────────────

    public function limits(): array
    {
        return match ($this->plan) {
            'free'     => ['users' => 1,   'products' => 50,     'customers' => 100,    'suppliers' => 20],
            'pro'      => ['users' => 5,   'products' => 500,    'customers' => 2000,   'suppliers' => 200],
            'business' => ['users' => 999, 'products' => 999999, 'customers' => 999999, 'suppliers' => 999999],
            default    => ['users' => 1,   'products' => 50,     'customers' => 100,    'suppliers' => 20],
        };
    }

    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->limits()['users'];
    }

    public function canAddProduct(): bool
    {
        return $this->products()->count() < $this->limits()['products'];
    }

    public function canAddCustomer(): bool
    {
        return $this->customers()->count() < $this->limits()['customers'];
    }

    public function canAddSupplier(): bool
    {
        return $this->suppliers()->count() < $this->limits()['suppliers'];
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function generateSlug(string $name): string
    {
        $slug  = Str::slug($name);
        $count = static::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function getPlanLabelAttribute(): string
    {
        return match ($this->plan) {
            'free'     => 'Gratuito',
            'pro'      => 'Pro',
            'business' => 'Business',
            default    => 'Gratuito',
        };
    }
}
