<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            
            // Snapshot de datos del producto (para histórico)
            $table->string('codigo', 50)->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('unidad', 20)->default('Unidad');
            
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('itbis_porcentaje', 5, 2)->default(18);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index('cotizacion_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_items');
    }
};
