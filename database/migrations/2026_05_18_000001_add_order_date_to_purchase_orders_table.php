<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Soft deletes (já adicionado via ALTER manual, mas $table->softDeletes() é idempotente via hasColumn)
            if (!Schema::hasColumn('purchase_orders', 'deleted_at')) {
                $table->softDeletes();
            }

            // Coluna de data do pedido
            if (!Schema::hasColumn('purchase_orders', 'order_date')) {
                $table->date('order_date')->nullable()->after('user_id');
            }
        });

        // Preenche order_date com created_at para registros existentes
        DB::table('purchase_orders')
            ->whereNull('order_date')
            ->update(['order_date' => DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('order_date');
        });
    }
};
