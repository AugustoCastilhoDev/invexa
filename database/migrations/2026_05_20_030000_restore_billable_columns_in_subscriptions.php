<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── subscriptions ──────────────────────────────────────
        // Passo 1: renomeia company_id → billable_id (se ainda não foi feito)
        if (Schema::hasColumn('subscriptions', 'company_id') &&
            ! Schema::hasColumn('subscriptions', 'billable_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->renameColumn('company_id', 'billable_id');
            });
        }

        // Passo 2: adiciona billable_type se não existir
        if (! Schema::hasColumn('subscriptions', 'billable_type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                // Adiciona após id para ficar junto ao billable_id
                $table->string('billable_type')->nullable()->after('id');
            });
        }

        // Passo 3: preenche billable_type nos registros existentes
        DB::table('subscriptions')
            ->where(function ($q) {
                $q->whereNull('billable_type')->orWhere('billable_type', '');
            })
            ->update(['billable_type' => 'App\\Models\\Company']);

        // Passo 4: torna NOT NULL
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('billable_type')->nullable(false)->change();
        });

        // ── subscription_items ──────────────────────────────────
        if (Schema::hasColumn('subscription_items', 'company_id') &&
            ! Schema::hasColumn('subscription_items', 'billable_id')) {
            Schema::table('subscription_items', function (Blueprint $table) {
                $table->renameColumn('company_id', 'billable_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'billable_type')) {
                $table->dropColumn('billable_type');
            }
            if (Schema::hasColumn('subscriptions', 'billable_id') &&
                ! Schema::hasColumn('subscriptions', 'company_id')) {
                $table->renameColumn('billable_id', 'company_id');
            }
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_items', 'billable_id') &&
                ! Schema::hasColumn('subscription_items', 'company_id')) {
                $table->renameColumn('billable_id', 'company_id');
            }
        });
    }
};
