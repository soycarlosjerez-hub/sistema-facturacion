<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de items de presupuesto.
     * Cada presupuesto puede tener múltiples items que representan productos,
     * mano de obra, desplazamiento, servicios, licencias u otros conceptos.
     * Los items individualizan la estructura financiera del presupuesto.
     */
    public function up(): void
    {
        Schema::create('presupuesto_items', function (Blueprint $table) {
            $table->id();

            // Presupuesto padre
            $table->foreignId('presupuesto_id')
                ->constrained('presupuestos')
                ->cascadeOnDelete();

            // Producto asociado (NULL si es solo mano de obra/servicio)
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            // Descripción detallada del item (se registra aunque tenga producto asociado)
            $table->text('descripcion');

            // Cantidad de unidades
            $table->integer('cantidad')->default(1);

            // Precio unitario sin descuentos ni impuestos
            $table->decimal('precio_unitario', 12, 2)->default(0);

            // Tipo de item
            $table->enum('tipo_item', [
                'producto',
                'mano_obra',
                'desplazamiento',
                'servicio',
                'licencia',
                'otro'
            ])->default('producto');

            // Descuento específico del item (en porcentaje)
            $table->decimal('descuento', 5, 2)->default(0);

            // Porcentaje de ITBIS para este item
            $table->decimal('itbis_porcentaje', 5, 2)->default(18);

            // Subtotal calculado: (cantidad * precio_unitario * (1 - descuento/100))
            $table->decimal('subtotal', 12, 2)->default(0);

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();

            $table->timestamps();

            // Índices de rendimiento
            $table->index(['presupuesto_id']);
            $table->index(['producto_id']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuesto_items');
    }
};
