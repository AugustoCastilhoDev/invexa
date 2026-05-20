<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Company extends Model
{
    use HasFactory, Billable;

    /**
     * Cashier usa billable_id por padrão.
     * Sobrescrevemos para que as queries usem o nome correto.
     */
    protected $primaryKey = 'id';

    // Diz ao Cashier qual coluna usar na tabela subscriptions
    public function subscriptions()
    {
        return $this->hasMany(\Laravel\Cashier\Subscription::class, 'billable_id')->orderBy('created_at', 'desc');
    }

    protected $fillable = [
        'name', 'email', 'phone', 'document', 'address',
        'plan', 'active', 'trial_ends_at',
    ];

    protected $casts = [
        'active'        => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    // ── Relacionamentos

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
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

    // ── Acesso / Trial

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function trialDaysLeft(): int
    {
        if (!$this->isOnTrial()) return 0;
        return (int) now()->diffInDays($this->trial_ends_at);
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function hasActiveSubscription(): bool
    {
        try {
            return $this->subscribed('default');
        } catch (\Exception $e) {
            // Tabela subscriptions ainda não existe ou coluna incorreta
            return false;
        }
    }

    /**
     * Empresa acessível = trial ainda válido OU assinatura ativa OU plano free.
     */
    public function isAccessible(): bool
    {
        if ($this->isOnTrial()) return true;
        if ($this->plan === 'free') return true;
        return $this->hasActiveSubscription();
    }

    /**
     * Sincroniza o campo `plan` local com o Price ID da assinatura Stripe.
     */
    public function syncPlanFromSubscription(): void
    {
        try {
            $sub = $this->subscription('default');
            if (!$sub || !$sub->active()) {
                $this->update(['plan' => 'free']);
                return;
            }
            $priceId = $sub->stripe_price;
            $map = [
                config('cashier.prices.pro')        => 'pro',
                config('cashier.prices.pro_launch') => 'pro',
                config('cashier.prices.business')   => 'business',
            ];
            $this->update(['plan' => $map[$priceId] ?? 'free']);
        } catch (\Exception $e) {
            // Silencia erro se tabela não existir ainda
        }
    }

    // ── Limites por plano

    public function planLimits(): array
    {
        return match ($this->plan) {
            'free'     => [
                'products'        => 50,
                'customers'       => 100,
                'suppliers'       => 10,
                'users'           => 2,
                'purchase_orders' => 20,
            ],
            'pro'      => [
                'products'        => 500,
                'customers'       => 1000,
                'suppliers'       => 100,
                'users'           => 10,
                'purchase_orders' => 200,
            ],
            'business' => [
                'products'        => PHP_INT_MAX,
                'customers'       => PHP_INT_MAX,
                'suppliers'       => PHP_INT_MAX,
                'users'           => PHP_INT_MAX,
                'purchase_orders' => PHP_INT_MAX,
            ],
            default    => [
                'products'        => 50,
                'customers'       => 100,
                'suppliers'       => 10,
                'users'           => 2,
                'purchase_orders' => 20,
            ],
        };
    }

    public function limit(string $resource): int
    {
        return $this->planLimits()[$resource] ?? 0;
    }

    public function canAdd(string $resource): bool
    {
        $limit = $this->limit($resource);
        if ($limit === PHP_INT_MAX) return true;

        return match ($resource) {
            'products'        => $this->products()->count() < $limit,
            'customers'       => $this->customers()->count() < $limit,
            'suppliers'       => $this->suppliers()->count() < $limit,
            'users'           => $this->users()->count() < $limit,
            'purchase_orders' => PurchaseOrder::where('company_id', $this->id)->count() < $limit,
            default           => false,
        };
    }

    /** @deprecated Use canAdd('users') */
    public function canAddUser(): bool
    {
        return $this->canAdd('users');
    }

    public function usagePercent(string $resource): int
    {
        $limit = $this->limit($resource);
        if ($limit === PHP_INT_MAX) return 0;

        $used = match ($resource) {
            'products'        => $this->products()->count(),
            'customers'       => $this->customers()->count(),
            'suppliers'       => $this->suppliers()->count(),
            'users'           => $this->users()->count(),
            'purchase_orders' => PurchaseOrder::where('company_id', $this->id)->count(),
            default           => 0,
        };

        return (int) min(100, round($used / $limit * 100));
    }
}
