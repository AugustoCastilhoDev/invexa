<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillBulkPayTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private int $companyId;

    protected function setUp(): void
    {
        parent::setUp();
        $company = Company::factory()->create();
        $this->companyId = $company->id;
        $this->user = User::factory()->create([
            'company_id' => $this->companyId,
            'role'       => 'admin',
        ]);
    }

    /** @test */
    public function bulk_pay_marks_pending_bills_as_paid(): void
    {
        $bills = Bill::factory()->count(3)->create([
            'company_id' => $this->companyId,
            'status'     => 'pendente',
        ]);

        $this->actingAs($this->user)
             ->post(route('bills.bulk-pay'), [
                 'ids'            => $bills->pluck('id')->toArray(),
                 'paid_at'        => today()->toDateString(),
                 'payment_method' => 'pix',
             ])
             ->assertRedirect(route('bills.index'));

        foreach ($bills as $bill) {
            $this->assertDatabaseHas('bills', [
                'id'     => $bill->id,
                'status' => 'paga',
            ]);
        }
    }

    /** @test */
    public function bulk_pay_ignores_already_paid_bills(): void
    {
        $paid = Bill::factory()->create([
            'company_id' => $this->companyId,
            'status'     => 'paga',
        ]);

        $this->actingAs($this->user)
             ->post(route('bills.bulk-pay'), [
                 'ids'            => [$paid->id],
                 'paid_at'        => today()->toDateString(),
                 'payment_method' => 'dinheiro',
             ])
             ->assertRedirect();

        // Não altera o registro já pago
        $this->assertDatabaseHas('bills', ['id' => $paid->id, 'status' => 'paga']);
    }

    /** @test */
    public function bulk_pay_requires_ids_and_date(): void
    {
        $this->actingAs($this->user)
             ->post(route('bills.bulk-pay'), [])
             ->assertSessionHasErrors(['ids', 'paid_at', 'payment_method']);
    }

    /** @test */
    public function bulk_pay_cannot_access_other_company_bills(): void
    {
        $other = Company::factory()->create();
        $bill  = Bill::factory()->create(['company_id' => $other->id, 'status' => 'pendente']);

        $this->actingAs($this->user)
             ->post(route('bills.bulk-pay'), [
                 'ids'            => [$bill->id],
                 'paid_at'        => today()->toDateString(),
                 'payment_method' => 'pix',
             ]);

        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'pendente']);
    }
}
