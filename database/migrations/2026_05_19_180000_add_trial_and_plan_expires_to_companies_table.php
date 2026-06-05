<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Data de término do trial (14 dias após o cadastro)
            $table->timestamp('trial_ends_at')->nullable()->after('active');
            // Data de expiração do plano pago (nulo = sem plano ativo)
            $table->timestamp('plan_expires_at')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'plan_expires_at']);
        });
    }
};
