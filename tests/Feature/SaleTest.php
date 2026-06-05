<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Customer $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company  = Company::factory()->create();
        $this->user     = User::factory()->create(['company_id' => $this->company->id, 'role' => 'admin']);
        $this->customer = Customer::factory()->create(['company_id' => $this->company->id]);
        $this->product  = Product::factory()->create([
            'company_id' => $this->company->id,
            'quantity'   => 100,
            'price'      => 50.00,
            'active'     => true,
        ]);
    }

    public function test_authenticated_user_can_list_sales(): void
    {
        $this->actingAs($this->user)
            ->get(route('sales.index'))
            ->assertOk();
    }

    public function test_sale_creation_reduces_stock(): void
    {
        $this->actingAs($this->user)
            ->post(route('sales.store'), [
                'customer_id' => $this->customer->id,
                'sale_date'   => now()->toDateString(),
                'status'      => 'concluida',
                'items'       => [[
                    'product_id' => $this->product->id,
                    'quantity'   => 5,
                    'price'      => 50.00,
                ]],
            ])
            ->assertRedirect(route('sales.index'));

        $this->assertDatabaseHas('sales', [
            'customer_id' => $this->customer->id,
            'total'       => 250.00,
            'status'      => 'concluida',
        ]);

        $this->assertEquals(95, $this->product->fresh()->quantity);
    }

    public function test_completed_sale_creates_receivable_automatically(): void
    {
        $this->actingAs($this->user)
            ->post(route('sales.store'), [
                'customer_id' => $this->customer->id,
                'sale_date'   => now()->toDateString(),
                'status'      => 'concluida',
                'items'       => [[
                    'product_id' => $this->product->id,
                    'quantity'   => 2,
                    'price'      => 50.00,
                ]],
            ]);

        $sale = Sale::where('customer_id', $this->customer->id)->latest()->first();

        $this->assertNotNull($sale);
        $this->assertDatabaseHas('receivables', [
            'sale_id'    => $sale->id,
            'amount'     => 100.00,
            'status'     => 'pendente',
        ]);
    }

    public function test_pending_sale_does_not_create_receivable(): void
    {
        $this->actingAs($this->user)
            ->post(route('sales.store'), [
                'customer_id' => $this->customer->id,
                'sale_date'   => now()->toDateString(),
                'status'      => 'pendente',
                'items'       => [[
                    'product_id' => $this->product->id,
                    'quantity'   => 1,
                    'price'      => 50.00,
                ]],
            ]);

        $sale = Sale::where('customer_id', $this->customer->id)->latest()->first();
        $this->assertDatabaseMissing('receivables', ['sale_id' => $sale->id]);
    }

    public function test_sale_with_insufficient_stock_fails(): void
    {
        $this->actingAs($this->user)
            ->post(route('sales.store'), [
                'customer_id' => $this->customer->id,
                'sale_date'   => now()->toDateString(),
                'status'      => 'concluida',
                'items'       => [[
                    'product_id' => $this->product->id,
                    'quantity'   => 999,
                    'price'      => 50.00,
                ]],
            ])
            ->assertSessionHasErrors();
    }

    public function test_invoice_view_is_accessible(): void
    {
        $sale = Sale::factory()->create([
            'company_id'    => $this->company->id,
            'customer_id'   => $this->customer->id,
            'customer_name' => $this->customer->name,
            'total'         => 200.00,
            'status'        => 'concluida',
        ]);

        $this->actingAs($this->user)
            ->get(route('sales.invoice', $sale))
            ->assertOk()
            ->assertSee('Nota de Venda');
    }

    public function test_pdf_download_returns_pdf_response(): void
    {
        $sale = Sale::factory()->create([
            'company_id'    => $this->company->id,
            'customer_id'   => $this->customer->id,
            'customer_name' => $this->customer->name,
            'total'         => 200.00,
            'status'        => 'concluida',
        ]);

        $this->actingAs($this->user)
            ->get(route('sales.pdf', $sale))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
