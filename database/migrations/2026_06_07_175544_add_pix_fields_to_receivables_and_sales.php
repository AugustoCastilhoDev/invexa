<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->string('asaas_payment_id')->nullable()->after('notes');
            $table->string('asaas_customer_id')->nullable()->after('asaas_payment_id');
            $table->text('pix_qr_code')->nullable()->after('asaas_customer_id');
            $table->text('pix_copy_paste')->nullable()->after('pix_qr_code');
            $table->string('pix_status')->nullable()->after('pix_copy_paste');
            $table->timestamp('pix_expires_at')->nullable()->after('pix_status');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('asaas_payment_id')->nullable()->after('notes');
            $table->text('pix_qr_code')->nullable()->after('asaas_payment_id');
            $table->text('pix_copy_paste')->nullable()->after('pix_qr_code');
            $table->string('pix_status')->nullable()->after('pix_copy_paste');
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn(['asaas_payment_id', 'asaas_customer_id', 'pix_qr_code', 'pix_copy_paste', 'pix_status', 'pix_expires_at']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['asaas_payment_id', 'pix_qr_code', 'pix_copy_paste', 'pix_status']);
        });
    }
};
