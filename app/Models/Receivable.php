<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id','customer_id','sale_id','description',
        'amount','due_date','status','payment_date','payment_method',
        'notes','installment_number','installments_total',
        'recurrence','parent_receivable_id',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function company(): BelongsTo  { return $this->belongsTo(Company::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function sale(): BelongsTo     { return $this->belongsTo(Sale::class); }
    public function parent(): BelongsTo   { return $this->belongsTo(Receivable::class, 'parent_receivable_id'); }

    public function isOverdue(): bool
    {
        return $this->status === 'pendente' && $this->due_date->isPast();
    }

    public function isDueSoon(int $days = 3): bool
    {
        return $this->status === 'pendente'
            && $this->due_date->isFuture()
            && $this->due_date->diffInDays(now()) <= $days;
    }
}
