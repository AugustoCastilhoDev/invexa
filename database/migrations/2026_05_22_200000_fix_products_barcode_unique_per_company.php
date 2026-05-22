<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Barcode também deve ser único apenas dentro da mesma empresa
        if (collect(Schema::getIndexes('products'))->firstWhere('name', 'products_barcode_unique')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique('products_barcode_unique');
                $table->unique(['company_id', 'barcode'], 'products_company_barcode_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_company_barcode_unique');
            $table->unique('barcode');
        });
    }
};
