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
            $table->enum('status', [
                'menunggu_konfirmasi',
                'ditolak',
                'menunggu_pakaian_dikirim',
                'pakaian_dikirim',
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
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
        });
    }
};
