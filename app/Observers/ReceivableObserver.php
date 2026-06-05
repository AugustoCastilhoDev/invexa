<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Receivable;

class ReceivableObserver
{
    public function created(Receivable $receivable): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $receivable->company_id,
            'action'     => 'created',
            'model_type' => Receivable::class,
            'model_id'   => $receivable->id,
            'new_values' => $receivable->toArray(),
        ]);
    }

    public function updated(Receivable $receivable): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $receivable->company_id,
            'action'     => 'updated',
            'model_type' => Receivable::class,
            'model_id'   => $receivable->id,
            'old_values' => $receivable->getOriginal(),
            'new_values' => $receivable->getDirty(),
        ]);
    }

    public function deleted(Receivable $receivable): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $receivable->company_id,
            'action'     => 'deleted',
            'model_type' => Receivable::class,
            'model_id'   => $receivable->id,
            'old_values' => $receivable->toArray(),
        ]);
    }
}
