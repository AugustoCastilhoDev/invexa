<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrialAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(Company $company, string $role = 'admin'): User
    {
        return User::factory()->create(['company_id' => $company->id, 'role' => $role]);
    }

    public function test_company_on_trial_is_accessible()
    {
        $company = Company::factory()->create([
            'plan' => 'free',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $this->assertTrue($company->isAccessible());
        $this->assertTrue($company->isOnTrial());

        $user = $this->makeUser($company);
        $this->actingAs($user)->get(route('home'))->assertOk();
    }

    public function test_company_with_expired_trial_and_no_subscription_is_blocked()
    {
        $company = Company::factory()->create([
            'plan' => 'free',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($company->isAccessible());

        $user = $this->makeUser($company);
        $this->actingAs($user)->get(route('home'))->assertRedirect(route('upgrade'));

        // /upgrade em si continua acessível mesmo com a empresa bloqueada,
        // senão o usuário fica sem nenhuma tela pra ver e resolver a situação.
        $this->actingAs($user)->get(route('upgrade'))->assertOk();
    }

    public function test_paid_plan_is_always_accessible_regardless_of_trial()
    {
        foreach (['pro', 'business'] as $plan) {
            $company = Company::factory()->create([
                'plan' => $plan,
                'trial_ends_at' => now()->subDays(30),
            ]);

            $this->assertTrue($company->isAccessible());

            $user = $this->makeUser($company);
            $this->actingAs($user)->get(route('home'))->assertOk();
        }
    }

    public function test_new_registration_gets_a_fourteen_day_trial()
    {
        $response = $this->post(route('register.post'), [
            'company_name'    => 'Empresa Teste Trial',
            'name'            => 'Admin Teste',
            'email'           => 'admintrial@example.com',
            'password'        => 'senha1234',
            'password_confirmation' => 'senha1234',
            'terms_accepted'  => '1',
        ]);

        $company = Company::where('email', 'admintrial@example.com')->first();
        $this->assertNotNull($company);
        $this->assertTrue($company->trial_ends_at->betweenIncluded(now()->addDays(13), now()->addDays(15)));
    }

    public function test_non_admin_role_can_still_view_upgrade_page_when_blocked()
    {
        $company = Company::factory()->create([
            'plan' => 'free',
            'trial_ends_at' => now()->subDay(),
        ]);

        foreach (['vendedor', 'gerente'] as $role) {
            $user = $this->makeUser($company, $role);
            $this->actingAs($user)->get(route('upgrade'))->assertOk();
        }
    }
}
