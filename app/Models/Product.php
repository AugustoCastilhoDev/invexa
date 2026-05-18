<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id','category_id','supplier_id',
        'name','description','sku','price',
        'quantity','min_quantity','active',
    ];

    protected $casts = ['active' => 'boolean', 'price' => 'decimal:2'];

    public function company(): BelongsTo    { return $this->belongsTo(Company::class); }
    public function category(): BelongsTo   { return $this->belongsTo(Category::class); }
    public function supplier(): BelongsTo   { return $this->belongsTo(Supplier::class); }
    public function stockMovements(): HasMany { return $this->hasMany(StockMovement::class); }
    public function saleItems(): HasMany    { return $this->hasMany(SaleItem::class); }

    public function isLowStock(): bool
    {
        return $this->min_quantity !== null && $this->quantity <= $this->min_quantity;
    }
}
