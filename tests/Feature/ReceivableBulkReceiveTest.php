<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceivableBulkReceiveTest extends TestCase
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
    public function bulk_receive_marks_pending_receivables_as_received(): void
    {
        $recs = Receivable::factory()->count(3)->create([
            'company_id' => $this->companyId,
            'status'     => 'pendente',
        ]);

        $this->actingAs($this->user)
             ->post(route('receivables.bulk-receive'), [
                 'ids'            => $recs->pluck('id')->toArray(),
                 'received_at'    => today()->toDateString(),
                 'payment_method' => 'pix',
             ])
             ->assertRedirect(route('receivables.index'));

        foreach ($recs as $rec) {
            $this->assertDatabaseHas('receivables', [
                'id'     => $rec->id,
                'status' => 'recebida',
            ]);
        }
    }

    /** @test */
    public function bulk_receive_ignores_already_received(): void
    {
        $received = Receivable::factory()->create([
            'company_id' => $this->companyId,
            'status'     => 'recebida',
        ]);

        $this->actingAs($this->user)
             ->post(route('receivables.bulk-receive'), [
                 'ids'            => [$received->id],
                 'received_at'    => today()->toDateString(),
                 'payment_method' => 'dinheiro',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('receivables', ['id' => $received->id, 'status' => 'recebida']);
    }

    /** @test */
    public function bulk_receive_requires_ids_and_date(): void
    {
        $this->actingAs($this->user)
             ->post(route('receivables.bulk-receive'), [])
             ->assertSessionHasErrors(['ids', 'received_at', 'payment_method']);
    }

    /** @test */
    public function bulk_receive_cannot_access_other_company_receivables(): void
    {
        $other = Company::factory()->create();
        $rec   = Receivable::factory()->create(['company_id' => $other->id, 'status' => 'pendente']);

        $this->actingAs($this->user)
             ->post(route('receivables.bulk-receive'), [
                 'ids'            => [$rec->id],
                 'received_at'    => today()->toDateString(),
                 'payment_method' => 'pix',
             ]);

        $this->assertDatabaseHas('receivables', ['id' => $rec->id, 'status' => 'pendente']);
    }
}
