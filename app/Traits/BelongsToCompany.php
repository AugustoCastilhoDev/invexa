<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Adicione este trait em Product, Category, Sale (e qualquer model
 * que deva ser isolado por empresa).
 *
 * O que ele faz automaticamente:
 *  - Filtra todas as queries pelo company_id do usuário logado
 *  - Preenche company_id ao criar um novo registro
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        // ── Escopo global: filtra por company_id em TODA query ──
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where(
                    (new static)->getTable() . '.company_id',
                    auth()->user()->company_id
                );
            }
        });

        // ── Preenche company_id automaticamente ao criar ──
        static::creating(function ($model) {
            if (auth()->check() && empty($model->company_id)) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    // ── Relacionamento reverso ───────────────────────────────

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    // ── Scope manual (para usar sem o global scope) ──────────

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->withoutGlobalScope('company')
                     ->where($this->getTable() . '.company_id', $companyId);
    }
}