<?php

namespace App\Providers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use App\Observers\BillObserver;
use App\Observers\CustomerObserver;
use App\Observers\ProductObserver;
use App\Observers\ReceivableObserver;
use App\Observers\SaleObserver;
use App\Policies\BillPolicy;
use App\Policies\ReceivablePolicy;
use App\Policies\SalePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paginação: usa tema Bootstrap 5 (compatível com o layout do Invexa)
        Paginator::useBootstrapFive();

        // Observers — Audit Log ativo em todos os modelos principais
        Sale::observe(SaleObserver::class);
        Bill::observe(BillObserver::class);
        Receivable::observe(ReceivableObserver::class);
        Product::observe(ProductObserver::class);
        Customer::observe(CustomerObserver::class);

        // Policies
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Bill::class, BillPolicy::class);
        Gate::policy(Receivable::class, ReceivablePolicy::class);
    }
}
