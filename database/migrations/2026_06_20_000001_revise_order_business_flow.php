<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi alur bisnis pesanan:
     * menunggu_konfirmasi → ditolak
     *                     → menunggu_fitting → menunggu_dp → menunggu_bahan → dalam_antrian → dijahit → selesai_produksi → siap_diambil → selesai
     *                     → dibatalkan (bisa dari beberapa tahap)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ubah enum status ke alur bisnis baru
            $table->enum('status', [
                'menunggu_konfirmasi',
                'ditolak',
                'menunggu_fitting',
                'menunggu_dp',
                'menunggu_bahan',
                'dalam_antrian',
                'dijahit',
                'selesai_produksi',
                'siap_diambil',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_konfirmasi')->change();

            // Kolom baru untuk kontrol admin
            $table->text('rejection_reason')->nullable()->after('cancellation_reason');
            $table->decimal('dp_amount', 12, 2)->nullable()->after('rejection_reason');
            $table->unsignedInteger('production_days')->nullable()->after('dp_amount');
            $table->unsignedInteger('po_days')->nullable()->after('production_days');
            $table->timestamp('production_started_at')->nullable()->after('po_days');
            $table->timestamp('production_finished_at')->nullable()->after('production_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'menunggu_appointment',
                'menunggu_bahan',
                'diproses',
                'dijahit',
                'finishing',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_appointment')->change();

            $table->dropColumn([
                'rejection_reason',
                'dp_amount',
                'production_days',
                'po_days',
                'production_started_at',
                'production_finished_at',
            ]);
        });
    }
};
