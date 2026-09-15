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
        if (Schema::hasTable('servicios_domotica')) {
            return;
        }
        Schema::create('servicios_domotica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('numero_proyecto', 50);
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_servicio', ['camara_seguridad', 'alarma', 'cerradura_smart', 'iluminacion', 'termostato', 'paquete_completo', 'otro']);
            $table->text('direccion_instalacion')->nullable();
            $table->foreignId('equipo_asignado_id')->nullable()->constrained('tecnicos')->nullOnDelete();
            $table->decimal('presupuesto', 10, 2)->default(0);
            $table->decimal('precio_final', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('itbis', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['cotizacion', 'aprobado', 'programado', 'en_progreso', 'completado', 'facturado', 'cancelado'])
                ->default('cotizacion');
            $table->date('fecha_programada')->nullable();
            $table->date('fecha_completada')->nullable();
            $table->json('materiales_usados')->nullable();
            $table->decimal('horas_trabajadas', 5, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('numero_proyecto');
            $table->index('estado');
            $table->index('cliente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_domotica');
    }
};
