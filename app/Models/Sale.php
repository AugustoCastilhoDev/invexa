<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
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
        'sale_date' => 'datetime',
        'total'     => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'source');
    }

    // Nome de exibição: prefere o cliente cadastrado, cai no texto livre
    public function getDisplayCustomerAttribute(): string
    {
        return $this->customer?->name ?? $this->customer_name ?? '—';
    }
}
