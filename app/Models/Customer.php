<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'document',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'notes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Relacionamentos ──────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    // ── Helpers ──────────────────────────────────────────────

    public function getDocumentFormattedAttribute(): string
    {
        $doc = preg_replace('/\D/', '', $this->document ?? '');
        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }
        if (strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }
        return $this->document ?? '';
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->sales()->sum('total');
    }
}
