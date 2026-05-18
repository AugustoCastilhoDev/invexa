<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\AuditLog;
use App\Notifications\LowStockNotification;
use App\Models\User;

class ProductObserver
{
    private function log(string $event, Product $model): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $model->company_id,
            'action'     => $event,
            'model_type' => Product::class,
            'model_id'   => $model->id,
            'old_values' => null,
            'new_values' => $model->getAttributes(),
        ]);
    }

    public function created(Product $product): void  { $this->log('created', $product); }
    public function deleted(Product $product): void  { $this->log('deleted', $product); }

    public function updated(Product $product): void
    {
        $this->log('updated', $product);

        // Dispara notificação de estoque baixo
        if ($product->isLowStock() && $product->wasChanged('quantity')) {
            User::where('company_id', $product->company_id)
                ->whereIn('role', ['admin', 'gerente'])
                ->each(fn(User $u) => $u->notify(new LowStockNotification($product)));
        }
    }
}
