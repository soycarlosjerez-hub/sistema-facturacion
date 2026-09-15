<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SafeAlterTable::alter("ALTER TABLE ordenes_reparacion MODIFY COLUMN tipo_servicio ENUM('reparacion','instalacion','configuracion','diagnostico','mantenimiento','hardware','software','desbloqueo','recuperacion_datos','personalizacion','otro') NOT NULL");
        SafeAlterTable::alter("ALTER TABLE ordenes_reparacion MODIFY COLUMN estado ENUM('recibido','pendiente','diagnosticando','en_reparacion','esperando_piezas','listo_para_entrega','terminado','entregado','cancelado') NOT NULL DEFAULT 'recibido'");
    }

    public function down(): void
    {
        SafeAlterTable::alter("ALTER TABLE ordenes_reparacion MODIFY COLUMN tipo_servicio ENUM('reparacion','instalacion','configuracion','diagnostico','mantenimiento') NOT NULL");
        SafeAlterTable::alter("ALTER TABLE ordenes_reparacion MODIFY COLUMN estado ENUM('recibido','diagnosticando','en_reparacion','listo_para_entrega','entregado','cancelado') NOT NULL DEFAULT 'recibido'");
    }
};
