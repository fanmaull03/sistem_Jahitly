<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Modifikasi enum status untuk menambahkan 'dibatalkan'
            $table->enum('status', [
                'menunggu_appointment',
                'menunggu_bahan',
                'diproses',
                'dijahit',
                'finishing',
                'selesai',
                'dibatalkan',
            ])->change();

            // Tambah kolom untuk tracking pembatalan
            $table->timestamp('cancelled_at')->nullable()->after('status');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert enum status
            $table->enum('status', [
                'menunggu_appointment',
                'menunggu_bahan',
                'diproses',
                'dijahit',
                'finishing',
                'selesai',
            ])->change();

            // Drop kolom pembatalan
            $table->dropColumn(['cancelled_at', 'cancellation_reason']);
        });
    }
};
