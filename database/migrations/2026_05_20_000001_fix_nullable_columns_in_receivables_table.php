<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            // amount_received e received_at só existem quando status = 'recebida'
            $table->decimal('amount_received', 10, 2)->nullable()->change();
            $table->timestamp('received_at')->nullable()->change();

            // category pode não existir ainda — adiciona se não existir
            if (!Schema::hasColumn('receivables', 'category')) {
                $table->string('category', 50)->nullable()->after('status');
            } else {
                $table->string('category', 50)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->decimal('amount_received', 10, 2)->nullable(false)->change();
            $table->timestamp('received_at')->nullable(false)->change();
            $table->string('category', 50)->nullable(false)->change();
        });
    }
};
