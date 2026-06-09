<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    const STATUS_LABELS = [
        'pendente'  => 'Pendente',
        'enviada'   => 'Enviada',
        'recebida'  => 'Recebida',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable = [
        'company_id', 'supplier_id', 'user_id',
        'order_date', 'expected_date', 'received_at',
        'status', 'total', 'notes', 'number',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'expected_date' => 'date',
        'received_at'   => 'datetime',
        'total'         => 'decimal:2',
    ];

    // ------------------------------------
    // Auto-generate order number on create
    // ------------------------------------
    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->number)) {
                $year  = now()->format('Y');
                $companyId = $order->company_id ?? auth()->user()?->company_id;
                $last = static::withTrashed()->whereYear('created_at', $year)->where('company_id', $companyId)->max('number');
                $count = $last ? ((int) substr($last, -4)) + 1 : 1;
                // fallback: conta registros se max falhar
                if ($count === 1 && static::whereYear('created_at', $year)->where('company_id', $companyId)->exists()) {
                    $count = static::whereYear('created_at', $year)->where('company_id', $companyId)->count() + 1;
                }
                $order->number = 'OC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
            if (empty($order->user_id)) {
                $order->user_id = auth()->id();
            }
        });
    }

    // ------------------------------------
    // Accessors
    // ------------------------------------
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendente'  => 'warning',
            'enviada'   => 'info',
            'recebida'  => 'success',
            'cancelada' => 'danger',
            default     => 'secondary',
        };
    }

    // ------------------------------------
    // Status helpers
    // ------------------------------------

    /** Pode ser enviada ao fornecedor: apenas ordens pendentes. */
    public function canSend(): bool
    {
        return $this->status === 'pendente';
    }

    /** Pode registrar recebimento: ordens pendentes ou enviadas. */
    public function canReceive(): bool
    {
        return in_array($this->status, ['pendente', 'enviada']);
    }

    /** Pode ser cancelada: ordens pendentes ou enviadas. */
    public function canCancel(): bool
    {
        return in_array($this->status, ['pendente', 'enviada']);
    }

    // ------------------------------------
    // Relationships
    // ------------------------------------
    public function company(): BelongsTo  { return $this->belongsTo(Company::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function items(): HasMany      { return $this->hasMany(PurchaseOrderItem::class); }
    public function bill(): HasOne        { return $this->hasOne(Bill::class); }
}
