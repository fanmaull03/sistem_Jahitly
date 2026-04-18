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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->enum('status', [
                'menunggu_appointment',
                'menunggu_bahan',
                'diproses',
                'dijahit',
                'finishing',
                'selesai',
            ])->default('menunggu_appointment');
            $table->enum('material_source', ['customer', 'jasa'])->nullable();
            $table->enum('material_status', ['ready', 'po'])->nullable();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->date('estimated_finish_date')->nullable();
            $table->string('order_number')->unique();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('service_id');
            $table->index('status');
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
