<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'price',
        'cost',
        'quantity',
        'min_quantity',
        'unit',
        'category_id',
        'active',
        'company_id',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'cost'         => 'decimal:2',
        'quantity'     => 'integer',
        'min_quantity' => 'integer',
        'active'       => 'boolean',
    ];

    // ── Relacionamentos ──────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // ── Helpers ──────────────────────────────────────────────

    /** Verifica se o estoque está abaixo do mínimo */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }

    /** Margem de lucro em percentual */
    public function getMarginAttribute(): float
    {
        if (! $this->cost || $this->cost == 0) return 0;
        return round((($this->price - $this->cost) / $this->price) * 100, 2);
    }
}