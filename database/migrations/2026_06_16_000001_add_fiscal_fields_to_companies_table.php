<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('focusnfe_token')->nullable()->after('asaas_wallet_id');
            $table->enum('ambiente_nfe', ['homologacao', 'producao'])->default('homologacao')->after('focusnfe_token');
            $table->string('inscricao_estadual')->nullable()->after('ambiente_nfe');
            $table->string('inscricao_municipal')->nullable()->after('inscricao_estadual');
            $table->string('regime_tributario')->nullable()->after('inscricao_municipal'); // 1=Simples, 2=Lucro Presumido, 3=Lucro Real
            $table->string('serie_nfe')->default('1')->after('regime_tributario');
            $table->unsignedInteger('proximo_numero_nfe')->default(1)->after('serie_nfe');
            $table->string('csc_token')->nullable()->after('proximo_numero_nfe');   // para NFC-e
            $table->string('csc_id')->nullable()->after('csc_token');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'focusnfe_token', 'ambiente_nfe', 'inscricao_estadual',
                'inscricao_municipal', 'regime_tributario',
                'serie_nfe', 'proximo_numero_nfe', 'csc_token', 'csc_id',
            ]);
        });
    }
};
