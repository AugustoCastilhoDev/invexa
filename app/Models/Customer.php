<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'document',
        'cpf_cnpj',
        'address',
        'city',
        'state',
        // Endereço estruturado (NF-e)
        'cep',
        'logradouro',
        'numero_endereco',
        'complemento',
        'bairro',
        'municipio',
        'uf',
        'codigo_municipio',
        'ie_destinatario',
        'tipo_pessoa',
        'indicador_ie',
        'active',
        'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ------------------------------------
    // Scopes
    // ------------------------------------

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    // ------------------------------------
    // Relationships
    // ------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }
}
