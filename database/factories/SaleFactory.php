<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class SaleFactory extends Factory
{
    public function definition(): array
    {
        static $saleNumber = 1;
        return [
            'company_id'    => 1,
            'sale_number'   => $saleNumber++,
            'customer_name' => fake()->name(),
            'sale_date'     => now(),
            'total'         => fake()->randomFloat(2, 50, 1000),
            'status'        => 'concluida',
        ];
    }
}
