<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('pix_charge_id')->nullable()->after('notes');
            $table->text('pix_payload')->nullable()->after('pix_charge_id');
            $table->text('pix_qrcode_image')->nullable()->after('pix_payload');
            $table->timestamp('pix_expires_at')->nullable()->after('pix_qrcode_image');
            $table->timestamp('pix_paid_at')->nullable()->after('pix_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'pix_charge_id', 'pix_payload',
                'pix_qrcode_image', 'pix_expires_at', 'pix_paid_at',
            ]);
        });
    }
};
