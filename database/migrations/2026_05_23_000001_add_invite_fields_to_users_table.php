<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('invite_token', 64)->nullable()->unique()->after('active');
            $table->timestamp('invite_sent_at')->nullable()->after('invite_token');
            $table->timestamp('invite_accepted_at')->nullable()->after('invite_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['invite_token', 'invite_sent_at', 'invite_accepted_at']);
        });
    }
};
