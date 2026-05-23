<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImport extends Model
{
    protected $fillable = [
        'company_id', 'user_id', 'filename',
        'status', 'total_rows', 'imported_rows', 'failed_rows',
        'errors', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'errors'      => 'array',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Aguardando',
            'processing' => 'Processando',
            'done'       => 'Concluído',
            'failed'     => 'Falhou',
            default      => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'secondary',
            'processing' => 'warning',
            'done'       => 'success',
            'failed'     => 'danger',
            default      => 'secondary',
        };
    }
}
