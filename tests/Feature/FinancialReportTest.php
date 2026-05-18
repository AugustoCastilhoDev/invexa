<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Company;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user    = User::factory()->create(['company_id' => $this->company->id, 'role' => 'admin']);
    }

    public function test_financial_report_is_accessible(): void
    {
        $this->actingAs($this->user)
            ->get(route('reports.financial'))
            ->assertOk()
            ->assertSee('Relatório Financeiro');
    }

    public function test_financial_report_shows_correct_totals(): void
    {
        Receivable::factory()->create([
            'company_id' => $this->company->id,
            'amount'     => 1000.00,
            'status'     => 'recebido',
            'paid_at'    => now(),
            'due_date'   => now(),
        ]);

        Bill::factory()->create([
            'company_id' => $this->company->id,
            'amount'     => 400.00,
            'status'     => 'pago',
            'paid_at'    => now(),
            'due_date'   => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('reports.financial', ['period' => 'month']));

        $response->assertOk()
            ->assertSee('1.000,00')
            ->assertSee('400,00');
    }

    public function test_financial_report_custom_period(): void
    {
        $this->actingAs($this->user)
            ->get(route('reports.financial', [
                'period' => 'custom',
                'from'   => now()->startOfMonth()->toDateString(),
                'to'     => now()->endOfMonth()->toDateString(),
            ]))
            ->assertOk();
    }
}
