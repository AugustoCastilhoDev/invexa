<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'name'       => fake()->words(3, true),
            'sku'        => strtoupper(Str::random(8)),
            'price'      => fake()->randomFloat(2, 10, 500),
            'cost'       => fake()->randomFloat(2, 5, 200),
            'quantity'   => fake()->numberBetween(10, 100),
            'min_stock'  => 5,
            'min_quantity' => 5,
            'unit'       => 'und',
            'active'     => true,
        ];
    }
}
