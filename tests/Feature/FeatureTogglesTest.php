<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureTogglesTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(array $companyAttrs = []): array
    {
        $company = Company::factory()->create(array_merge([
            'plan' => 'business',
            'active' => true,
            'trial_ends_at' => now()->addDays(30),
        ], $companyAttrs));

        $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin']);

        return [$company, $user];
    }

    public function test_nfe_and_fiscal_routes_are_disabled_by_default()
    {
        config(['features.nfe_enabled' => false]);
        [$company, $admin] = $this->makeAdmin();

        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $sale = Sale::factory()->create(['company_id' => $company->id, 'customer_id' => $customer->id]);

        $this->actingAs($admin)->get(route('settings.fiscal'))->assertNotFound();
        $this->actingAs($admin)->get(route('nfes.index'))->assertNotFound();
        $this->actingAs($admin)->post(route('nfes.emitir', $sale))->assertNotFound();
    }

    public function test_nfe_and_fiscal_routes_work_when_flag_enabled()
    {
        config(['features.nfe_enabled' => true]);
        [, $admin] = $this->makeAdmin();

        $this->actingAs($admin)->get(route('settings.fiscal'))->assertOk();
        $this->actingAs($admin)->get(route('nfes.index'))->assertOk();
    }

    public function test_sale_show_hides_emitir_nfe_button_when_disabled()
    {
        config(['features.nfe_enabled' => false]);
        [$company, $admin] = $this->makeAdmin();
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $sale = Sale::factory()->create(['company_id' => $company->id, 'customer_id' => $customer->id, 'status' => 'pendente']);

        $response = $this->actingAs($admin)->get(route('sales.show', $sale));
        $response->assertOk();
        $response->assertDontSee('Emitir NF-e');
    }

    public function test_asaas_settings_route_is_disabled_by_default()
    {
        config(['features.pix_enabled' => false]);
        [, $admin] = $this->makeAdmin();

        $this->actingAs($admin)->post(route('settings.asaas.update'), [
            'asaas_environment' => 'sandbox',
        ])->assertNotFound();
    }

    public function test_asaas_settings_route_works_when_flag_enabled()
    {
        config(['features.pix_enabled' => true]);
        [, $admin] = $this->makeAdmin();

        $this->actingAs($admin)->post(route('settings.asaas.update'), [
            'asaas_environment' => 'sandbox',
        ])->assertRedirect();
    }

    public function test_sale_creation_never_generates_pix_charge_when_disabled_even_if_company_has_key()
    {
        config(['features.pix_enabled' => false]);
        [$company, $admin] = $this->makeAdmin(['asaas_api_key' => encrypt('fake-key')]);
        $customer = Customer::factory()->create(['company_id' => $company->id]);
        $product = Product::factory()->create(['company_id' => $company->id, 'quantity' => 10]);

        $response = $this->actingAs($admin)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'status' => 'pendente',
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'price' => 10],
            ],
        ]);

        $sale = Sale::where('company_id', $company->id)->latest()->first();
        $this->assertNotNull($sale);
        $this->assertNull($sale->pix_charge_id);
    }

    public function test_company_has_asaas_configured_respects_feature_flag()
    {
        $company = Company::factory()->create(['asaas_api_key' => encrypt('fake-key')]);

        config(['features.pix_enabled' => false]);
        $this->assertFalse($company->hasAsaasConfigured());

        config(['features.pix_enabled' => true]);
        $this->assertTrue($company->fresh()->hasAsaasConfigured());
    }
}
