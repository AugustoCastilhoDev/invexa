<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'supplier_id',
        'purchase_order_id',
        'description',
        'amount',
        'due_date',
        'paid_at',
        'status',
        'notes',
        'installments',
        'installment_number',
        'recurrence',
        'parent_bill_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
        'amount'   => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function parentBill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'parent_bill_id');
    }

    public function installmentBills()
    {
        return $this->hasMany(Bill::class, 'parent_bill_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pendente' && $this->due_date < now();
    }
}
