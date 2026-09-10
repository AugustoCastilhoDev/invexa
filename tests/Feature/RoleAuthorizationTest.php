<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $company = Company::factory()->create([
            'plan' => 'business',
            'active' => true,
            'trial_ends_at' => now()->addDays(30),
        ]);

        return User::factory()->create([
            'company_id' => $company->id,
            'role' => $role,
        ]);
    }

    public function test_vendedor_cannot_view_or_create_users()
    {
        $vendedor = $this->makeUser('vendedor');

        $this->actingAs($vendedor)->get(route('users.index'))->assertForbidden();

        $this->actingAs($vendedor)->post(route('users.store'), [
            'name'     => 'Tentativa Escalada',
            'email'    => 'escalada@example.com',
            'role'     => 'admin',
            'password' => 'senha123',
        ])->assertForbidden();

        $this->assertNull(User::where('email', 'escalada@example.com')->first());
    }

    public function test_vendedor_cannot_access_gerente_only_areas()
    {
        $vendedor = $this->makeUser('vendedor');

        foreach (['suppliers.index', 'purchase-orders.index', 'products.index', 'categories.index',
                  'stock.index', 'bills.index', 'receivables.index', 'reports.index'] as $route) {
            $this->actingAs($vendedor)->get(route($route))->assertForbidden();
        }
    }

    public function test_vendedor_cannot_access_admin_only_settings_or_billing()
    {
        $vendedor = $this->makeUser('vendedor');

        foreach (['settings.company', 'settings.fiscal', 'settings.api', 'webhooks.index',
                  'upgrade', 'company-profile.edit'] as $route) {
            $this->actingAs($vendedor)->get(route($route))->assertForbidden();
        }

        $this->actingAs($vendedor)->post(route('subscription.cancel'))->assertForbidden();
    }

    public function test_gerente_can_access_gerente_areas_but_not_admin_only()
    {
        $gerente = $this->makeUser('gerente');

        $this->actingAs($gerente)->get(route('suppliers.index'))->assertOk();
        $this->actingAs($gerente)->get(route('bills.index'))->assertOk();
        $this->actingAs($gerente)->get(route('reports.index'))->assertOk();

        $this->actingAs($gerente)->get(route('users.index'))->assertForbidden();
        $this->actingAs($gerente)->get(route('settings.company'))->assertForbidden();
        $this->actingAs($gerente)->get(route('upgrade'))->assertForbidden();
    }

    public function test_admin_can_access_everything()
    {
        $admin = $this->makeUser('admin');

        foreach (['users.index', 'suppliers.index', 'bills.index', 'reports.index',
                  'settings.company', 'settings.fiscal', 'settings.api', 'webhooks.index', 'upgrade'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
    }

    public function test_all_roles_can_access_shared_modules()
    {
        foreach (['vendedor', 'gerente', 'admin'] as $role) {
            $user = $this->makeUser($role);

            $this->actingAs($user)->get(route('home'))->assertOk();
            $this->actingAs($user)->get(route('sales.index'))->assertOk();
            $this->actingAs($user)->get(route('quotes.index'))->assertOk();
            $this->actingAs($user)->get(route('customers.index'))->assertOk();
            $this->actingAs($user)->get(route('returns.index'))->assertOk();
            $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        }
    }
}
