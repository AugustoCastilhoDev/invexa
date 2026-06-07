<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class BillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id'  => 1,
            'description' => fake()->sentence(4),
            'amount'      => fake()->randomFloat(2, 50, 1000),
            'amount_paid' => 0,
            'due_date'    => now()->addDays(30),
            'status'      => 'pendente',
            'category'    => 'outros',
        ];
    }
}
