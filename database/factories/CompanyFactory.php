<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name'                 => $name,
            'slug'                 => Str::slug($name) . '-' . Str::random(4),
            'email'                => fake()->companyEmail(),
            'phone'                => fake()->phoneNumber(),
            'plan'                 => 'business',
            'active'               => true,
            'onboarding_completed' => true,
            'trial_ends_at'        => now()->addDays(14),
        ];
    }
}
