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
        $company = Company::firstOrCreate(
            ['slug' => 'empresa-demo'],
            [
                'name'   => 'Empresa Demo',
                'email'  => 'contato@empresademo.com',
                'plan'   => 'pro',
                'active' => true,
            ]
        );

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

        $this->command->info('Usuários criados:');
        $this->command->line('   admin@estoque.com    → Admin@123');
        $this->command->line('   gerente@estoque.com  → Gerente@123');
        $this->command->line('   vendedor@estoque.com → Vendedor@123');
    }
}