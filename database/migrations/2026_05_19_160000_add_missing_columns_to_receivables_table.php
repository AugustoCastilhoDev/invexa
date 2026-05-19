<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            // Adiciona apenas colunas que ainda não existem na tabela.
            // paid_at, installment_number, installments_total, recurrence, parent_receivable_id
            // já foram adicionadas por migrations anteriores.

            if (! Schema::hasColumn('receivables', 'installments')) {
                $table->unsignedTinyInteger('installments')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            if (Schema::hasColumn('receivables', 'installments')) {
                $table->dropColumn('installments');
            }
        });
    }
};
