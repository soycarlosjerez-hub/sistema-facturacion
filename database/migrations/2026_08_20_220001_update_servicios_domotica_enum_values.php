<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE servicios_domotica SET estado = CASE estado
            WHEN 'cotizacion' THEN 'pendiente'
            WHEN 'aprobado' THEN 'programado'
            WHEN 'en_progreso' THEN 'en_curso'
            WHEN 'facturado' THEN 'completado'
            ELSE estado END");

        DB::statement("UPDATE servicios_domotica SET tipo_servicio = CASE tipo_servicio
            WHEN 'camara_seguridad' THEN 'camaras_seguridad'
            WHEN 'alarma' THEN 'alarmas'
            WHEN 'cerradura_smart' THEN 'control_acceso'
            WHEN 'termostato' THEN 'otro'
            WHEN 'paquete_completo' THEN 'otro'
            ELSE tipo_servicio END");

        DB::statement("ALTER TABLE servicios_domotica MODIFY COLUMN tipo_servicio ENUM('camaras_seguridad','alarmas','control_acceso','redes','automatizacion','sonido','iluminacion','otro') NOT NULL");
        DB::statement("ALTER TABLE servicios_domotica MODIFY COLUMN estado ENUM('pendiente','programado','en_curso','completado','cancelado') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE servicios_domotica MODIFY COLUMN tipo_servicio ENUM('camara_seguridad','alarma','cerradura_smart','iluminacion','termostato','paquete_completo','otro') NOT NULL");
        DB::statement("ALTER TABLE servicios_domotica MODIFY COLUMN estado ENUM('cotizacion','aprobado','programado','en_progreso','completado','facturado','cancelado') NOT NULL DEFAULT 'cotizacion'");
    }
};