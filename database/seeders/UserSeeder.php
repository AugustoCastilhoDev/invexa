<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuário de teste
        User::firstOrCreate(
            ['email' => 'teste@estoque.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('Teste@123'),
            ]
        );

        // Usuário administrador
        User::firstOrCreate(
            ['email' => 'admin@estoque.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin@123'),
            ]
        );
    }
}
