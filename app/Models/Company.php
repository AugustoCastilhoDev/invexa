<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Relacionamentos ──────────────────────────────────────

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

    // ── Helpers ──────────────────────────────────────────────

    /** Gera slug único a partir do nome da empresa */
    public static function generateSlug(string $name): string
    {
        $slug  = Str::slug($name);
        $count = static::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /** Limites por plano */
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
