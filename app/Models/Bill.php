<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'supplier_id', 'description', 'amount', 'amount_paid',
        'due_date', 'paid_at', 'status', 'category', 'payment_method', 'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date'    => 'date',
        'paid_at'     => 'date',
    ];

    // ── Relacionamentos ───────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ── Labels ────────────────────────────────────────────────

    const STATUS_LABELS = [
        'pendente'  => 'Pendente',
        'paga'      => 'Paga',
        'vencida'   => 'Vencida',
        'cancelada' => 'Cancelada',
    ];

    const STATUS_COLORS = [
        'pendente'  => 'warning',
        'paga'      => 'success',
        'vencida'   => 'danger',
        'cancelada' => 'secondary',
    ];

    const CATEGORY_LABELS = [
        'fornecedor' => 'Fornecedor',
        'aluguel'    => 'Aluguel',
        'energia'    => 'Energia',
        'agua'       => 'Água',
        'internet'   => 'Internet',
        'folha'      => 'Folha de Pagamento',
        'imposto'    => 'Imposto / Tributo',
        'servico'    => 'Serviço',
        'outro'      => 'Outro',
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
        return max(0, (float) $this->amount - (float) $this->amount_paid);
    }

    // ── Verifica vencimento e sincroniza status ──────────────

    public function syncStatus(): void
    {
        if (in_array($this->status, ['paga', 'cancelada'])) {
            return;
        }
        if ($this->due_date->isPast() && $this->status === 'pendente') {
            $this->update(['status' => 'vencida']);
        }
    }

    // ── Scopes ────────────────────────────────────────────────

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
