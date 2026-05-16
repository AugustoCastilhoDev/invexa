<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'model',
        'model_id',
        'before',
        'after',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'before'     => 'array',
        'after'      => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /** Registra uma ação de auditoria */
    public static function record(
        string $action,
        ?Model $model = null,
        ?array $before = null,
        ?array $after  = null
    ): void {
        if (! auth()->check()) return;

        static::create([
            'company_id' => auth()->user()->company_id,
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model'      => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'before'     => $before,
            'after'      => $after,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}