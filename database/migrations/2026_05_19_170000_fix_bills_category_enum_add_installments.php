<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Converte coluna category para string (substitui o enum restritivo)
        Schema::table('bills', function (Blueprint $table) {
            $table->string('category', 50)->default('outros')->change();
        });

        // 2. Mapeia valores antigos do enum para os novos usados pelo model
        DB::table('bills')->where('category', 'energia')->update(['category' => 'utilidades']);
        DB::table('bills')->where('category', 'agua')->update(['category' => 'utilidades']);
        DB::table('bills')->where('category', 'internet')->update(['category' => 'utilidades']);
        DB::table('bills')->where('category', 'imposto')->update(['category' => 'impostos']);
        DB::table('bills')->where('category', 'servico')->update(['category' => 'outros']);
        DB::table('bills')->where('category', 'outro')->update(['category' => 'outros']);

        // 3. Adiciona coluna installments se ainda nao existir
        if (! Schema::hasColumn('bills', 'installments')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->unsignedTinyInteger('installments')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->enum('category', [
                'fornecedor','aluguel','energia','agua','internet',
                'folha','imposto','servico','outro',
            ])->default('outro')->change();
        });

        if (Schema::hasColumn('bills', 'installments')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->dropColumn('installments');
            });
        }
    }
};
