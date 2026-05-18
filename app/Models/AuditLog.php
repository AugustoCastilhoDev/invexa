<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getModelLabelAttribute(): string
    {
        $map = [
            'App\\Models\\Sale'       => 'Venda',
            'App\\Models\\Bill'       => 'Conta a Pagar',
            'App\\Models\\Receivable' => 'Conta a Receber',
            'App\\Models\\Product'    => 'Produto',
            'App\\Models\\Customer'   => 'Cliente',
            'App\\Models\\Supplier'   => 'Fornecedor',
        ];
        return $map[$this->model_type] ?? class_basename($this->model_type);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Criado',
            'updated' => 'Atualizado',
            'deleted' => 'Excluído',
            default   => ucfirst($this->action),
        };
    }
}
