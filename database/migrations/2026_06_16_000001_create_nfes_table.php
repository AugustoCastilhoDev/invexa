<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfes', function (Blueprint $table) {
            $table->id();

            // Multi-tenant
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Venda origem (opcional — pode ser emitida avulsa no futuro)
            $table->unsignedBigInteger('sale_id')->nullable()->index();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');

            // Usuário que emitiu
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Identificação fiscal
            $table->unsignedInteger('numero')->nullable()->comment('Número sequencial da NF-e');
            $table->unsignedSmallInteger('serie')->default(1);
            $table->string('chave_acesso', 44)->nullable()->unique()->comment('Chave de 44 dígitos da NF-e');
            $table->string('protocolo', 20)->nullable();
            $table->string('natureza_operacao')->default('Venda de mercadoria');

            // Status
            // pendente | processando | autorizada | rejeitada | cancelada | erro
            $table->string('status', 20)->default('pendente')->index();
            $table->string('mensagem_erro')->nullable();

            // Datas fiscais
            $table->timestamp('data_emissao')->nullable();
            $table->timestamp('data_autorizacao')->nullable();
            $table->timestamp('data_cancelamento')->nullable();

            // Referência Focus NFe
            $table->string('focus_ref')->nullable()->comment('ref retornado pelo Focus NFe');

            // Resposta bruta da API (JSON)
            $table->json('resposta_sefaz')->nullable();

            // Caminhos dos arquivos gerados
            $table->string('xml_path')->nullable();
            $table->string('danfe_path')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Garante número único por empresa+série
            $table->unique(['company_id', 'serie', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfes');
    }
};
