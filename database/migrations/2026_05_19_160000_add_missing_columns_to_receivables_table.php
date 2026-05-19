<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            // Data efetiva do pagamento (alias mais explicito que received_at)
            $table->timestamp('paid_at')->nullable()->after('received_at');

            // Parcelamento
            $table->unsignedTinyInteger('installments')->nullable()->after('paid_at');
            $table->unsignedTinyInteger('installment_number')->nullable()->after('installments');
            $table->unsignedTinyInteger('installments_total')->nullable()->after('installment_number');

            // Recorrencia
            $table->string('recurrence', 20)->nullable()->after('installments_total');

            // Referencia ao pai (parcelamento)
            $table->foreignId('parent_receivable_id')
                ->nullable()
                ->after('recurrence')
                ->constrained('receivables')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropForeign(['parent_receivable_id']);
            $table->dropColumn([
                'paid_at',
                'installments',
                'installment_number',
                'installments_total',
                'recurrence',
                'parent_receivable_id',
            ]);
        });
    }
};
