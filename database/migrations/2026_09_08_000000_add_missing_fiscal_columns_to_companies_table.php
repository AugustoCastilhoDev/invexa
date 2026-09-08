<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Em produção, a migration 2026_06_16_000001_add_fiscal_fields_to_companies_table
 * só chegou a criar a coluna focusnfe_token antes de falhar silenciosamente
 * (ficou marcada como "Ran" mesmo sem terminar) — as outras 8 colunas nunca
 * existiram na tabela. Esta migration é idempotente: só cria o que faltar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'ambiente_nfe')) {
                $table->enum('ambiente_nfe', ['homologacao', 'producao'])->default('homologacao')->after('focusnfe_token');
            }
            if (! Schema::hasColumn('companies', 'inscricao_estadual')) {
                $table->string('inscricao_estadual')->nullable()->after('ambiente_nfe');
            }
            if (! Schema::hasColumn('companies', 'inscricao_municipal')) {
                $table->string('inscricao_municipal')->nullable()->after('inscricao_estadual');
            }
            if (! Schema::hasColumn('companies', 'regime_tributario')) {
                $table->string('regime_tributario')->nullable()->after('inscricao_municipal');
            }
            if (! Schema::hasColumn('companies', 'serie_nfe')) {
                $table->string('serie_nfe')->default('1')->after('regime_tributario');
            }
            if (! Schema::hasColumn('companies', 'proximo_numero_nfe')) {
                $table->unsignedInteger('proximo_numero_nfe')->default(1)->after('serie_nfe');
            }
            if (! Schema::hasColumn('companies', 'csc_token')) {
                $table->string('csc_token')->nullable()->after('proximo_numero_nfe');
            }
            if (! Schema::hasColumn('companies', 'csc_id')) {
                $table->string('csc_id')->nullable()->after('csc_token');
            }
        });
    }

    public function down(): void
    {
        // Irreversível de propósito: não sabemos se essas colunas já existiam
        // antes desta migration em outros ambientes, então não removemos nada.
    }
};
