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
     * Expand estado ENUM from ['programado','instalado','funcionando']
     * to ['pendiente','programado','instalado','fallido','cancelado']
     * and map existing values accordingly.
     */
    public function up(): void
    {
        if (!Schema::hasTable('instalacion_equipo_domotico')) {
            return;
        }

        // Get current data
        $data = DB::table('instalacion_equipo_domotico')->get(['id', 'estado'])->toArray();

        // Create temp table with new ENUM structure
        DB::statement("
            CREATE TABLE instalacion_equipo_domotico_temp LIKE instalacion_equipo_domotico
        ");

        // Alter temp table to have new ENUM
        DB::statement("
            ALTER TABLE instalacion_equipo_domotico_temp
            MODIFY COLUMN estado ENUM('pendiente','programado','instalado','fallido','cancelado') DEFAULT 'programado'
        ");

        // Copy data with mapping
        foreach ($data as $row) {
            $mappedState = match ($row->estado) {
                'programado' => 'programado',
                'instalado' => 'instalado',
                'funcionando' => 'instalado', // functioning implies installed
                default => 'programado',
            };

            DB::table('instalacion_equipo_domotico_temp')->insert([
                'id' => $row->id,
                'estado' => $mappedState,
            ]);
        }

        // Copy indexes and timestamps
        DB::table('instalacion_equipo_domotico_temp')->update([
            'created_at' => DB::raw('(SELECT created_at FROM instalacion_equipo_domotico WHERE id = instalacion_equipo_domotico_temp.id)'),
            'updated_at' => DB::raw('(SELECT updated_at FROM instalacion_equipo_domotico WHERE id = instalacion_equipo_domotico_temp.id)'),
        ]);

        // Swap tables
        DB::statement("DROP TABLE instalacion_equipo_domotico");
        DB::statement("RENAME TABLE instalacion_equipo_domotico_temp TO instalacion_equipo_domotico");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('instalacion_equipo_domotico')) {
            return;
        }

        DB::statement("
            ALTER TABLE instalacion_equipo_domotico
            MODIFY COLUMN estado ENUM('programado','instalado','funcionando') DEFAULT 'programado'
        ");

        // Map back: installed/funcionando -> instalado, programado stays
        DB::statement("
            UPDATE instalacion_equipo_domotico
            SET estado = CASE
                WHEN estado IN ('instalado', 'fallido', 'cancelado') THEN 'instalado'
                WHEN estado = 'pendiente' THEN 'programado'
                ELSE 'programado'
            END
        ");
    }
};
