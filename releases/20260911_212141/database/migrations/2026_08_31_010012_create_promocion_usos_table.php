<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocion_usos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocion_id')->constrained('promocions')->cascadeOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->decimal('descuento_aplicado', 12, 2);
            $table->timestamps();

            $table->index('cart_id');
            $table->index('venta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promocion_usos');
    }
};
