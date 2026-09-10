<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsaasWebhookSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_request_without_configured_token()
    {
        config(['services.asaas.webhook_token' => '']);

        $company = Company::factory()->create(['slug' => 'empresa-teste']);
        $sale = Sale::factory()->create([
            'company_id' => $company->id,
            'status' => 'pendente',
            'pix_charge_id' => 'pay_123',
        ]);

        $response = $this->postJson('/webhook/asaas/empresa-teste', [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => ['id' => 'pay_123'],
        ]);

        $response->assertStatus(401);
        $this->assertEquals('pendente', $sale->fresh()->status);
    }

    public function test_webhook_rejects_wrong_token_and_accepts_correct_one()
    {
        config(['services.asaas.webhook_token' => 'segredo-correto']);

        $company = Company::factory()->create(['slug' => 'empresa-teste-2']);
        $sale = Sale::factory()->create([
            'company_id' => $company->id,
            'status' => 'pendente',
            'pix_charge_id' => 'pay_456',
        ]);

        $wrong = $this->postJson('/webhook/asaas/empresa-teste-2', [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => ['id' => 'pay_456'],
        ], ['asaas-access-token' => 'token-errado']);
        $wrong->assertStatus(401);
        $this->assertEquals('pendente', $sale->fresh()->status);

        $right = $this->postJson('/webhook/asaas/empresa-teste-2', [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => ['id' => 'pay_456'],
        ], ['asaas-access-token' => 'segredo-correto']);
        $right->assertOk();
        $this->assertEquals('concluida', $sale->fresh()->status);
    }
}
