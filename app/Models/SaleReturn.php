<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = [
        'sale_id',
        'company_id',
        'user_id',
        'total',
        'reason',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'defeito'       => 'Produto com defeito',
            'arrependimento'=> 'Arrependimento do cliente',
            'troca'         => 'Troca de produto',
            'erro_venda'    => 'Erro na venda',
            'outro'         => 'Outro motivo',
            default         => $this->reason ?? '-',
        };
    }
}
