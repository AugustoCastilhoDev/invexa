<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Altera o ENUM para garantir os valores corretos em português
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('pendente','recebida','cancelada') NOT NULL DEFAULT 'pendente'");
    }

    public function down(): void
    {
        // Reverte para o ENUM anterior (ajuste conforme necessário)
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('pending','received','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
