<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id','user_id','customer_id','number','status',
        'valid_until','subtotal','discount','total',
        'notes','converted_sale_id',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal'    => 'float',
        'discount'    => 'float',
        'total'       => 'float',
    ];

    // ── Relacionamentos ──────────────────────────────────────────────
    public function company()   { return $this->belongsTo(Company::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function customer()  { return $this->belongsTo(Customer::class); }
    public function items()     { return $this->hasMany(QuoteItem::class); }
    public function sale()      { return $this->belongsTo(Sale::class, 'converted_sale_id'); }

    // ── Helpers ───────────────────────────────────────────────────
    public function recalcTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => max(0, $subtotal - $this->discount),
        ]);
    }

    /**
     * Gera o próximo número de orçamento no formato ORC-YYYY-NNNN.
     *
     * Prefixo ex: 'ORC-2026-' = 9 caracteres.
     * SUBSTRING em MySQL é 1-based, então os dígitos começam na posição 10.
     * Usamos LENGTH($prefix)+1 para calcular dinamicamente, evitando bugs.
     */
    public static function nextNumber(int $companyId): string
    {
        $year   = now()->year;
        $prefix = 'ORC-' . $year . '-'; // ex: 'ORC-2026-' (9 chars)
        $start  = strlen($prefix) + 1;   // posição MySQL 1-based = 10

        $last = static::where('company_id', $companyId)
            ->where('number', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(number, {$start}) AS UNSIGNED)) as max_seq")
            ->value('max_seq');

        return $prefix . str_pad(($last ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast()
            && !in_array($this->status, ['accepted','converted']);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'draft'     => 'Rascunho',
            'sent'      => 'Enviado',
            'accepted'  => 'Aceito',
            'rejected'  => 'Recusado',
            'expired'   => 'Expirado',
            'converted' => 'Convertido',
            default     => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'draft'     => 'secondary',
            'sent'      => 'info',
            'accepted'  => 'success',
            'rejected'  => 'danger',
            'expired'   => 'warning',
            'converted' => 'primary',
            default     => 'secondary',
        };
    }

    public function getWhatsappUrlAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->customer?->phone ?? '');
        if (!$phone) return '#';
        $msg = urlencode(
            "Olá {$this->customer->name}, segue o orçamento *{$this->number}* "
            . "no valor de R\$ " . number_format($this->total, 2, ',', '.') . "."
            . ($this->valid_until ? " Válido até " . $this->valid_until->format('d/m/Y') . "." : '')
        );
        return "https://wa.me/{$phone}?text={$msg}";
    }
}
