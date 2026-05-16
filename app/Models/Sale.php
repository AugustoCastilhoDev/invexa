<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'customer_name',
        'sale_date',
        'status',
        'notes',
        'total',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'total'     => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    /** Soma de todas as devoluções desta venda **/
    public function getTotalReturnedAttribute(): float
    {
        return (float) $this->returns->sum('total');
    }

    /** Valor líquido (venda - devoluções) **/
    public function getNetTotalAttribute(): float
    {
        return (float) $this->total - $this->total_returned;
    }
}
