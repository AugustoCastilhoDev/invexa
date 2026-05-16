<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'company_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'notes',
        'source_type',
        'source_id',
    ];

    protected $casts = [
        'quantity'        => 'integer',
        'quantity_before' => 'integer',
        'quantity_after'  => 'integer',
    ];

    // ── Relacionamentos ──────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    // ── Helpers ──────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'entrada' => 'Entrada',
            'saida'   => 'Saída',
            'ajuste'  => 'Ajuste',
            default   => ucfirst($this->type),
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'entrada' => 'success',
            'saida'   => 'danger',
            'ajuste'  => 'warning',
            default   => 'secondary',
        };
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'compra'      => 'Compra / Reposição',
            'devolucao'   => 'Devolução de cliente',
            'ajuste'      => 'Ajuste de inventário',
            'venda'       => 'Venda',
            'perda'       => 'Perda / Avaria',
            'transferencia' => 'Transferência',
            default       => $this->reason ?? '-',
        };
    }
}
