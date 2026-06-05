<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'supplier_id',
        'name',
        'description',
        'sku',
        'price',
        'cost',
        'unit',
        'quantity',
        'min_quantity',
        'active',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'cost'         => 'decimal:2',
        'quantity'     => 'integer',
        'min_quantity' => 'integer',
        'active'       => 'boolean',
    ];

    // ------------------------------------
    // Accessors
    // ------------------------------------

    /** Margem de lucro em % sobre o preço de venda. */
    public function getMarginAttribute(): ?float
    {
        if (!$this->cost || !$this->price || $this->price == 0) {
            return null;
        }
        return round((($this->price - $this->cost) / $this->price) * 100, 1);
    }

    // ------------------------------------
    // Status helpers
    // ------------------------------------

    /**
     * Alerta de estoque baixo quando quantidade <= mínimo definido.
     * Se min_quantity for null ou 0, não dispara alerta.
     */
    public function isLowStock(): bool
    {
        if (empty($this->min_quantity)) {
            return false;
        }

        return $this->quantity <= $this->min_quantity;
    }

    // ------------------------------------
    // Relationships
    // ------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
