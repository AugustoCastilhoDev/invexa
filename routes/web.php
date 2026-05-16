<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Autenticação (guest) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Área autenticada ────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

    // Produtos
    Route::resource('products', ProductController::class);

    // Categorias
    Route::resource('categories', CategoryController::class);

    // Clientes
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // Vendas
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

    // Devoluções
    Route::resource('returns', SaleReturnController::class);
    Route::get('/returns/{saleReturn}/items', [SaleReturnController::class, 'getItems'])->name('returns.items');

    // Estoque
    Route::resource('stock', StockMovementController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/stock/product/{product}', [StockMovementController::class, 'product'])->name('stock.product');

    // Relatórios
    Route::get('/reports',                  [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export',           [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/top-products',     [ReportController::class, 'topProducts'])->name('reports.top-products');
    Route::get('/reports/top-products/csv', [ReportController::class, 'topProductsCsv'])->name('reports.top-products.csv');
    Route::get('/reports/top-products/pdf', [ReportController::class, 'topProductsPdf'])->name('reports.top-products.pdf');

    // Usuários (admin)
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});
