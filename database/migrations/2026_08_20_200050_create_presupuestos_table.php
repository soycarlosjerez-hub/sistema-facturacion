<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de presupuestos técnicos.
     * Los presupuestos son cotizaciones formales con desglose de items,
     * validez temporal y estados de seguimiento (borrador, enviada, aprobada,
     * rechazada, vencida). Sirve como documento vinculante antes de convertir
     * el presupuesto aprobado en orden de reparación o venta.
     */
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();

            // Cliente al que se emite el presupuesto
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            // Número único del presupuesto
            $table->string('numero', 50)->unique();

            // Estado actual del presupuesto
            $table->enum('estado', [
                'borrador',
                'enviada',
                'aprobada',
                'rechazada',
                'vencida'
            ])->default('borrador');

            // Subtotal sin impuestos ni descuentos
            $table->decimal('subtotal', 12, 2)->default(0);

            // ITBIS aplicado (18% en República Dominicana)
            $table->decimal('itbis', 12, 2)->default(0);

            // Descuento global aplicado
            $table->decimal('descuento', 12, 2)->default(0);

            // Total final
            $table->decimal('total', 12, 2)->default(0);

            // Fecha límite de validez del presupuesto
            $table->date('valido_hasta')->nullable();

            // Notas generales del presupuesto
            $table->text('notas')->nullable();

            // Usuario que creó el presupuesto
            $table->foreignId('creado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            // Índices de rendimiento
            $table->index(['numero', 'estado']);
            $table->index(['cliente_id', 'estado']);
            $table->index(['valido_hasta', 'estado']);
            $table->index(['tenant_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
