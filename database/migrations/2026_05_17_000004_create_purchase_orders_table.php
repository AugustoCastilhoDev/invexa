<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // quem criou

            $table->string('number', 20)->comment('Número da OC, ex: OC-000001');
            $table->enum('status', ['rascunho', 'enviada', 'recebida_parcial', 'recebida', 'cancelada'])
                  ->default('rascunho');

            $table->date('expected_date')->nullable()->comment('Previsão de entrega');
            $table->date('received_at')->nullable()->comment('Data do recebimento');

            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'number']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
