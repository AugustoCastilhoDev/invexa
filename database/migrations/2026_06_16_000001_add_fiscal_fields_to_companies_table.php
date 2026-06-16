<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Focus NFe credentials
            $table->string('focusnfe_token')->nullable()->after('asaas_wallet_id');
            $table->string('focusnfe_cnpj', 18)->nullable()->after('focusnfe_token');

            // Fiscal data
            $table->string('inscricao_estadual', 20)->nullable()->after('focusnfe_cnpj');
            $table->enum('regime_tributario', [
                'simples_nacional',
                'simples_nacional_excesso',
                'regime_normal',
            ])->default('simples_nacional')->after('inscricao_estadual');
            $table->unsignedSmallInteger('serie_nfe')->default(1)->after('regime_tributario');

            // Certificate A1 (.pfx) stored as base64 or path
            $table->text('certificado_a1')->nullable()->after('serie_nfe');
            $table->string('certificado_a1_senha')->nullable()->after('certificado_a1');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'focusnfe_token',
                'focusnfe_cnpj',
                'inscricao_estadual',
                'regime_tributario',
                'serie_nfe',
                'certificado_a1',
                'certificado_a1_senha',
            ]);
        });
    }
};
