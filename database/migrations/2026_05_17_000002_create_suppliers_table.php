<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('name', 200);
            $table->string('trade_name', 200)->nullable();   // nome fantasia
            $table->string('document', 20)->nullable();      // CNPJ / CPF
            $table->string('email', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('contact_person', 100)->nullable();

            // Endereço
            $table->string('address', 200)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();

            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['company_id', 'active']);
            $table->index(['company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
