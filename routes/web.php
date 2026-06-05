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
use App\Http\Controllers\QuoteController;
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
use App\Http\Controllers\UserInviteController;
use App\Http\Controllers\WebhookEndpointController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.post');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
});

// 2FA — verificação pós-login (sem auth completo, sessão temporária)
Route::get('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
Route::post('/two-factor/validate', [TwoFactorController::class, 'validateCode'])->name('two-factor.validate');

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Upgrade (antes do trial/two-factor para não bloquear pagamento)
    Route::get('/upgrade', [UpgradeController::class, 'index'])->name('upgrade');
    Route::post('/upgrade/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/upgrade/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::post('/upgrade/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Todas as rotas protegidas por 2FA + paywall
    Route::middleware(['two-factor', 'trial'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // 2FA Setup (dentro do app, já autenticado e verificado)
        Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.index');
        Route::post('/two-factor/enable', [TwoFactorController::class, 'confirm'])->name('two-factor.enable');
        Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

        // Company Settings
        Route::patch('/settings/company',       [CompanyProfileController::class, 'update'])->name('settings.company.update');
        Route::delete('/settings/company/logo', [CompanyProfileController::class, 'destroyLogo'])->name('settings.company.logo.destroy');

        // Sales
        Route::resource('sales', SaleController::class);
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::get('/sales/{sale}/nf', [SaleController::class, 'nf'])->name('sales.nf');
        Route::get('/sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');
        Route::post('/sales/{id}/restore', [SaleController::class, 'restore'])->name('sales.restore');
        Route::delete('/sales/{id}/force-destroy', [SaleController::class, 'forceDestroy'])->name('sales.force-destroy');

        // Orçamentos
        Route::resource('quotes', QuoteController::class)->except(['edit', 'update']);
        Route::patch('/quotes/{quote}/status',  [QuoteController::class, 'updateStatus'])->name('quotes.status');
        Route::post('/quotes/{quote}/convert',  [QuoteController::class, 'convert'])->name('quotes.convert');
        Route::get('/quotes/{quote}/pdf',       [QuoteController::class, 'pdf'])->name('quotes.pdf');

        // Customers
        Route::resource('customers', CustomerController::class);

        // Returns
        Route::resource('returns', SaleReturnController::class);

        // Suppliers
        Route::resource('suppliers', SupplierController::class);

        // Purchase Orders
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

        // Products & Categories
        // ATENÇÃO: rotas estáticas de /products devem vir ANTES do resource
        // para não serem interceptadas pelo binding de {product}
        Route::get('/products/import',          [ProductController::class, 'importIndex'])->name('products.import');
        Route::post('/products/import',         [ProductController::class, 'import'])->name('products.import.store');
        Route::get('/products/import/template', [ProductController::class, 'importTemplate'])->name('products.import.template');
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);

        // Stock
        Route::resource('stock', StockMovementController::class);

        // Financial
        Route::resource('bills', BillController::class);
        Route::post('/bills/{bill}/pay',         [BillController::class, 'pay'])->name('bills.pay');
        Route::post('/bills/bulk-pay',           [BillController::class, 'bulkPay'])->name('bills.bulk-pay');
        Route::post('/receivables/bulk-receive', [ReceivableController::class, 'bulkReceive'])->name('receivables.bulk-receive');
        Route::resource('receivables', ReceivableController::class);
        Route::post('/receivables/{receivable}/receive', [ReceivableController::class, 'receive'])->name('receivables.receive');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');

            Route::get('/profitability',     [ReportController::class, 'profitability'])->name('profitability');
            Route::get('/profitability/pdf', [ReportController::class, 'profitabilityPdf'])->name('profitability.pdf');
            Route::get('/profitability/csv', [ReportController::class, 'profitabilityCsv'])->name('profitability.csv');

            Route::get('/top-products',     [ReportController::class, 'topProducts'])->name('top-products');
            Route::get('/top-products/pdf', [ReportController::class, 'topProductsPdf'])->name('top-products.pdf');
            Route::get('/top-products/csv', [ReportController::class, 'topProductsCsv'])->name('top-products.csv');

            Route::get('/purchases',     [ReportController::class, 'purchases'])->name('purchases');
            Route::get('/purchases/pdf', [ReportController::class, 'purchasesPdf'])->name('purchases.pdf');
            Route::get('/purchases/csv', [ReportController::class, 'purchasesCsv'])->name('purchases.csv');

            Route::get('/sales',     [ReportController::class, 'sales'])->name('sales');
            Route::get('/sales/pdf', [ReportController::class, 'salesPdf'])->name('sales.pdf');
            Route::get('/sales/csv', [ReportController::class, 'salesCsv'])->name('sales.csv');

            Route::get('/returns',     [ReportController::class, 'returns'])->name('returns');
            Route::get('/returns/pdf', [ReportController::class, 'returnsPdf'])->name('returns.pdf');
            Route::get('/returns/csv', [ReportController::class, 'returnsCsv'])->name('returns.csv');

            Route::get('/financial',     [ReportController::class, 'financial'])->name('financial');
            Route::get('/financial/pdf', [ReportController::class, 'financialPdf'])->name('financial.pdf');
            Route::get('/financial/csv', [ReportController::class, 'financialCsv'])->name('financial.csv');

            Route::get('/stock',     [ReportController::class, 'stock'])->name('stock');
            Route::get('/stock/pdf', [ReportController::class, 'stockPdf'])->name('stock.pdf');
            Route::get('/stock/csv', [ReportController::class, 'stockCsv'])->name('stock.csv');

            Route::get('/suppliers',     [ReportController::class, 'suppliers'])->name('suppliers');
            Route::get('/suppliers/pdf', [ReportController::class, 'suppliersPdf'])->name('suppliers.pdf');
            Route::get('/suppliers/csv', [ReportController::class, 'suppliersCsv'])->name('suppliers.csv');

            Route::get('/bills',     [ReportController::class, 'bills'])->name('bills');
            Route::get('/bills/pdf', [ReportController::class, 'billsPdf'])->name('bills.pdf');
            Route::get('/bills/csv', [ReportController::class, 'billsCsv'])->name('bills.csv');
        });

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

        // Company Profile
        Route::get('/company-profile', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
        Route::put('/company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');

        // API Tokens
        Route::get('/settings/api',                   [ApiTokenController::class, 'index'])->name('settings.api');
        Route::post('/settings/api/tokens',           [ApiTokenController::class, 'store'])->name('settings.api.tokens.store');
        Route::delete('/settings/api/tokens/{token}', [ApiTokenController::class, 'destroy'])->name('settings.api.tokens.destroy');

        // Webhooks
        Route::resource('webhooks', WebhookEndpointController::class);

        // Users
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/resend-invite',  [UserInviteController::class, 'resend'])->name('users.resend-invite');
        Route::post('/users/{user}/invite',         [UserInviteController::class, 'send'])->name('users.invite.send');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // Sair do modo suporte
        Route::post('/superadmin/leave-impersonate', [SuperAdminController::class, 'leaveImpersonate'])->name('admin.leave-impersonate');

        // Super Admin
        Route::middleware('superadmin')->prefix('superadmin')->name('admin.')->group(function () {
            Route::get('/',          [SuperAdminController::class, 'index'])->name('index');
            Route::get('/users',     [SuperAdminController::class, 'users'])->name('users');
            Route::get('/plans',     [SuperAdminController::class, 'plans'])->name('plans');

            Route::post('/companies/{company}/impersonate', [SuperAdminController::class, 'impersonate'])->name('companies.impersonate');
            Route::patch('/companies/{company}/toggle',     [SuperAdminController::class, 'toggleCompany'])->name('companies.toggle');
            Route::patch('/companies/{company}/plan',       [SuperAdminController::class, 'changePlan'])->name('companies.plan');
            Route::delete('/companies/{company}',           [SuperAdminController::class, 'destroyCompany'])->name('companies.destroy');
        });

    }); // end middleware(['two-factor', 'trial'])

}); // end middleware('auth')

// Stripe Webhook (sem auth)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');
