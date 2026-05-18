<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $product->company_id,
            'action'     => 'created',
            'model_type' => Product::class,
            'model_id'   => $product->id,
            'new_values' => $product->toArray(),
        ]);
    }

    public function updated(Product $product): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $product->company_id,
            'action'     => 'updated',
            'model_type' => Product::class,
            'model_id'   => $product->id,
            'old_values' => $product->getOriginal(),
            'new_values' => $product->getDirty(),
        ]);
    }

    public function deleted(Product $product): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $product->company_id,
            'action'     => 'deleted',
            'model_type' => Product::class,
            'model_id'   => $product->id,
            'old_values' => $product->toArray(),
        ]);
    }
}
