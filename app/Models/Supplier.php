<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'trade_name',
        'document',
        'email',
        'phone',
        'contact_person',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Relacionamentos ───────────────────────────────────────

    /** Produtos fornecidos (futuro) */
    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }

    // ── Helpers ──────────────────────────────────────────────

    /** Label formatado do documento (CNPJ/CPF) */
    public function getDocumentFormattedAttribute(): string
    {
        $doc = preg_replace('/\D/', '', $this->document ?? '');

        if (strlen($doc) === 14) {
            return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $doc);
        }

        if (strlen($doc) === 11) {
            return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $doc);
        }

        return $this->document ?? '—';
    }

    /** Endereço completo em linha */
    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->state, $this->zip_code])
            ->filter()
            ->implode(', ');
    }
}
