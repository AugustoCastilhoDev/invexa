<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Customer;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $customer->company_id,
            'action'     => 'created',
            'model_type' => Customer::class,
            'model_id'   => $customer->id,
            'new_values' => $customer->toArray(),
        ]);
    }

    public function updated(Customer $customer): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $customer->company_id,
            'action'     => 'updated',
            'model_type' => Customer::class,
            'model_id'   => $customer->id,
            'old_values' => $customer->getOriginal(),
            'new_values' => $customer->getDirty(),
        ]);
    }

    public function deleted(Customer $customer): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $customer->company_id,
            'action'     => 'deleted',
            'model_type' => Customer::class,
            'model_id'   => $customer->id,
            'old_values' => $customer->toArray(),
        ]);
    }
}
