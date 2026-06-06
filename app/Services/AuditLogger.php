<?php
namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            AuditLog::create([
                'company_id' => auth()->user()?->company_id,
                'user_id'    => auth()->id(),
                'action'     => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id'   => $model?->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Nunca deixar o audit log quebrar o fluxo principal
            logger()->error('AuditLog failed: ' . $e->getMessage());
        }
    }

    public static function created(Model $model): void
    {
        static::log('created', $model, null, $model->toArray());
    }

    public static function updated(Model $model, array $oldValues): void
    {
        static::log('updated', $model, $oldValues, $model->toArray());
    }

    public static function deleted(Model $model): void
    {
        static::log('deleted', $model, $model->toArray(), null);
    }

    public static function action(string $action, ?Model $model = null): void
    {
        static::log($action, $model);
    }
}
