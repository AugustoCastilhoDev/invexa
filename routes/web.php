<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Autenticação (pública) ────────────────────────────────────────
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// ── Rotas protegidas ────────────────────────────────────────────
Route::middleware(['auth', 'company'])->group(function () {

    // ── Home (página inicial pós-login) ──
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home.alias');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Vendas
    Route::get('/sales',        [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales',       [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('/sales/{sale}/pdf',     [SaleController::class, 'pdf'])->name('sales.pdf');
    Route::middleware('role:admin,gerente')->group(function () {
        Route::get('/sales/{sale}/edit',     [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('/sales/{sale}',          [SaleController::class, 'update'])->name('sales.update');
        Route::patch('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::delete('/sales/{sale}',       [SaleController::class, 'destroy'])->name('sales.destroy');
        Route::patch('/sales/{id}/restore',  [SaleController::class, 'restore'])->name('sales.restore');
    });
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

    // Produtos
    Route::resource('products', ProductController::class);

    // Categorias
    Route::resource('categories', CategoryController::class);

    // Clientes
    Route::resource('customers', CustomerController::class);

    // Fornecedores
    Route::resource('suppliers', SupplierController::class);

    // Ordens de Compra
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Contas a Pagar
    Route::resource('bills', BillController::class);
    Route::patch('/bills/{bill}/pay',            [BillController::class, 'pay'])->name('bills.pay');
    Route::post('/bills/bulk-pay',               [BillController::class, 'bulkPay'])->name('bills.bulk-pay');
    Route::patch('/bills/{bill}/cancel',         [BillController::class, 'cancel'])->name('bills.cancel');

    // Contas a Receber
    Route::resource('receivables', ReceivableController::class);
    Route::patch('/receivables/{receivable}/receive',   [ReceivableController::class, 'receive'])->name('receivables.receive');
    Route::post('/receivables/bulk-receive',            [ReceivableController::class, 'bulkReceive'])->name('receivables.bulk-receive');
    Route::patch('/receivables/{receivable}/cancel',    [ReceivableController::class, 'cancel'])->name('receivables.cancel');

    // Relatórios
    Route::get('/reports',                [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/financial',      [ReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/purchases',      [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/purchases/pdf',  [ReportController::class, 'purchasesPdf'])->name('reports.purchases.pdf');
    Route::get('/reports/purchases/csv',  [ReportController::class, 'purchasesCsv'])->name('reports.purchases.csv');

    // Usuários
    Route::resource('users', UserController::class);

});
