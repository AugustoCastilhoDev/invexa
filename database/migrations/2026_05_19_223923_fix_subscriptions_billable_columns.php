<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Renomeia billable_id → company_id e billable_type → remove (não usamos polimórfico)
            if (Schema::hasColumn('subscriptions', 'billable_id')) {
                $table->renameColumn('billable_id', 'company_id');
            }
            if (Schema::hasColumn('subscriptions', 'billable_type')) {
                $table->dropColumn('billable_type');
            }
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_items', 'billable_id')) {
                $table->renameColumn('billable_id', 'company_id');
            }
            if (Schema::hasColumn('subscription_items', 'billable_type')) {
                $table->dropColumn('billable_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('company_id', 'billable_id');
            $table->string('billable_type')->after('billable_id')->nullable();
        });
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->renameColumn('company_id', 'billable_id');
        });
    }
};