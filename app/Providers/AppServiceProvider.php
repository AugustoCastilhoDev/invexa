<?php

namespace App\Providers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\Supplier;
use App\Observers\BillObserver;
use App\Observers\ProductObserver;
use App\Observers\ReceivableObserver;
use App\Observers\SaleObserver;
use App\Policies\BillPolicy;
use App\Policies\ReceivablePolicy;
use App\Policies\SalePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Observers
        Sale::observe(SaleObserver::class);
        Product::observe(ProductObserver::class);
        Bill::observe(BillObserver::class);
        Receivable::observe(ReceivableObserver::class);

        // Policies
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Bill::class, BillPolicy::class);
        Gate::policy(Receivable::class, ReceivablePolicy::class);
    }
}
