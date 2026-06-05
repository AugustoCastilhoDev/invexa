<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\SaleController;
use App\Http\Controllers\Api\V1\StockController;

/*
|--------------------------------------------------------------------------
| API Routes — Invexa v1
|--------------------------------------------------------------------------
*/

// ── Autenticação (pública)
Route::prefix('v1')->name('api.')->group(function () {
    Route::post('/auth/token', [AuthController::class, 'token'])->name('auth.token');
});

// ── Rotas protegidas por Sanctum
Route::prefix('v1')->name('api.')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // Usuário autenticado
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user'    => $request->user()->only('id', 'name', 'email', 'role'),
            'company' => $request->user()->company->only('id', 'name', 'plan'),
        ]);
    })->name('me');

    Route::delete('/auth/token', [AuthController::class, 'revoke'])->name('auth.revoke');

    // Produtos
    Route::apiResource('products',  ProductController::class);

    // Clientes
    Route::apiResource('customers', CustomerController::class);

    // Vendas
    Route::get('sales',        [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('sales',       [SaleController::class, 'store'])->name('sales.store');

    // Estoque
    Route::get('stock',           [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/low',       [StockController::class, 'low'])->name('stock.low');
    Route::post('stock/movement', [StockController::class, 'movement'])->name('stock.movement');
});
