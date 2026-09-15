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
        if (Schema::hasTable('ordenes_reparacion')) {
            return;
        }
        Schema::create('ordenes_reparacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('numero_orden', 50);
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('tecnicos')->nullOnDelete();
            $table->enum('tipo_servicio', ['reparacion', 'instalacion', 'configuracion', 'diagnostico', 'mantenimiento']);
            $table->text('problema_reportado');
            $table->text('diagnostico')->nullable();
            $table->text('solucion_aplicada')->nullable();
            $table->decimal('costo_piezas', 10, 2)->default(0);
            $table->decimal('mano_obra', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('itbis', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['recibido', 'diagnosticando', 'en_reparacion', 'listo_para_entrega', 'entregado', 'cancelado'])
                ->default('recibido');
            $table->dateTime('fecha_recibo');
            $table->dateTime('fecha_entrega_estimada')->nullable();
            $table->dateTime('fecha_entrega_real')->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('garantia_extendida')->default(false);
            $table->string('creado_por', 100)->nullable();
            $table->timestamps();

            $table->index('numero_orden');
            $table->index('estado');
            $table->index('cliente_id');
            $table->index('tecnico_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_reparacion');
    }
};
