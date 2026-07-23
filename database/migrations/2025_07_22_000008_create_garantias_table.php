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
        if (Schema::hasTable('garantias')) {
            return;
        }
        Schema::create('garantias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('orden_reparacion_id')->nullable()->constrained('ordenes_reparacion')->nullOnDelete();
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->enum('tipo', ['fabrica', 'extendida', 'servicio']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('cobertura', ['piezas', 'mano_obra', 'ambos'])->default('ambos');
            $table->enum('estado', ['vigente', 'expirada', 'reclamada', 'rechazada'])->default('vigente');
            $table->text('terminos_condiciones')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('tipo');
            $table->index(['orden_reparacion_id', 'equipo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
