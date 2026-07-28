<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand tipo_servicio enum with additional values
        DB::statement("ALTER TABLE ordenes_reparacion MODIFY COLUMN tipo_servicio ENUM('reparacion','instalacion','configuracion','diagnostico','mantenimiento','hardware','software','desbloqueo','recuperacion_datos','personalizacion','otro') NOT NULL");

        // Expand estado enum with additional values
        DB::statement("ALTER TABLE ordenes_reparacion MODIFY COLUMN estado ENUM('recibido','pendiente','diagnosticando','en_reparacion','esperando_piezas','listo_para_entrega','terminado','entregado','cancelado') NOT NULL DEFAULT 'recibido'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert tipo_servicio to original values
        DB::statement("ALTER TABLE ordenes_reparacion MODIFY COLUMN tipo_servicio ENUM('reparacion','instalacion','configuracion','diagnostico','mantenimiento') NOT NULL");

        // Revert estado to original values
        DB::statement("ALTER TABLE ordenes_reparacion MODIFY COLUMN estado ENUM('recibido','diagnosticando','en_reparacion','listo_para_entrega','entregado','cancelado') NOT NULL DEFAULT 'recibido'");
    }
};
