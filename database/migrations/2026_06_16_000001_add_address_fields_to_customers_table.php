<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Campos já existentes no model mas que podem não estar na tabela
            if (!Schema::hasColumn('customers', 'logradouro')) {
                $table->string('logradouro', 255)->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'numero_endereco')) {
                $table->string('numero_endereco', 20)->nullable()->after('logradouro');
            }
            if (!Schema::hasColumn('customers', 'complemento')) {
                $table->string('complemento', 100)->nullable()->after('numero_endereco');
            }
            if (!Schema::hasColumn('customers', 'bairro')) {
                $table->string('bairro', 100)->nullable()->after('complemento');
            }
            if (!Schema::hasColumn('customers', 'municipio')) {
                $table->string('municipio', 100)->nullable()->after('bairro');
            }
            if (!Schema::hasColumn('customers', 'uf')) {
                $table->string('uf', 2)->nullable()->after('municipio');
            }
            if (!Schema::hasColumn('customers', 'cep')) {
                $table->string('cep', 8)->nullable()->after('uf');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['logradouro','numero_endereco','complemento','bairro','municipio','uf','cep']);
        });
    }
};
