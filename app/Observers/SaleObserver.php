<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\AuditLog;

class SaleObserver
{
    private function log(string $event, Sale $model, ?Sale $old = null): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $model->company_id,
            'action'     => $event,
            'model_type' => Sale::class,
            'model_id'   => $model->id,
            'old_values' => $old ? $old->getOriginal() : null,
            'new_values' => $model->getAttributes(),
        ]);
    }

    public function created(Sale $sale): void  { $this->log('created', $sale); }
    public function updated(Sale $sale): void  { $this->log('updated', $sale, $sale); }
    public function deleted(Sale $sale): void  { $this->log('deleted', $sale); }
    public function restored(Sale $sale): void { $this->log('restored', $sale); }
}
