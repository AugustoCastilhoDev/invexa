<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Bill;

class BillObserver
{
    public function created(Bill $bill): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $bill->company_id,
            'action'     => 'created',
            'model_type' => Bill::class,
            'model_id'   => $bill->id,
            'new_values' => $bill->toArray(),
        ]);
    }

    public function updated(Bill $bill): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $bill->company_id,
            'action'     => 'updated',
            'model_type' => Bill::class,
            'model_id'   => $bill->id,
            'old_values' => $bill->getOriginal(),
            'new_values' => $bill->getDirty(),
        ]);
    }

    public function deleted(Bill $bill): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $bill->company_id,
            'action'     => 'deleted',
            'model_type' => Bill::class,
            'model_id'   => $bill->id,
            'old_values' => $bill->toArray(),
        ]);
    }
}
