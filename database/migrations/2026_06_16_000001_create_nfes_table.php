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
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('serie', 10)->default('1');
            $table->unsignedInteger('numero')->nullable();
            $table->string('status', 30)->default('pendente'); // pendente|processando|autorizada|rejeitada|cancelada|denegada
            $table->string('ambiente', 20)->default('homologacao'); // homologacao|producao

            $table->string('chave_acesso', 44)->nullable();
            $table->string('protocolo', 30)->nullable();
            $table->string('ref_focusnfe', 100)->nullable()->index(); // referência usada na API Focus

            $table->timestamp('data_emissao')->nullable();
            $table->timestamp('data_autorizacao')->nullable();
            $table->timestamp('data_cancelamento')->nullable();

            $table->decimal('valor_produtos', 14, 2)->default(0);
            $table->decimal('valor_desconto', 14, 2)->default(0);
            $table->decimal('valor_frete', 14, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->decimal('valor_icms', 14, 2)->default(0);
            $table->decimal('valor_pis', 14, 2)->default(0);
            $table->decimal('valor_cofins', 14, 2)->default(0);

            $table->json('payload_enviado')->nullable();
            $table->json('retorno_focusnfe')->nullable();

            $table->string('xml_path', 500)->nullable();
            $table->string('danfe_path', 500)->nullable();
            $table->text('motivo_rejeicao')->nullable();

            // Carta de Correção Eletrônica
            $table->string('cce_protocolo', 30)->nullable();
            $table->text('cce_correcao')->nullable();
            $table->timestamp('cce_data')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'numero', 'serie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfes');
    }
};
