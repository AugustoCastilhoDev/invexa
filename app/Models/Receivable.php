<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receivable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'sale_id',
        'description',
        'amount',
        'due_date',
        'paid_at',
        'status',
        'notes',
        'installments',
        'installment_number',
        'recurrence',
        'parent_receivable_id',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function parentReceivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class, 'parent_receivable_id');
    }

    public function installmentReceivables()
    {
        return $this->hasMany(Receivable::class, 'parent_receivable_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pendente' && $this->due_date < now();
    }
}
