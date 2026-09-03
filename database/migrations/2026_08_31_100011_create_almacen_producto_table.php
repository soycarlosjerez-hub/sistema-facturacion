<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('stock_actual', 12, 2)->default(0);
            $table->decimal('stock_minimo', 12, 2)->default(0);
            $table->decimal('stock_maximo', 12, 2)->default(0);
            $table->decimal('costo_promedio', 12, 2)->default(0);
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();

            $table->unique(['almacen_id', 'producto_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_producto');
    }
};
