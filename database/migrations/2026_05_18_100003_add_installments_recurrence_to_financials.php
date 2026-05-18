<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->unsignedTinyInteger('installment_number')->nullable()->after('notes');
            $table->unsignedTinyInteger('installments_total')->nullable()->after('installment_number');
            $table->string('recurrence')->nullable()->after('installments_total'); // none|monthly|weekly
            $table->unsignedBigInteger('parent_bill_id')->nullable()->after('recurrence');
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->unsignedTinyInteger('installment_number')->nullable()->after('notes');
            $table->unsignedTinyInteger('installments_total')->nullable()->after('installment_number');
            $table->string('recurrence')->nullable()->after('installments_total');
            $table->unsignedBigInteger('parent_receivable_id')->nullable()->after('recurrence');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['installment_number','installments_total','recurrence','parent_bill_id']);
        });
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn(['installment_number','installments_total','recurrence','parent_receivable_id']);
        });
    }
};
