<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeSweepTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompanyAndAdmin(string $role = 'admin'): array
    {
        $company = Company::factory()->create([
            'plan' => 'business',
            'active' => true,
            'trial_ends_at' => now()->addDays(30),
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => $role,
        ]);

        return [$company, $user];
    }

    public function test_get_routes_do_not_500()
    {
        [$company, $user] = $this->makeCompanyAndAdmin();
        $this->actingAs($user);

        $category = Category::create(['company_id' => $company->id, 'name' => 'Categoria Teste']);
        $product = Product::factory()->create(['company_id' => $company->id, 'category_id' => $category->id]);
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $supplier = Supplier::create([
            'company_id' => $company->id,
            'name' => 'Fornecedor Teste',
            'document' => '12345678000199',
            'active' => true,
        ]);
        $sale = Sale::factory()->create(['company_id' => $company->id, 'customer_id' => $customer->id]);
        $bill = Bill::factory()->create(['company_id' => $company->id]);
        $receivable = Receivable::factory()->create(['company_id' => $company->id]);

        $simpleRoutes = [
            'profile.edit', 'two-factor.index',
            'settings.company', 'settings.fiscal',
            'nfes.index',
            'sales.index', 'sales.create',
            'quotes.index', 'quotes.create',
            'customers.index', 'customers.create',
            'returns.index',
            'suppliers.index', 'suppliers.create',
            'purchase-orders.index', 'purchase-orders.create',
            'products.index', 'products.create', 'products.import',
            'categories.index', 'categories.create',
            'stock.index',
            'bills.index', 'bills.create',
            'receivables.index', 'receivables.create',
            'reports.index', 'reports.profitability', 'reports.top-products',
            'reports.purchases', 'reports.sales', 'reports.returns',
            'reports.financial', 'reports.stock', 'reports.suppliers', 'reports.bills',
            'notifications.index', 'notifications.unread',
            'company-profile.edit',
            'settings.api',
            'webhooks.index', 'webhooks.create',
            'users.index', 'users.create',
            'upgrade',
        ];

        $failures = [];

        foreach ($simpleRoutes as $name) {
            try {
                $response = $this->get(route($name));
                if ($response->status() >= 500) {
                    $failures[$name] = $response->status().' :: '.$this->shortError($response);
                }
            } catch (\Throwable $e) {
                $failures[$name] = get_class($e).': '.$e->getMessage();
            }
        }

        $withParamRoutes = [
            'sales.show' => $sale,
            'customers.show' => $customer,
            'customers.edit' => $customer,
            'suppliers.show' => $supplier,
            'suppliers.edit' => $supplier,
            'products.show' => $product,
            'products.edit' => $product,
            'categories.edit' => $category,
            'bills.show' => $bill,
            'bills.edit' => $bill,
            'receivables.show' => $receivable,
            'receivables.edit' => $receivable,
        ];

        foreach ($withParamRoutes as $name => $model) {
            try {
                $response = $this->get(route($name, $model));
                if ($response->status() >= 500) {
                    $failures[$name] = $response->status().' :: '.$this->shortError($response);
                }
            } catch (\Throwable $e) {
                $failures[$name] = get_class($e).': '.$e->getMessage();
            }
        }

        $this->assertEmpty($failures, 'GET routes failing: '.print_r($failures, true));
    }

    public function test_dashboard_does_not_500()
    {
        // CONVERT_TZ() é exclusivo do MySQL; não roda sob o driver sqlite usado por padrão nos testes.
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('Dashboard usa CONVERT_TZ(), indisponível no driver sqlite. Rodar contra MySQL para cobrir esta rota.');
        }

        [, $user] = $this->makeCompanyAndAdmin();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $this->assertLessThan(500, $response->status(), $this->shortError($response));
    }

    public function test_create_flows_for_untested_modules()
    {
        [$company, $user] = $this->makeCompanyAndAdmin();
        $this->actingAs($user);

        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $category = Category::create(['company_id' => $company->id, 'name' => 'Categoria Teste']);
        $product = Product::factory()->create(['company_id' => $company->id, 'category_id' => $category->id, 'quantity' => 50]);

        $failures = [];

        try {
            $response = $this->post(route('quotes.store'), [
                'customer_id' => $customer->id,
                'items' => [
                    ['description' => 'Item teste', 'quantity' => 1, 'unit_price' => 10, 'product_id' => $product->id],
                ],
            ]);
            if ($response->status() >= 500) $failures['quotes.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['quotes.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $response = $this->post(route('suppliers.store'), [
                'name' => 'Fornecedor Novo',
                'document' => '98765432000188',
            ]);
            if ($response->status() >= 500) $failures['suppliers.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['suppliers.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $response = $this->post(route('categories.store'), ['name' => 'Nova Categoria']);
            if ($response->status() >= 500) $failures['categories.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['categories.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $response = $this->post(route('products.store'), [
                'name' => 'Produto Novo',
                'sku' => 'SKU-NOVO-1',
                'price' => 99.90,
                'cost_price' => 50,
                'quantity' => 10,
                'min_quantity' => 1,
                'unit' => 'und',
                'category_id' => $category->id,
            ]);
            if ($response->status() >= 500) $failures['products.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['products.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $response = $this->post(route('customers.store'), [
                'name' => 'Cliente Novo',
                'email' => 'clientenovo@example.com',
            ]);
            if ($response->status() >= 500) $failures['customers.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['customers.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $supplier = Supplier::create(['company_id' => $company->id, 'name' => 'Forn PO', 'document' => '11122233000144', 'active' => true]);
            $response = $this->post(route('purchase-orders.store'), [
                'supplier_id' => $supplier->id,
                'order_date' => now()->format('Y-m-d'),
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 20],
                ],
            ]);
            if ($response->status() >= 500) $failures['purchase-orders.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['purchase-orders.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $response = $this->post(route('users.store'), [
                'name' => 'Usuario Novo',
                'email' => 'usuarionovo@example.com',
                'role' => 'vendedor',
            ]);
            if ($response->status() >= 500) $failures['users.store'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['users.store'] = get_class($e).': '.$e->getMessage();
        }

        try {
            $secret = app('pragmarx.google2fa')->generateSecretKey();
            session(['2fa_setup_secret' => $secret]);
            $code = app('pragmarx.google2fa')->getCurrentOtp($secret);
            $response = $this->post(route('two-factor.enable'), ['code' => $code]);
            if ($response->status() >= 500) $failures['two-factor.enable'] = $response->status().' :: '.$this->shortError($response);
        } catch (\Throwable $e) {
            $failures['two-factor.enable'] = get_class($e).': '.$e->getMessage();
        }

        $this->assertEmpty($failures, print_r($failures, true));
    }

    private function shortError($response): string
    {
        $content = $response->getContent();
        if (preg_match('/<title>(.*?)<\/title>/s', $content, $m)) {
            return trim(strip_tags($m[1]));
        }
        return substr(strip_tags($content), 0, 200);
    }
}
