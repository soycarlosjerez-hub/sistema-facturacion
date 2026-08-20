<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de especificaciones técnicas de productos.
     * Almacenaje flexible de atributos como procesador, RAM, pantalla, etc.
     * Usando un modelo key-value por producto para máxima flexibilidad.
     */
    public function up(): void
    {
        Schema::create('producto_especificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            // Clave de la especificación (ej: 'procesador', 'ram', 'pulgadas_pantalla')
            $table->string('especificacion_key', 100);

            // Valor de la especificación (ej: 'Intel Core i7 12ª Gen', '16 GB DDR4')
            $table->string('especificacion_value', 500);

            $table->timestamps();

            // Índice para búsquedas rápidas por producto y clave
            $table->index(['producto_id', 'especificacion_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_especificaciones');
    }
};
