<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Renomeia colunas para alinhar com o AuditLog model
            $table->renameColumn('model',  'model_type');
            $table->renameColumn('before', 'old_values');
            $table->renameColumn('after',  'new_values');
            $table->renameColumn('ip',     'ip_address');

            // Adiciona updated_at para timestamps() completo
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->renameColumn('model_type', 'model');
            $table->renameColumn('old_values', 'before');
            $table->renameColumn('new_values', 'after');
            $table->renameColumn('ip_address', 'ip');
            $table->dropColumn('updated_at');
        });
    }
};
