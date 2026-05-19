<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona a coluna se ainda não existir
        if (!Schema::hasColumn('bills', 'amount_paid')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->decimal('amount_paid', 10, 2)->nullable()->default(0)->after('amount');
            });
        }

        // Corrige registros já pagos que não têm amount_paid preenchido
        DB::table('bills')
            ->where('status', 'paga')
            ->where(function ($q) {
                $q->whereNull('amount_paid')->orWhere('amount_paid', 0);
            })
            ->update(['amount_paid' => DB::raw('amount')]);
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
};
