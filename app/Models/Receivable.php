<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'customer_id', 'sale_id', 'description',
        'amount', 'amount_received', 'due_date', 'received_at',
        'status', 'category', 'payment_method', 'notes',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'amount_received' => 'decimal:2',
        'due_date'        => 'date',
        'received_at'     => 'date',
    ];

    // ── Relacionamentos ────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // ── Labels ─────────────────────────────────────────

    const STATUS_LABELS = [
        'pendente'  => 'Pendente',
        'recebida'  => 'Recebida',
        'vencida'   => 'Vencida',
        'cancelada' => 'Cancelada',
    ];

    const STATUS_COLORS = [
        'pendente'  => 'warning',
        'recebida'  => 'success',
        'vencida'   => 'danger',
        'cancelada' => 'secondary',
    ];

    const CATEGORY_LABELS = [
        'venda'       => 'Venda',
        'servico'     => 'Serviço',
        'mensalidade' => 'Mensalidade',
        'acordo'      => 'Acordo / Negociação',
        'outro'       => 'Outro',
    ];

    const PAYMENT_METHODS = [
        'pix'            => 'PIX',
        'boleto'         => 'Boleto',
        'transferencia'  => 'Transferência',
        'cartao_debito'  => 'Cartão de Débito',
        'cartao_credito' => 'Cartão de Crédito',
        'dinheiro'       => 'Dinheiro',
        'outro'          => 'Outro',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? ucfirst($this->category);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? ($this->payment_method ?? '-');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->amount_received);
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pendente', 'vencida']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'vencida');
    }
}
