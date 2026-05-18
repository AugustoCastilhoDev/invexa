<?php

namespace App\Observers;

use App\Models\Receivable;
use App\Models\AuditLog;

class ReceivableObserver
{
    private function log(string $event, Receivable $model): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $model->company_id,
            'action'     => $event,
            'model_type' => Receivable::class,
            'model_id'   => $model->id,
            'old_values' => null,
            'new_values' => $model->getAttributes(),
        ]);
    }

    public function created(Receivable $r): void  { $this->log('created', $r); }
    public function updated(Receivable $r): void  { $this->log('updated', $r); }
    public function deleted(Receivable $r): void  { $this->log('deleted', $r); }
}
