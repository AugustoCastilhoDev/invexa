<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A tabela 'nfes' já existia no banco com schema incompleto.
 * Esta migration garante que todas as colunas necessárias existam,
 * adicionando apenas as que estiverem faltando (safe for existing tables).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Se por algum motivo não existir, cria do zero
        if (!Schema::hasTable('nfes')) {
            Schema::create('nfes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->unsignedBigInteger('sale_id')->nullable()->index();
                $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->unsignedInteger('numero')->nullable()->comment('Número sequencial da NF-e');
                $table->unsignedSmallInteger('serie')->default(1);
                $table->string('chave_acesso', 44)->nullable()->unique();
                $table->string('protocolo', 20)->nullable();
                $table->string('natureza_operacao')->default('Venda de mercadoria');
                $table->string('status', 20)->default('pendente')->index();
                $table->string('mensagem_erro')->nullable();
                $table->timestamp('data_emissao')->nullable();
                $table->timestamp('data_autorizacao')->nullable();
                $table->timestamp('data_cancelamento')->nullable();
                $table->string('focus_ref')->nullable();
                $table->json('resposta_sefaz')->nullable();
                $table->string('xml_path')->nullable();
                $table->string('danfe_path')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            return;
        }

        // Tabela já existe: adiciona apenas as colunas ausentes
        Schema::table('nfes', function (Blueprint $table) {
            if (!Schema::hasColumn('nfes', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('nfes', 'sale_id')) {
                $table->unsignedBigInteger('sale_id')->nullable()->index()->after('company_id');
            }
            if (!Schema::hasColumn('nfes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('sale_id');
            }
            if (!Schema::hasColumn('nfes', 'numero')) {
                $table->unsignedInteger('numero')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('nfes', 'serie')) {
                $table->unsignedSmallInteger('serie')->default(1)->after('numero');
            }
            if (!Schema::hasColumn('nfes', 'chave_acesso')) {
                $table->string('chave_acesso', 44)->nullable()->after('serie');
            }
            if (!Schema::hasColumn('nfes', 'protocolo')) {
                $table->string('protocolo', 20)->nullable()->after('chave_acesso');
            }
            if (!Schema::hasColumn('nfes', 'natureza_operacao')) {
                $table->string('natureza_operacao')->default('Venda de mercadoria')->after('protocolo');
            }
            if (!Schema::hasColumn('nfes', 'status')) {
                $table->string('status', 20)->default('pendente')->index()->after('natureza_operacao');
            }
            if (!Schema::hasColumn('nfes', 'mensagem_erro')) {
                $table->string('mensagem_erro')->nullable()->after('status');
            }
            if (!Schema::hasColumn('nfes', 'data_emissao')) {
                $table->timestamp('data_emissao')->nullable()->after('mensagem_erro');
            }
            if (!Schema::hasColumn('nfes', 'data_autorizacao')) {
                $table->timestamp('data_autorizacao')->nullable()->after('data_emissao');
            }
            if (!Schema::hasColumn('nfes', 'data_cancelamento')) {
                $table->timestamp('data_cancelamento')->nullable()->after('data_autorizacao');
            }
            if (!Schema::hasColumn('nfes', 'focus_ref')) {
                $table->string('focus_ref')->nullable()->after('data_cancelamento');
            }
            if (!Schema::hasColumn('nfes', 'resposta_sefaz')) {
                $table->json('resposta_sefaz')->nullable()->after('focus_ref');
            }
            if (!Schema::hasColumn('nfes', 'xml_path')) {
                $table->string('xml_path')->nullable()->after('resposta_sefaz');
            }
            if (!Schema::hasColumn('nfes', 'danfe_path')) {
                $table->string('danfe_path')->nullable()->after('xml_path');
            }
            if (!Schema::hasColumn('nfes', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfes');
    }
};
