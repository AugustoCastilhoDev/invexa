<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Landing Page (pública)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ── Pricing (pública)
Route::get('/pricing', fn () => view('pricing'))->name('pricing');

// ── Webhook Stripe (sem CSRF, sem auth)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

// ── Autenticação (pública)
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('login.post');
Route::post('/logout',[AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('register.store');

// ── 2FA — verificação pós-login (pública, sem auth)
Route::get('/two-factor/verify',    [TwoFactorController::class, 'verify'])->name('two-factor.verify');
Route::post('/two-factor/validate', [TwoFactorController::class, 'validateCode'])->name('two-factor.validate')->middleware('throttle:10,1');

// ── Onboarding (autenticado, sem trial check)
Route::middleware(['auth', 'company'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/',     [OnboardingController::class, 'show'])->name('show');
    Route::post('/',    [OnboardingController::class, 'store'])->name('store');
    Route::post('/skip',[OnboardingController::class, 'skip'])->name('skip');
});

// ── Super-Admin
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                                    [SuperAdminController::class, 'index'])->name('index');
    Route::patch('/companies/{company}/toggle',        [SuperAdminController::class, 'toggleCompany'])->name('companies.toggle');
    Route::post('/companies/{company}/impersonate',    [SuperAdminController::class, 'impersonate'])->name('companies.impersonate');
    Route::delete('/companies/{company}',              [SuperAdminController::class, 'destroyCompany'])->name('companies.destroy');
});

// ── Sair do modo suporte
Route::post('/admin/leave-impersonate', [SuperAdminController::class, 'leaveImpersonate'])
    ->middleware('auth')
    ->name('admin.leave-impersonate');

// ── Upgrade (autenticado, fora do trial check)
Route::middleware(['auth', 'company'])->group(function () {
    Route::get('/upgrade', [UpgradeController::class, 'index'])->name('upgrade');
});

