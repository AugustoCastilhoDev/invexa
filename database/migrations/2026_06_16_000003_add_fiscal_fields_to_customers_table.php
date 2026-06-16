<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos fiscais à tabela customers.
     * Usa Schema::hasColumn() em cada campo para ser idempotente.
     * A coluna de referência é 'document' (CPF/CNPJ) — não 'cpf_cnpj'.
     */
    public function up(): void
    {
        $columns = [
            'ie_destinatario'  => fn(Blueprint $t) => $t->string('ie_destinatario', 20)->nullable()->after('document'),
            'tipo_pessoa'      => fn(Blueprint $t) => $t->string('tipo_pessoa', 2)->nullable()->after('ie_destinatario'),
            'indicador_ie'     => fn(Blueprint $t) => $t->string('indicador_ie', 1)->nullable()->after('tipo_pessoa'),
            'logradouro'       => fn(Blueprint $t) => $t->string('logradouro', 150)->nullable()->after('indicador_ie'),
            'numero_endereco'  => fn(Blueprint $t) => $t->string('numero_endereco', 10)->nullable()->after('logradouro'),
            'complemento'      => fn(Blueprint $t) => $t->string('complemento', 60)->nullable()->after('numero_endereco'),
            'bairro'           => fn(Blueprint $t) => $t->string('bairro', 60)->nullable()->after('complemento'),
            'municipio'        => fn(Blueprint $t) => $t->string('municipio', 60)->nullable()->after('bairro'),
            'uf'               => fn(Blueprint $t) => $t->string('uf', 2)->nullable()->after('municipio'),
            'cep'              => fn(Blueprint $t) => $t->string('cep', 9)->nullable()->after('uf'),
            'codigo_municipio' => fn(Blueprint $t) => $t->string('codigo_municipio', 7)->nullable()->after('cep'),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('customers', $column)) {
                Schema::table('customers', function (Blueprint $table) use ($definition) {
                    $definition($table);
                });
            }
        }
    }

    public function down(): void
    {
        $cols = [
            'ie_destinatario', 'tipo_pessoa', 'indicador_ie',
            'logradouro', 'numero_endereco', 'complemento', 'bairro',
            'municipio', 'uf', 'cep', 'codigo_municipio',
        ];

        Schema::table('customers', function (Blueprint $table) use ($cols) {
            foreach ($cols as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
