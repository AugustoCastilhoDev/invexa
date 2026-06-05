<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedInteger('sale_number')->nullable()->after('company_id');
        });

        // Backfill: numera as vendas existentes sequencialmente por empresa
        $companies = DB::table('sales')
            ->select('company_id')
            ->distinct()
            ->pluck('company_id');

        foreach ($companies as $companyId) {
            $sales = DB::table('sales')
                ->where('company_id', $companyId)
                ->orderBy('id')
                ->pluck('id');

            foreach ($sales as $seq => $saleId) {
                DB::table('sales')
                    ->where('id', $saleId)
                    ->update(['sale_number' => $seq + 1]);
            }
        }

        // Após o backfill, torna NOT NULL e adiciona unique por empresa
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedInteger('sale_number')->nullable(false)->change();
            $table->unique(['company_id', 'sale_number']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'sale_number']);
            $table->dropColumn('sale_number');
        });
    }
};
