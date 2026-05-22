<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Esta migration foi revertida intencionalmente.
 * A coluna permanece como 'cost' no banco de dados.
 * O controller e o model usam 'cost' de forma consistente.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Nenhuma alteração — coluna já existe como 'cost'
    }

    public function down(): void
    {
        // Nada a reverter
    }
};
