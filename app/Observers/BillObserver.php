<?php

namespace App\Observers;

use App\Models\Bill;
use App\Models\AuditLog;

class BillObserver
{
    private function log(string $event, Bill $model): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $model->company_id,
            'action'     => $event,
            'model_type' => Bill::class,
            'model_id'   => $model->id,
            'old_values' => null,
            'new_values' => $model->getAttributes(),
        ]);
    }

    public function created(Bill $bill): void  { $this->log('created', $bill); }
    public function updated(Bill $bill): void  { $this->log('updated', $bill); }
    public function deleted(Bill $bill): void  { $this->log('deleted', $bill); }
}
