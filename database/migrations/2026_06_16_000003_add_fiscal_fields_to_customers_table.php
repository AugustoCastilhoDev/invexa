<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // CPF (11 digits) or CNPJ (14 digits)
            $table->string('cpf_cnpj', 18)->nullable()->after('email');

            // Inscricao Estadual (IE)
            $table->string('inscricao_estadual', 20)->nullable()->after('cpf_cnpj');

            // Whether the customer is an ICMS taxpayer
            $table->enum('contribuinte_icms', ['1', '2', '9'])
                  ->default('9')
                  ->comment('1=Contribuinte, 2=Isento, 9=Não contribuinte')
                  ->after('inscricao_estadual');

            // Indicador de tipo de pessoa
            $table->enum('tipo_pessoa', ['F', 'J'])
                  ->default('F')
                  ->comment('F=Física, J=Jurídica')
                  ->after('contribuinte_icms');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'cpf_cnpj',
                'inscricao_estadual',
                'contribuinte_icms',
                'tipo_pessoa',
            ]);
        });
    }
};
