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

// ── Autenticação (pública) ────────────────────────────
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout',[AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// ── Rotas protegidas ───────────────────────────────
Route::middleware(['auth', 'company'])->group(function () {

    // Home
    Route::get('/',     [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home.alias');

    // Dashboard
    Route::get('/dashboard',            [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

    // Perfil
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Vendas
    Route::get('/sales',        [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales',       [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}',         [SaleController::class, 'show'])->name('sales.show');
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
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // Fornecedores
    Route::resource('suppliers', SupplierController::class);

    // Ordens de Compra
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Contas a Pagar
    // ATENÇÃO: rotas estáticas (bulk-pay) devem vir ANTES do resource para não conflitar com bills.store (POST /bills)
    Route::post('/bills/bulk-pay', [BillController::class, 'bulkPay'])->name('bills.bulk-pay');
    Route::resource('bills', BillController::class);
    Route::patch('/bills/{bill}/pay',    [BillController::class, 'pay'])->name('bills.pay');
    Route::patch('/bills/{bill}/cancel', [BillController::class, 'cancel'])->name('bills.cancel');

    // Contas a Receber
    // ATENÇÃO: rota estática (bulk-receive) deve vir ANTES do resource
    Route::post('/receivables/bulk-receive', [ReceivableController::class, 'bulkReceive'])->name('receivables.bulk-receive');
    Route::resource('receivables', ReceivableController::class);
    Route::patch('/receivables/{receivable}/receive', [ReceivableController::class, 'receive'])->name('receivables.receive');
    Route::patch('/receivables/{receivable}/cancel',  [ReceivableController::class, 'cancel'])->name('receivables.cancel');

    // ── Relatórios ────────────────────────────────────
Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                  [ReportController::class, 'index'])->name('index');

        // Financeiro
        Route::get('/financial',         [ReportController::class, 'financial'])->name('financial');
        Route::get('/financial/pdf',     [ReportController::class, 'financialPdf'])->name('financial.pdf');
        Route::get('/financial/csv',     [ReportController::class, 'financialCsv'])->name('financial.csv');

        // Compras
        Route::get('/purchases',         [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/purchases/pdf',     [ReportController::class, 'purchasesPdf'])->name('purchases.pdf');
        Route::get('/purchases/csv',     [ReportController::class, 'purchasesCsv'])->name('purchases.csv');

        // Vendas
        Route::get('/sales',             [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/pdf',         [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('/sales/csv',         [ReportController::class, 'salesCsv'])->name('sales.csv');

        // Contas a Pagar
        Route::get('/bills',             [ReportController::class, 'bills'])->name('bills');
        Route::get('/bills/pdf',         [ReportController::class, 'billsPdf'])->name('bills.pdf');
        Route::get('/bills/csv',         [ReportController::class, 'billsCsv'])->name('bills.csv');

        // Estoque
        Route::get('/stock',             [ReportController::class, 'stock'])->name('stock');
        Route::get('/stock/pdf',         [ReportController::class, 'stockPdf'])->name('stock.pdf');
        Route::get('/stock/csv',         [ReportController::class, 'stockCsv'])->name('stock.csv');

        // Fornecedores
        Route::get('/suppliers',         [ReportController::class, 'suppliers'])->name('suppliers');
        Route::get('/suppliers/pdf',     [ReportController::class, 'suppliersPdf'])->name('suppliers.pdf');
        Route::get('/suppliers/csv',     [ReportController::class, 'suppliersCsv'])->name('suppliers.csv');

        // Produtos Mais Vendidos
        Route::get('/top-products',      [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/top-products/pdf',  [ReportController::class, 'topProductsPdf'])->name('top-products.pdf');
        Route::get('/top-products/csv',  [ReportController::class, 'topProductsCsv'])->name('top-products.csv');
    });

    // Usuários
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});
