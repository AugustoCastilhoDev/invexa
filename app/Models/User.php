<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'active'            => 'boolean',
    ];

    // ── Relacionamentos ──────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Helpers de papel (role) ──────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGerente(): bool
    {
        return in_array($this->role, ['admin', 'gerente']);
    }

    public function isVendedor(): bool
    {
        return in_array($this->role, ['admin', 'gerente', 'vendedor']);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role, $roles);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'    => 'Administrador',
            'gerente'  => 'Gerente',
            'vendedor' => 'Vendedor',
            default    => 'Sem perfil',
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'admin'    => 'primary',
            'gerente'  => 'info',
            'vendedor' => 'secondary',
            default    => 'secondary',
        };
    }

    // ── Inicial para avatar ──────────────────────────────────

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper($words[0][0] . $words[1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}