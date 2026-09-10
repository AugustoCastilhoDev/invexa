<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboarding_wizard_loads_and_flows_through_all_steps()
    {
        $company = Company::factory()->create(['onboarding_completed' => false]);
        $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin']);

        $this->actingAs($user)->get(route('onboarding.show'))->assertOk();

        $this->actingAs($user)->post(route('onboarding.store'), [
            'step' => 1,
            'name' => 'Empresa Teste Onboarding',
        ])->assertRedirect(route('onboarding.show'));

        $this->actingAs($user)->post(route('onboarding.store'), [
            'step' => 2,
            'product_name' => 'Produto Inicial',
            'product_price' => 19.9,
            'product_qty' => 5,
        ])->assertRedirect(route('onboarding.show'));

        $product = Product::where('company_id', $company->id)->where('name', 'Produto Inicial')->first();
        $this->assertNotNull($product);
        $this->assertEquals(19.9, (float) $product->price);

        $this->actingAs($user)->post(route('onboarding.store'), [
            'step' => 3,
        ])->assertRedirect(route('dashboard'));

        $this->assertTrue($company->fresh()->onboarding_completed);
    }

    public function test_onboarding_skip_route_exists()
    {
        $company = Company::factory()->create(['onboarding_completed' => false]);
        $user = User::factory()->create(['company_id' => $company->id, 'role' => 'admin']);

        $this->actingAs($user)->post(route('onboarding.skip'))->assertRedirect(route('dashboard'));
        $this->assertTrue($company->fresh()->onboarding_completed);
    }
}
