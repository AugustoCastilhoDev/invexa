<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('asaas_api_key')->nullable()->after('email');
            $table->enum('asaas_environment', ['sandbox', 'production'])
                  ->default('production')->after('asaas_api_key');
            $table->string('asaas_wallet_id')->nullable()->after('asaas_environment');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['asaas_api_key', 'asaas_environment', 'asaas_wallet_id']);
        });
    }
};
