<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Empresa demo + usuários de teste
        $this->call(UserSeeder::class);

        // 2. Super Admin (sem empresa, company_id = null)
        User::firstOrCreate(
            ['email' => 'ac.castilho87@gmail.com'],
            [
                'name'       => 'Augusto Castilho',
                'password'   => Hash::make('@12345'),
                'role'       => 'superadmin',
                'company_id' => null,
                'active'     => true,
            ]
        );

        $this->command->info('Super Admin criado:');
        $this->command->line('   ac.castilho87@gmail.com → @12345');
    }
}
