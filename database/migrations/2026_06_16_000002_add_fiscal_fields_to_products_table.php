<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ncm', 8)->nullable()->after('barcode');    // Nomenclatura Comum do Mercosul (8 dígitos)
            $table->string('cfop', 4)->nullable()->after('ncm');        // Código Fiscal de Operações (ex: 5102)
            $table->string('cst_icms', 3)->nullable()->after('cfop');   // CST ou CSOSN do ICMS
            $table->string('cst_pis', 2)->nullable()->after('cst_icms');
            $table->string('cst_cofins', 2)->nullable()->after('cst_pis');
            $table->decimal('aliquota_icms', 5, 2)->default(0)->after('cst_cofins');   // % ICMS
            $table->decimal('aliquota_pis', 5, 2)->default(0)->after('aliquota_icms');
            $table->decimal('aliquota_cofins', 5, 2)->default(0)->after('aliquota_pis');
            $table->string('origem_produto', 1)->default('0')->after('aliquota_cofins'); // 0=Nacional, 1=Estrangeira importação direta, etc.
            $table->string('unidade_tributavel', 6)->nullable()->after('origem_produto'); // ex: UN, KG, CX
            $table->string('cest', 7)->nullable()->after('unidade_tributavel');           // Código Especificador da ST (opcional)
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ncm', 'cfop', 'cst_icms', 'cst_pis', 'cst_cofins',
                'aliquota_icms', 'aliquota_pis', 'aliquota_cofins',
                'origem_produto', 'unidade_tributavel', 'cest',
            ]);
        });
    }
};
