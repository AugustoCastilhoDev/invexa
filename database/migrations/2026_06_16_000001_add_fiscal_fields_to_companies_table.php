<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Credenciais Focus NFe (multi-tenant)
            $table->string('focusnfe_token', 100)->nullable()->after('asaas_wallet_id');
            $table->string('focusnfe_ambiente', 10)->default('homologacao')->after('focusnfe_token'); // homologacao | producao

            // Dados fiscais da empresa emissora
            $table->string('ie', 20)->nullable()->after('focusnfe_ambiente');          // Inscrição Estadual
            $table->string('im', 20)->nullable()->after('ie');                          // Inscrição Municipal (NFS-e)
            $table->string('crt', 1)->nullable()->after('im');                         // Código Regime Tributário: 1=SN, 2=SN Excesso, 3=Normal
            $table->unsignedTinyInteger('nfe_serie')->default(1)->after('crt');         // Série da NF-e (padrão 1)
            $table->unsignedInteger('nfe_numero_atual')->default(0)->after('nfe_serie'); // Último número emitido
            $table->string('certificado_pfx_path', 255)->nullable()->after('nfe_numero_atual'); // Caminho do .pfx no storage
            $table->string('certificado_senha', 255)->nullable()->after('certificado_pfx_path'); // Senha do certificado (encrypted)
            $table->date('certificado_validade')->nullable()->after('certificado_senha');

            // Endereço fiscal (complementa o que já existe)
            $table->string('logradouro', 150)->nullable()->after('certificado_validade');
            $table->string('numero_endereco', 10)->nullable()->after('logradouro');
            $table->string('complemento', 60)->nullable()->after('numero_endereco');
            $table->string('bairro', 60)->nullable()->after('complemento');
            $table->string('municipio', 60)->nullable()->after('bairro');
            $table->string('uf', 2)->nullable()->after('municipio');
            $table->string('cep', 9)->nullable()->after('uf');
            $table->string('codigo_municipio', 7)->nullable()->after('cep'); // Código IBGE do município
            $table->string('telefone_fiscal', 20)->nullable()->after('codigo_municipio');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'focusnfe_token', 'focusnfe_ambiente',
                'ie', 'im', 'crt',
                'nfe_serie', 'nfe_numero_atual',
                'certificado_pfx_path', 'certificado_senha', 'certificado_validade',
                'logradouro', 'numero_endereco', 'complemento', 'bairro',
                'municipio', 'uf', 'cep', 'codigo_municipio', 'telefone_fiscal',
            ]);
        });
    }
};
