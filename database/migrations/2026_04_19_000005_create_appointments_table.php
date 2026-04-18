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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('appointment_date');
            $table->enum('status', [
                'menunggu',
                'terkonfirmasi',
                'selesai',
                'dibatalkan',
            ])->default('menunggu');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('customer_id');
            $table->index('appointment_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
