<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('ie_destinatario', 20)->nullable()->after('cpf_cnpj');     // Inscrição Estadual do destinatário
            $table->string('tipo_pessoa', 2)->default('PF')->after('ie_destinatario'); // PF | PJ
            $table->string('indicador_ie', 1)->default('9')->after('tipo_pessoa');    // 1=Contribuinte, 2=Isento, 9=Não contribuinte
            // Endereço fiscal do destinatário
            $table->string('logradouro', 150)->nullable()->after('indicador_ie');
            $table->string('numero_endereco', 10)->nullable()->after('logradouro');
            $table->string('complemento', 60)->nullable()->after('numero_endereco');
            $table->string('bairro', 60)->nullable()->after('complemento');
            $table->string('municipio', 60)->nullable()->after('bairro');
            $table->string('uf', 2)->nullable()->after('municipio');
            $table->string('cep', 9)->nullable()->after('uf');
            $table->string('codigo_municipio', 7)->nullable()->after('cep'); // Código IBGE
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'ie_destinatario', 'tipo_pessoa', 'indicador_ie',
                'logradouro', 'numero_endereco', 'complemento', 'bairro',
                'municipio', 'uf', 'cep', 'codigo_municipio',
            ]);
        });
    }
};
