<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update harga layanan yang sudah ada
        DB::table('services')->where('type', 'vermak')->update(['base_price' => 10000.00]);
        DB::table('services')->where('type', 'custom')->update(['base_price' => 150000.00]);
    }

    public function down(): void
    {
        DB::table('services')->where('type', 'vermak')->update(['base_price' => 25000.00]);
        DB::table('services')->where('type', 'custom')->update(['base_price' => 250000.00]);
    }
};
