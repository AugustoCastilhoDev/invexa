<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->index()->after('email');
            }
            if (! Schema::hasColumn('companies', 'pm_type')) {
                $table->string('pm_type')->nullable()->after('stripe_id');
            }
            if (! Schema::hasColumn('companies', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable()->after('pm_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('companies', 'stripe_id')    ? 'stripe_id'    : null,
                Schema::hasColumn('companies', 'pm_type')      ? 'pm_type'      : null,
                Schema::hasColumn('companies', 'pm_last_four') ? 'pm_last_four' : null,
            ]));
        });
    }
};
