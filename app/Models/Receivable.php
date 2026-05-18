<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receivable extends Model
{
    use HasFactory, SoftDeletes;

    const PAYMENT_METHODS = [
        'dinheiro'        => 'Dinheiro',
        'pix'             => 'PIX',
        'cartao_credito'  => 'Cartão de Crédito',
        'cartao_debito'   => 'Cartão de Débito',
        'boleto'          => 'Boleto',
        'transferencia'   => 'Transferência Bancária',
        'cheque'          => 'Cheque',
    ];

    const CATEGORIES = [
        'venda'           => 'Venda',
        'servico'         => 'Serviço Prestado',
        'aluguel'         => 'Aluguel',
        'comissao'        => 'Comissão',
        'reembolso'       => 'Reembolso',
        'outros'          => 'Outros',
    ];

    protected $fillable = [
        'company_id',
        'customer_id',
        'sale_id',
        'description',
        'category',
        'amount',
        'amount_received',
        'payment_method',
        'due_date',
        'received_at',
        'paid_at',
        'status',
        'notes',
        'installments',
        'installment_number',
        'recurrence',
        'parent_receivable_id',
    ];

    protected $casts = [
        'due_date'        => 'date',
        'paid_at'         => 'datetime',
        'received_at'     => 'datetime',
        'amount'          => 'decimal:2',
        'amount_received' => 'decimal:2',
    ];

    // ── Accessors ─────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'  => 'Pendente',
            'recebida'  => 'Recebida',
            'vencida'   => 'Vencida',
            'cancelada' => 'Cancelada',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendente'  => 'warning',
            'recebida'  => 'success',
            'vencida'   => 'danger',
            'cancelada' => 'secondary',
            default     => 'secondary',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category ?? 'Outros');
    }

    // ── Relationships ──────────────────────────────────────────

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

    // ── Helpers ────────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->status === 'pendente' && $this->due_date < now();
    }
}
