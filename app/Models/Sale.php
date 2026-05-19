<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id','customer_id','customer_name',
        'sale_date','status','notes','total',
    ];

    protected $casts = ['sale_date' => 'datetime'];

    public function company(): BelongsTo   { return $this->belongsTo(Company::class); }
    public function customer(): BelongsTo  { return $this->belongsTo(Customer::class); }
    public function items(): HasMany       { return $this->hasMany(SaleItem::class); }
    public function saleReturns(): HasMany { return $this->hasMany(SaleReturn::class); }
    public function receivable(): HasOne   { return $this->hasOne(Receivable::class); }
}
