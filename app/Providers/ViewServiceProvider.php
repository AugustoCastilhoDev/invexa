<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Compartilha contador de produtos com estoque baixo em todas as views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $companyId = Auth::user()->company_id;

                $lowStockAlert = Product::where('company_id', $companyId)
                    ->where('active', true)
                    ->where('min_quantity', '>', 0)
                    ->whereColumn('quantity', '<=', 'min_quantity')
                    ->count();

                $view->with('lowStockAlert', $lowStockAlert);
            } else {
                $view->with('lowStockAlert', 0);
            }
        });
    }
}
