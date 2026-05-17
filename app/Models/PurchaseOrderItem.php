<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id',
        'quantity', 'quantity_received', 'unit_cost', 'subtotal',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'subtotal'  => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /** Quantidade pendente de recebimento */
    public function getPendingAttribute(): int
    {
        return max(0, $this->quantity - $this->quantity_received);
    }
}
