<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Garante que a coluna seja 'cost_price'.
 * Se ainda for 'cost', renomeia. Se já for 'cost_price', não faz nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('products');

        if (in_array('cost', $columns) && !in_array('cost_price', $columns)) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('cost', 'cost_price');
            });
        }
    }

    public function down(): void
    {
        $columns = Schema::getColumnListing('products');

        if (in_array('cost_price', $columns) && !in_array('cost', $columns)) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('cost_price', 'cost');
            });
        }
    }
};
