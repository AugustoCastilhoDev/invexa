<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // NCM - Nomenclatura Comum do Mercosul (8 digits)
            $table->string('ncm', 10)->nullable()->after('supplier_id');

            // CFOP - Código Fiscal de Operações e Prestações (4 digits)
            $table->string('cfop', 5)->nullable()->after('ncm');

            // CST / CSOSN
            $table->string('cst_icms', 5)->nullable()->after('cfop');  // 000–900 or CSOSN 101–900
            $table->string('cst_pis', 3)->nullable()->after('cst_icms');
            $table->string('cst_cofins', 3)->nullable()->after('cst_pis');

            // Tax rates (%)
            $table->decimal('aliquota_icms', 5, 2)->default(0)->after('cst_cofins');
            $table->decimal('aliquota_pis', 5, 2)->default(0)->after('aliquota_icms');
            $table->decimal('aliquota_cofins', 5, 2)->default(0)->after('aliquota_pis');

            // CEST - Código Especificador da Substituição Tributária (optional)
            $table->string('cest', 9)->nullable()->after('aliquota_cofins');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ncm',
                'cfop',
                'cst_icms',
                'cst_pis',
                'cst_cofins',
                'aliquota_icms',
                'aliquota_pis',
                'aliquota_cofins',
                'cest',
            ]);
        });
    }
};
