<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_LABELS = [
        'pendente'  => 'Pendente',
        'paga'      => 'Paga',
        'vencida'   => 'Vencida',
        'cancelada' => 'Cancelada',
    ];

    const CATEGORY_LABELS = [
        'fornecedor'  => 'Fornecedor',
        'aluguel'     => 'Aluguel',
        'utilidades'  => 'Utilidades (Água/Luz/Internet)',
        'folha'       => 'Folha de Pagamento',
        'impostos'    => 'Impostos e Taxas',
        'manutencao'  => 'Manutenção',
        'marketing'   => 'Marketing',
        'outros'      => 'Outros',
    ];

    const PAYMENT_METHODS = [
        'dinheiro'       => 'Dinheiro',
        'pix'            => 'PIX',
        'cartao_credito' => 'Cartão de Crédito',
        'cartao_debito'  => 'Cartão de Débito',
        'boleto'         => 'Boleto',
        'transferencia'  => 'Transferência Bancária',
        'cheque'         => 'Cheque',
    ];

    // Mantido por compatibilidade retroativa
    const CATEGORIES = self::CATEGORY_LABELS;

    protected $fillable = [
        'company_id',
        'supplier_id',
        'purchase_order_id',
        'description',
        'category',
        'amount',
        'amount_paid',
        'payment_method',
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
        'due_date'    => 'date',
        'paid_at'     => 'datetime',
        'amount'      => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    // ── Accessors ─────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendente'  => 'warning',
            'paga'      => 'success',
            'vencida'   => 'danger',
            'cancelada' => 'secondary',
            default     => 'secondary',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? ucfirst($this->category ?? 'Outros');
    }

    // ── Relationships ──────────────────────────────────────────

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

    // ── Helpers ────────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->status === 'pendente' && $this->due_date < now();
    }
}
