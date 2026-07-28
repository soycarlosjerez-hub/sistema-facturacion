<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Expand tipo_servicio ENUM from ['camara_seguridad','alarma','cerradura_smart','iluminacion','termostato','paquete_completo','otro']
     * to ['camaras_seguridad','alarmas','control_acceso','redes','automatizacion','sonido','iluminacion','otro']
     * and map existing values accordingly.
     */
    public function up(): void
    {
        if (!Schema::hasTable('servicios_domotica')) {
            return;
        }

        // Get current data
        $data = DB::table('servicios_domotica')->get(['id', 'tipo_servicio'])->toArray();

        // Create temp table with new ENUM structure
        DB::statement("
            CREATE TABLE servicios_domotica_temp LIKE servicios_domotica
        ");

        // Alter temp table to have new ENUM
        DB::statement("
            ALTER TABLE servicios_domotica_temp
            MODIFY COLUMN tipo_servicio ENUM('camaras_seguridad','alarmas','control_acceso','redes','automatizacion','sonido','iluminacion','otro')
        ");

        // Copy data with mapping
        foreach ($data as $row) {
            $mappedType = match ($row->tipo_servicio) {
                'camara_seguridad' => 'camaras_seguridad',
                'alarma' => 'alarmas',
                'cerradura_smart' => 'control_acceso',
                'iluminacion' => 'iluminacion',
                'termostato' => 'automatizacion',
                'paquete_completo' => 'otro',
                'otro' => 'otro',
                default => 'otro',
            };

            DB::table('servicios_domotica_temp')->insert([
                'id' => $row->id,
                'tipo_servicio' => $mappedType,
            ]);
        }

        // Copy all other fields
        DB::table('servicios_domotica_temp')->update([
            'tenant_id' => DB::raw('(SELECT tenant_id FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'user_id' => DB::raw('(SELECT user_id FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'numero_proyecto' => DB::raw('(SELECT numero_proyecto FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'cliente_id' => DB::raw('(SELECT cliente_id FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'titulo' => DB::raw('(SELECT titulo FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'descripcion' => DB::raw('(SELECT descripcion FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'direccion_instalacion' => DB::raw('(SELECT direccion_instalacion FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'equipo_asignado_id' => DB::raw('(SELECT equipo_asignado_id FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'presupuesto' => DB::raw('(SELECT presupuesto FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'precio_final' => DB::raw('(SELECT precio_final FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'subtotal' => DB::raw('(SELECT subtotal FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'itbis' => DB::raw('(SELECT itbis FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'descuento' => DB::raw('(SELECT descuento FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'total' => DB::raw('(SELECT total FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'estado' => DB::raw('(SELECT estado FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'fecha_programada' => DB::raw('(SELECT fecha_programada FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'fecha_completada' => DB::raw('(SELECT fecha_completada FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'materiales_usados' => DB::raw('(SELECT materiales_usados FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'horas_trabajadas' => DB::raw('(SELECT horas_trabajadas FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'notas' => DB::raw('(SELECT notas FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'created_at' => DB::raw('(SELECT created_at FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'updated_at' => DB::raw('(SELECT updated_at FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
        ]);

        // Swap tables
        DB::statement("DROP TABLE servicios_domotica");
        DB::statement("RENAME TABLE servicios_domotica_temp TO servicios_domotica");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('servicios_domotica')) {
            return;
        }

        DB::statement("
            ALTER TABLE servicios_domotica
            MODIFY COLUMN tipo_servicio ENUM('camara_seguridad','alarma','cerradura_smart','iluminacion','termostato','paquete_completo','otro')
        ");

        // Map back
        DB::statement("
            UPDATE servicios_domotica
            SET tipo_servicio = CASE
                WHEN tipo_servicio = 'camaras_seguridad' THEN 'camara_seguridad'
                WHEN tipo_servicio = 'alarmas' THEN 'alarma'
                WHEN tipo_servicio = 'control_acceso' THEN 'cerradura_smart'
                WHEN tipo_servicio = 'iluminacion' THEN 'iluminacion'
                WHEN tipo_servicio = 'automatizacion' THEN 'termostato'
                WHEN tipo_servicio = 'otro' THEN 'paquete_completo'
                ELSE 'otro'
            END
        ");
    }
};
