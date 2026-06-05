<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove índice global de SKU
            $table->dropUnique('products_sku_unique');

            // SKU único apenas dentro da mesma empresa
            $table->unique(['company_id', 'sku'], 'products_company_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_company_sku_unique');
            $table->unique('sku');
        });
    }
};
