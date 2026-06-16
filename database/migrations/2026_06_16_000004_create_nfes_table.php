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
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('sale_id')->nullable();       // venda de origem (pode ser nulo para NF avulsa)
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('user_id');                   // quem emitiu

            // Numeração
            $table->unsignedTinyInteger('serie')->default(1);
            $table->unsignedInteger('numero');

            // Status
            // pendente | processando | autorizada | rejeitada | cancelada | denegada
            $table->string('status', 20)->default('pendente');
            $table->string('ambiente', 12)->default('homologacao'); // homologacao | producao

            // Chave e protocolo
            $table->string('chave_acesso', 44)->nullable()->unique();
            $table->string('protocolo', 20)->nullable();
            $table->string('ref_focusnfe', 50)->nullable();          // referência enviada ao Focus NFe

            // Datas
            $table->dateTime('data_emissao')->nullable();
            $table->dateTime('data_autorizacao')->nullable();
            $table->dateTime('data_cancelamento')->nullable();

            // Valores totais
            $table->decimal('valor_produtos', 14, 2)->default(0);
            $table->decimal('valor_desconto', 14, 2)->default(0);
            $table->decimal('valor_frete', 14, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->decimal('valor_icms', 14, 2)->default(0);
            $table->decimal('valor_pis', 14, 2)->default(0);
            $table->decimal('valor_cofins', 14, 2)->default(0);

            // Payloads e arquivos
            $table->json('payload_enviado')->nullable();             // JSON enviado ao Focus NFe
            $table->json('retorno_focusnfe')->nullable();            // JSON de retorno completo
            $table->string('xml_path', 255)->nullable();             // Caminho do XML no storage
            $table->string('danfe_path', 255)->nullable();           // Caminho do DANFE PDF

            // Mensagem de erro/rejeição
            $table->text('motivo_rejeicao')->nullable();

            // Carta de correção
            $table->string('cce_protocolo', 20)->nullable();
            $table->text('cce_correcao')->nullable();
            $table->dateTime('cce_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Unicidade: número + série por empresa
            $table->unique(['company_id', 'serie', 'numero'], 'nfes_empresa_serie_numero_unique');

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'data_emissao']);
            $table->index('chave_acesso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfes');
    }
};
