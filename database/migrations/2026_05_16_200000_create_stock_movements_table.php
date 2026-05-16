<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['entrada', 'saida', 'ajuste']);
            $table->integer('quantity');          // positivo = entrada, negativo = saída
            $table->integer('quantity_before');   // snapshot do estoque antes
            $table->integer('quantity_after');    // snapshot do estoque depois
            $table->string('reason')->nullable(); // motivo (compra, devolução, ajuste, venda)
            $table->text('notes')->nullable();
            $table->nullableMorphs('source');     // polymorphic: sale, purchase order, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
