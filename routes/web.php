<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Autenticação (pública) ──────────────────────────────────────────
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// ── Rotas protegidas ────────────────────────────────────
Route::middleware(['auth', 'company'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Vendas — listagem e criação para todos os perfis
    Route::get('/sales',        [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales',       [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

    // Vendas — edição, cancelamento e lixeira somente para admin e gerente
    Route::middleware('role:admin,gerente')->group(function () {
        Route::get('/sales/{sale}/edit',    [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('/sales/{sale}',         [SaleController::class, 'update'])->name('sales.update');
        Route::patch('/sales/{sale}/cancel',[SaleController::class, 'cancel'])->name('sales.cancel');
        Route::delete('/sales/{sale}',      [SaleController::class, 'destroy'])->name('sales.destroy');
        Route::patch('/sales/{id}/restore', [SaleController::class, 'restore'])->name('sales.restore');
    });

    // Exclusão permanente — somente admin
    Route::middleware('role:admin')->group(function () {
        Route::delete('/sales/{id}/force', [SaleController::class, 'forceDestroy'])->name('sales.force-destroy');
    });

    // Devoluções
    Route::get('/returns',              [SaleReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create',       [SaleReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns',             [SaleReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/{saleReturn}', [SaleReturnController::class, 'show'])->name('returns.show');
    Route::get('/returns/{sale}/items', [SaleReturnController::class, 'getSaleItems'])->name('returns.items');

    // Estoque
    Route::get('/stock',        [StockMovementController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockMovementController::class, 'create'])->name('stock.create');
    Route::post('/stock',       [StockMovementController::class, 'store'])->name('stock.store');
    Route::delete('/stock/{stock}', [StockMovementController::class, 'destroy'])->name('stock.destroy');
    Route::get('/stock/product/{product}', [StockMovementController::class, 'product'])->name('stock.product');

    // Clientes — search ANTES do resource para não conflitar com {customer}
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // Relatórios
    Route::get('/reports',            [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [ReportController::class, 'export'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [ReportController::class, 'topProductsPdf'])->name('reports.export.pdf');

    // Fornecedores — todos leem, admin/gerente criam/editam/excluem
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::middleware('role:admin,gerente')->group(function () {
        Route::get('/suppliers/create',          [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers',                [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}',      [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}',   [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // Produtos e Categorias — somente admin e gerente
    Route::middleware('role:admin,gerente')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
    });

    // Usuários — somente admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });
});
