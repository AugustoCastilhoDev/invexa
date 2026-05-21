<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Empresa demo com trial "eterno" para não ser bloqueada pelo middleware
        $company = Company::firstOrCreate(
            ['slug' => 'empresa-demo'],
            [
                'name'          => 'Empresa Demo',
                'email'         => 'contato@empresademo.com',
                'plan'          => 'free',
                'active'        => true,
                'trial_ends_at' => now()->addYears(10),
            ]
        );

        // Garante trial ativo mesmo se o registro já existia sem trial_ends_at
        if (! $company->trial_ends_at || $company->trial_ends_at->isPast()) {
            $company->update(['trial_ends_at' => now()->addYears(10)]);
        }

        User::firstOrCreate(
            ['email' => 'admin@estoque.com'],
            [
                'name'       => 'Administrador',
                'password'   => Hash::make('Admin@123'),
                'company_id' => $company->id,
                'role'       => 'admin',
                'active'     => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'gerente@estoque.com'],
            [
                'name'       => 'Gerente Teste',
                'password'   => Hash::make('Gerente@123'),
                'company_id' => $company->id,
                'role'       => 'gerente',
                'active'     => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'vendedor@estoque.com'],
            [
                'name'       => 'Vendedor Teste',
                'password'   => Hash::make('Vendedor@123'),
                'company_id' => $company->id,
                'role'       => 'vendedor',
                'active'     => true,
            ]
        );

        $this->command->info('Usuários de demo criados:');
        $this->command->line('   admin@estoque.com    → Admin@123     (admin)');
        $this->command->line('   gerente@estoque.com  → Gerente@123   (gerente)');
        $this->command->line('   vendedor@estoque.com → Vendedor@123  (vendedor)');
    }
}
