<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Garante billable_id (renomeia company_id se necessário)
        if (Schema::hasColumn('subscriptions', 'company_id') &&
            ! Schema::hasColumn('subscriptions', 'billable_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->renameColumn('company_id', 'billable_id');
            });
        }

        // Se nenhuma das duas existir, cria billable_id do zero
        if (! Schema::hasColumn('subscriptions', 'billable_id') &&
            ! Schema::hasColumn('subscriptions', 'company_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('billable_id')->nullable()->after('id');
            });
        }

        // Garante billable_type
        if (! Schema::hasColumn('subscriptions', 'billable_type')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('billable_type')->nullable()->after('id');
            });
        }

        DB::table('subscriptions')
            ->where(function ($q) {
                $q->whereNull('billable_type')->orWhere('billable_type', '');
            })
            ->update(['billable_type' => 'App\\Models\\Company']);
    }

    public function down(): void
    {
        // não reverte — operação segura
    }
};
