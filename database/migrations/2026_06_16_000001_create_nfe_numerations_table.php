<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfe_numerations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('ambiente', 20)->default('homologacao'); // homologacao | producao
            $table->string('serie', 3)->default('1');
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->timestamps();

            // Cada empresa tem uma linha por ambiente+série
            $table->unique(['company_id', 'ambiente', 'serie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfe_numerations');
    }
};
