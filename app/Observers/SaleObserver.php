<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Sale;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'company_id'   => $sale->company_id,
            'action'       => 'created',
            'model_type'   => Sale::class,
            'model_id'     => $sale->id,
            'new_values'   => $sale->toArray(),
        ]);
    }

    public function updated(Sale $sale): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $sale->company_id,
            'action'     => 'updated',
            'model_type' => Sale::class,
            'model_id'   => $sale->id,
            'old_values' => $sale->getOriginal(),
            'new_values' => $sale->getDirty(),
        ]);
    }

    public function deleted(Sale $sale): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $sale->company_id,
            'action'     => 'deleted',
            'model_type' => Sale::class,
            'model_id'   => $sale->id,
            'old_values' => $sale->toArray(),
        ]);
    }
}
