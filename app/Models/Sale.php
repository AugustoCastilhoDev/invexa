<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'customer_name',
        'sale_date',
        'status',
        'notes',
        'total',
    ];

    protected $casts = [
        'sale_date'  => 'datetime',
        'total'      => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    // Valor total já devolvido nesta venda
    public function getTotalReturnedAttribute(): float
    {
        return (float) $this->saleReturns()->sum('total');
    }

    // Valor líquido (total - devoluções)
    public function getNetTotalAttribute(): float
    {
        return max(0, (float) $this->total - $this->total_returned);
    }
}
