<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'name'       => fake()->name(),
            'email'      => fake()->unique()->safeEmail(),
            'phone'      => fake()->phoneNumber(),
            'active'     => true,
        ];
    }
}
