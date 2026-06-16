<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'sale_number', 'customer_id', 'customer_name',
        'sale_date', 'status', 'notes', 'total',
        'pix_charge_id', 'pix_payload', 'pix_qrcode_image', 'pix_expires_at', 'pix_paid_at',
    ];

    protected $casts = [
        'sale_date'      => 'datetime',
        'pix_expires_at' => 'datetime',
        'pix_paid_at'    => 'datetime',
    ];

    /**
     * Isola automaticamente todas as queries pelo company_id do usuário autenticado.
     * Use Sale::withoutGlobalScope('company') quando precisar de acesso irrestrito.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where('sales.company_id', auth()->user()->company_id);
            }
        });
    }

    public function company(): BelongsTo   { return $this->belongsTo(Company::class); }
    public function customer(): BelongsTo  { return $this->belongsTo(Customer::class); }
    public function items(): HasMany       { return $this->hasMany(SaleItem::class); }
    public function saleReturns(): HasMany { return $this->hasMany(SaleReturn::class); }
    public function receivable(): HasOne   { return $this->hasOne(Receivable::class); }
    public function nfes(): HasMany        { return $this->hasMany(Nfe::class); }

    // Helpers financeiros
    public function getTotalReturnedAttribute(): float
    {
        return $this->saleReturns->sum('total');
    }

    public function getNetTotalAttribute(): float
    {
        return $this->total - $this->total_returned;
    }
}
