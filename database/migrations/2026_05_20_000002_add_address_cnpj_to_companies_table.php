<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'cnpj')) {
                $table->string('cnpj', 18)->nullable()->after('email');
            }
            if (! Schema::hasColumn('companies', 'address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cnpj', 'address']);
        });
    }
};