// ── Assinatura (autenticado, fora do trial check)
Route::middleware(['auth', 'company'])->prefix('settings')->name('subscription.')->group(function () {
    Route::get('/subscription',                        [SubscriptionController::class, 'index'])->name('index');
    Route::post('/subscription/checkout',              [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::get('/subscription/success',                [SubscriptionController::class, 'success'])->name('success');
    Route::get('/subscription/portal',                 [SubscriptionController::class, 'billingPortal'])->name('billing-portal');
    Route::delete('/subscription/cancel',              [SubscriptionController::class, 'cancel'])->name('cancel');
    Route::get('/subscription/checkout/redirect',      [SubscriptionController::class, 'checkoutRedirect'])->name('checkout.redirect');
    Route::get('/subscription/invoice/{invoice}', function (string $invoice) {
        return auth()->user()->company->downloadInvoice($invoice);
    })->name('invoice');
});

// ── 2FA — configurações (autenticado)
Route::middleware(['auth', 'company'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/security',            [TwoFactorController::class, 'show'])->name('two-factor');
    Route::post('/security/confirm',   [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/security/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

// ── Tokens de API
Route::middleware(['auth', 'company'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/api',                        [ApiTokenController::class, 'index'])->name('api');
    Route::post('/api/tokens',                [ApiTokenController::class, 'store'])->name('api.tokens.store');
    Route::delete('/api/tokens/{tokenId}',    [ApiTokenController::class, 'destroy'])->name('api.tokens.destroy');
});

// ── Perfil da Empresa (autenticado, apenas admin)
Route::middleware(['auth', 'company', 'role:admin'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/company',         [CompanyProfileController::class, 'edit'])->name('company.edit');
    Route::patch('/company',       [CompanyProfileController::class, 'update'])->name('company.update');
    Route::delete('/company/logo', [CompanyProfileController::class, 'destroyLogo'])->name('company.logo.destroy');
});

// ── Protegidas (requer trial/plano ativo)
Route::middleware(['auth', 'company', 'trial', 'onboarding'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard',            [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ── Notificações
    Route::get('/notifications',                       [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread',                [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/mark-all-read',        [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/read',            [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}',               [NotificationController::class, 'destroy'])->name('notifications.destroy');

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

    Route::get('/returns',              [SaleReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create',       [SaleReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns',             [SaleReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/{saleReturn}', [SaleReturnController::class, 'show'])->name('returns.show');
    Route::get('/returns/{sale}/items', [SaleReturnController::class, 'getSaleItems'])->name('returns.items');

    Route::get('/stock',        [StockMovementController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockMovementController::class, 'create'])->name('stock.create');
    Route::post('/stock',       [StockMovementController::class, 'store'])->name('stock.store');
    Route::delete('/stock/{stock}', [StockMovementController::class, 'destroy'])->name('stock.destroy');

    Route::resource('products', ProductController::class);
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/import/template', [ProductController::class, 'importTemplate'])->name('products.import.template');
    Route::resource('categories', CategoryController::class);

    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    Route::post('/bills/bulk-pay', [BillController::class, 'bulkPay'])->name('bills.bulk-pay');
    Route::resource('bills', BillController::class);
    Route::patch('/bills/{bill}/pay',    [BillController::class, 'pay'])->name('bills.pay');
    Route::patch('/bills/{bill}/cancel', [BillController::class, 'cancel'])->name('bills.cancel');

    Route::post('/receivables/bulk-receive', [ReceivableController::class, 'bulkReceive'])->name('receivables.bulk-receive');
    Route::resource('receivables', ReceivableController::class);
    Route::patch('/receivables/{receivable}/receive', [ReceivableController::class, 'receive'])->name('receivables.receive');
    Route::patch('/receivables/{receivable}/cancel',  [ReceivableController::class, 'cancel'])->name('receivables.cancel');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                        [ReportController::class, 'index'])->name('index');
        Route::get('/financial',               [ReportController::class, 'financial'])->name('financial');
        Route::get('/financial/pdf',           [ReportController::class, 'financialPdf'])->name('financial.pdf');
        Route::get('/financial/csv',           [ReportController::class, 'financialCsv'])->name('financial.csv');
        Route::get('/purchases',               [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/purchases/pdf',           [ReportController::class, 'purchasesPdf'])->name('purchases.pdf');
        Route::get('/purchases/csv',           [ReportController::class, 'purchasesCsv'])->name('purchases.csv');
        Route::get('/sales',                   [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/pdf',               [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('/sales/csv',               [ReportController::class, 'salesCsv'])->name('sales.csv');
        Route::get('/bills',                   [ReportController::class, 'bills'])->name('bills');
        Route::get('/bills/pdf',               [ReportController::class, 'billsPdf'])->name('bills.pdf');
        Route::get('/bills/csv',               [ReportController::class, 'billsCsv'])->name('bills.csv');
        Route::get('/stock',                   [ReportController::class, 'stock'])->name('stock');
        Route::get('/stock/pdf',               [ReportController::class, 'stockPdf'])->name('stock.pdf');
        Route::get('/stock/csv',               [ReportController::class, 'stockCsv'])->name('stock.csv');
        Route::get('/suppliers',               [ReportController::class, 'suppliers'])->name('suppliers');
        Route::get('/suppliers/pdf',           [ReportController::class, 'suppliersPdf'])->name('suppliers.pdf');
        Route::get('/suppliers/csv',           [ReportController::class, 'suppliersCsv'])->name('suppliers.csv');
        Route::get('/top-products',            [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/top-products/pdf',        [ReportController::class, 'topProductsPdf'])->name('top-products.pdf');
        Route::get('/top-products/csv',        [ReportController::class, 'topProductsCsv'])->name('top-products.csv');
        Route::get('/returns',                 [ReportController::class, 'returns'])->name('returns');
        Route::get('/returns/pdf',             [ReportController::class, 'returnsPdf'])->name('returns.pdf');
        Route::get('/returns/csv',             [ReportController::class, 'returnsCsv'])->name('returns.csv');
        // ── Lucratividade
        Route::get('/profitability',           [ReportController::class, 'profitability'])->name('profitability');
        Route::get('/profitability/pdf',       [ReportController::class, 'profitabilityPdf'])->name('profitability.pdf');
        Route::get('/profitability/csv',       [ReportController::class, 'profitabilityCsv'])->name('profitability.csv');
    });

    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});
