<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── subscriptions ──────────────────────────────────────────────
        Schema::table('subscriptions', function (Blueprint $table) {
            // Renomeia company_id de volta para billable_id
            if (Schema::hasColumn('subscriptions', 'company_id') &&
                ! Schema::hasColumn('subscriptions', 'billable_id')) {
                $table->renameColumn('company_id', 'billable_id');
            }

            // Restaura billable_type exigido pelo Cashier
            if (! Schema::hasColumn('subscriptions', 'billable_type')) {
                $table->string('billable_type')->after('billable_id')->nullable();
            }
        });

        // Preenche billable_type para registros existentes
        DB::table('subscriptions')
            ->whereNull('billable_type')
            ->orWhere('billable_type', '')
            ->update(['billable_type' => \App\Models\Company::class]);

        // Torna NOT NULL após preencher
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('billable_type')->nullable(false)->change();
        });

        // ── subscription_items ─────────────────────────────────────────
        Schema::table('subscription_items', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_items', 'company_id') &&
                ! Schema::hasColumn('subscription_items', 'billable_id')) {
                $table->renameColumn('company_id', 'billable_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
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
        });
    }
};
