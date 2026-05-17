<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'supplier_id', 'user_id',
        'number', 'status', 'expected_date', 'received_at',
        'total', 'notes',
    ];

    protected $casts = [
        'total'         => 'decimal:2',
        'expected_date' => 'date',
        'received_at'   => 'date',
    ];

    // ── Status helpers ───────────────────────────────────────

    const STATUS_LABELS = [
        'rascunho'          => 'Rascunho',
        'enviada'           => 'Enviada',
        'recebida_parcial'  => 'Recebida Parcialmente',
        'recebida'          => 'Recebida',
        'cancelada'         => 'Cancelada',
    ];

    const STATUS_COLORS = [
        'rascunho'          => 'secondary',
        'enviada'           => 'primary',
        'recebida_parcial'  => 'warning',
        'recebida'          => 'success',
        'cancelada'         => 'danger',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function canReceive(): bool
    {
        return in_array($this->status, ['enviada', 'recebida_parcial']);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['rascunho', 'enviada']);
    }

    public function canSend(): bool
    {
        return $this->status === 'rascunho';
    }

    // ── Relacionamentos ───────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // ── Número automático ───────────────────────────────────────

    public static function nextNumber(int $companyId): string
    {
        $last = self::where('company_id', $companyId)->max('id') ?? 0;
        return 'OC-' . str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    }
}
