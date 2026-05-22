<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove o índice global que impedia empresas diferentes de ter a mesma categoria
            $table->dropUnique('categories_name_unique');

            // Cria índice composto: único apenas dentro da mesma empresa
            $table->unique(['company_id', 'name'], 'categories_company_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_company_name_unique');
            $table->unique('name');
        });
    }
};
