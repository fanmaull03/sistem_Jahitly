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
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', [
                'katun',
                'polyester',
                'linen',
                'sutra',
                'denim',
                'sifon',
                'wol',
                'lainnya',
            ]);
            $table->string('color');
            $table->decimal('price_per_meter', 12, 2);
            $table->enum('stock_status', ['tersedia', 'po'])->default('tersedia');
            $table->unsignedInteger('po_days')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('stock_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
