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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('payment_type', ['dp', 'pelunasan']);
            $table->enum('payment_method', ['transfer', 'qris', 'cash']);
            $table->decimal('amount', 12, 2);
            $table->string('proof_file_path')->nullable();
            $table->enum('status', [
                'menunggu_verifikasi',
                'terverifikasi',
                'ditolak',
            ])->default('menunggu_verifikasi');
            $table->text('rejection_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
