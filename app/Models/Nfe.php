<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;

class Nfe extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany;

    protected $table = 'nfes';

    protected $fillable = [
        'company_id',
        'sale_id',
        'customer_id',
        'user_id',
        'serie',
        'numero',
        'status',
        'ambiente',
        'chave_acesso',
        'protocolo',
        'ref_focusnfe',
        'data_emissao',
        'data_autorizacao',
        'data_cancelamento',
        'valor_produtos',
        'valor_desconto',
        'valor_frete',
        'valor_total',
        'valor_icms',
        'valor_pis',
        'valor_cofins',
        'payload_enviado',
        'retorno_focusnfe',
        'xml_path',
        'danfe_path',
        'motivo_rejeicao',
        'cce_protocolo',
        'cce_correcao',
        'cce_data',
    ];

    protected $casts = [
        'data_emissao'      => 'datetime',
        'data_autorizacao'  => 'datetime',
        'data_cancelamento' => 'datetime',
        'cce_data'          => 'datetime',
        'payload_enviado'   => 'array',
        'retorno_focusnfe'  => 'array',
        'valor_produtos'    => 'decimal:2',
        'valor_desconto'    => 'decimal:2',
        'valor_frete'       => 'decimal:2',
        'valor_total'       => 'decimal:2',
        'valor_icms'        => 'decimal:2',
        'valor_pis'         => 'decimal:2',
        'valor_cofins'      => 'decimal:2',
    ];

    // ── Status constants ──────────────────────────────────────────────────────
    const STATUS_PENDENTE    = 'pendente';
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_AUTORIZADA  = 'autorizada';
    const STATUS_REJEITADA   = 'rejeitada';
    const STATUS_CANCELADA   = 'cancelada';
    const STATUS_DENEGADA    = 'denegada';

    const AMBIENTE_HOMOLOGACAO = 'homologacao';
    const AMBIENTE_PRODUCAO    = 'producao';

    // ── Relationships ─────────────────────────────────────────────────────────
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function isAutorizada(): bool
    {
        return $this->status === self::STATUS_AUTORIZADA;
    }

    public function isCancelada(): bool
    {
        return $this->status === self::STATUS_CANCELADA;
    }

    public function isPendente(): bool
    {
        return in_array($this->status, [self::STATUS_PENDENTE, self::STATUS_PROCESSANDO]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDENTE    => 'Pendente',
            self::STATUS_PROCESSANDO => 'Processando',
            self::STATUS_AUTORIZADA  => 'Autorizada',
            self::STATUS_REJEITADA   => 'Rejeitada',
            self::STATUS_CANCELADA   => 'Cancelada',
            self::STATUS_DENEGADA    => 'Denegada',
            default                  => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AUTORIZADA  => 'success',
            self::STATUS_PENDENTE,
            self::STATUS_PROCESSANDO => 'warning',
            self::STATUS_CANCELADA   => 'secondary',
            default                  => 'danger',
        };
    }

    public function getNumeroFormatadoAttribute(): string
    {
        return str_pad($this->numero, 9, '0', STR_PAD_LEFT);
    }
}
