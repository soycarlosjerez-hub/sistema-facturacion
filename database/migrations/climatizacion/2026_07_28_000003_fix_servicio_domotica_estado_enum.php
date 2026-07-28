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
     * Expand estado ENUM from ['cotizacion','aprobado','programado','en_progreso','completado','facturado','cancelado']
     * to ['pendiente','programado','en_curso','completado','facturado','cancelado']
     * and map existing values accordingly.
     */
    public function up(): void
    {
        if (!Schema::hasTable('servicios_domotica')) {
            return;
        }

        // Get current data
        $data = DB::table('servicios_domotica')->get(['id', 'estado'])->toArray();

        // Create temp table with new ENUM structure
        DB::statement("
            CREATE TABLE servicios_domotica_temp LIKE servicios_domotica
        ");

        // Alter temp table to have new ENUM
        DB::statement("
            ALTER TABLE servicios_domotica_temp
            MODIFY COLUMN estado ENUM('pendiente','programado','en_curso','completado','facturado','cancelado') DEFAULT 'pendiente'
        ");

        // Copy data with mapping
        foreach ($data as $row) {
            $mappedState = match ($row->estado) {
                'cotizacion' => 'pendiente',
                'aprobado' => 'programado',
                'programado' => 'programado',
                'en_progreso' => 'en_curso',
                'completado' => 'completado',
                'facturado' => 'facturado',
                'cancelado' => 'cancelado',
                default => 'pendiente',
            };

            DB::table('servicios_domotica_temp')->insert([
                'id' => $row->id,
                'estado' => $mappedState,
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
            'tipo_servicio' => DB::raw('(SELECT tipo_servicio FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'direccion_instalacion' => DB::raw('(SELECT direccion_instalacion FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'equipo_asignado_id' => DB::raw('(SELECT equipo_asignado_id FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'presupuesto' => DB::raw('(SELECT presupuesto FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'precio_final' => DB::raw('(SELECT precio_final FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'subtotal' => DB::raw('(SELECT subtotal FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'itbis' => DB::raw('(SELECT itbis FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'descuento' => DB::raw('(SELECT descuento FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
            'total' => DB::raw('(SELECT total FROM servicios_domotica WHERE id = servicios_domotica_temp.id)'),
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
            MODIFY COLUMN estado ENUM('cotizacion','aprobado','programado','en_progreso','completado','facturado','cancelado') DEFAULT 'cotizacion'
        ");

        // Map back
        DB::statement("
            UPDATE servicios_domotica
            SET estado = CASE
                WHEN estado = 'pendiente' THEN 'cotizacion'
                WHEN estado = 'programado' THEN 'aprobado'
                WHEN estado = 'en_curso' THEN 'en_progreso'
                WHEN estado = 'completado' THEN 'completado'
                WHEN estado = 'facturado' THEN 'facturado'
                WHEN estado = 'cancelado' THEN 'cancelado'
                ELSE 'cotizacion'
            END
        ");
    }
};
