<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona campos fiscais à tabela companies.
     * Usa Schema::hasColumn() em cada campo para ser idempotente
     * (seguro mesmo que o banco esteja em estado parcial por falha anterior).
     */
    public function up(): void
    {
        $columns = [
            'focusnfe_token'        => fn(Blueprint $t) => $t->string('focusnfe_token', 100)->nullable()->after('asaas_wallet_id'),
            'focusnfe_ambiente'     => fn(Blueprint $t) => $t->string('focusnfe_ambiente', 10)->nullable()->after('focusnfe_token'),
            'ie'                    => fn(Blueprint $t) => $t->string('ie', 20)->nullable()->after('focusnfe_ambiente'),
            'im'                    => fn(Blueprint $t) => $t->string('im', 20)->nullable()->after('ie'),
            'crt'                   => fn(Blueprint $t) => $t->string('crt', 1)->nullable()->after('im'),
            'nfe_serie'             => fn(Blueprint $t) => $t->unsignedTinyInteger('nfe_serie')->nullable()->after('crt'),
            'nfe_numero_atual'      => fn(Blueprint $t) => $t->unsignedInteger('nfe_numero_atual')->nullable()->after('nfe_serie'),
            'certificado_pfx_path'  => fn(Blueprint $t) => $t->string('certificado_pfx_path', 255)->nullable()->after('nfe_numero_atual'),
            'certificado_senha'     => fn(Blueprint $t) => $t->string('certificado_senha', 255)->nullable()->after('certificado_pfx_path'),
            'certificado_validade'  => fn(Blueprint $t) => $t->date('certificado_validade')->nullable()->after('certificado_senha'),
            'logradouro'            => fn(Blueprint $t) => $t->string('logradouro', 150)->nullable()->after('certificado_validade'),
            'numero_endereco'       => fn(Blueprint $t) => $t->string('numero_endereco', 10)->nullable()->after('logradouro'),
            'complemento'           => fn(Blueprint $t) => $t->string('complemento', 60)->nullable()->after('numero_endereco'),
            'bairro'                => fn(Blueprint $t) => $t->string('bairro', 60)->nullable()->after('complemento'),
            'municipio'             => fn(Blueprint $t) => $t->string('municipio', 60)->nullable()->after('bairro'),
            'uf'                    => fn(Blueprint $t) => $t->string('uf', 2)->nullable()->after('municipio'),
            'cep'                   => fn(Blueprint $t) => $t->string('cep', 9)->nullable()->after('uf'),
            'codigo_municipio'      => fn(Blueprint $t) => $t->string('codigo_municipio', 7)->nullable()->after('cep'),
            'telefone_fiscal'       => fn(Blueprint $t) => $t->string('telefone_fiscal', 20)->nullable()->after('codigo_municipio'),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('companies', $column)) {
                Schema::table('companies', function (Blueprint $table) use ($definition) {
                    $definition($table);
                });
            }
        }
    }

    public function down(): void
    {
        $cols = [
            'focusnfe_token', 'focusnfe_ambiente',
            'ie', 'im', 'crt',
            'nfe_serie', 'nfe_numero_atual',
            'certificado_pfx_path', 'certificado_senha', 'certificado_validade',
            'logradouro', 'numero_endereco', 'complemento', 'bairro',
            'municipio', 'uf', 'cep', 'codigo_municipio', 'telefone_fiscal',
        ];

        Schema::table('companies', function (Blueprint $table) use ($cols) {
            foreach ($cols as $col) {
                if (Schema::hasColumn('companies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
