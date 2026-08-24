<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MesasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `mesas` (13 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('mesas');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('mesas')->truncate();

        $rows = [
            ['id' => 1, 'sucursal_id' => null, 'numero' => '01', 'nombre' => 'test', 'capacidad' => 4, 'ubicacion_id' => 1, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-01 15:11:00', 'updated_at' => '2026-07-01 15:11:00', 'categoria_id' => 1, 'tenant_id' => 1],
            ['id' => 2, 'sucursal_id' => null, 'numero' => '01', 'nombre' => 'MESA 1', 'capacidad' => 4, 'ubicacion_id' => 2, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:29:09', 'updated_at' => '2026-08-18 11:10:01', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 3, 'sucursal_id' => null, 'numero' => '02', 'nombre' => 'MESA 2', 'capacidad' => 4, 'ubicacion_id' => 2, 'estado' => 'ocupada', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:29:30', 'updated_at' => '2026-08-18 11:49:11', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 4, 'sucursal_id' => null, 'numero' => '03', 'nombre' => 'MESA 3', 'capacidad' => 4, 'ubicacion_id' => 2, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:29:45', 'updated_at' => '2026-08-17 22:02:17', 'categoria_id' => 2, 'tenant_id' => 2],
            ['id' => 5, 'sucursal_id' => null, 'numero' => '01', 'nombre' => 'MESA 1', 'capacidad' => 4, 'ubicacion_id' => 4, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:30:02', 'updated_at' => '2026-07-02 20:30:02', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 6, 'sucursal_id' => null, 'numero' => '02', 'nombre' => 'MESA 2', 'capacidad' => 3, 'ubicacion_id' => 4, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:30:26', 'updated_at' => '2026-07-02 20:30:26', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 7, 'sucursal_id' => null, 'numero' => '01', 'nombre' => 'MESA 1', 'capacidad' => 4, 'ubicacion_id' => 3, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:30:55', 'updated_at' => '2026-07-02 20:30:55', 'categoria_id' => 2, 'tenant_id' => 2],
            ['id' => 8, 'sucursal_id' => null, 'numero' => '02', 'nombre' => 'MESA 2', 'capacidad' => 4, 'ubicacion_id' => 3, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:31:10', 'updated_at' => '2026-07-02 20:31:10', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 9, 'sucursal_id' => null, 'numero' => '04', 'nombre' => 'MESA 4', 'capacidad' => 3, 'ubicacion_id' => 2, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:35:57', 'updated_at' => '2026-08-18 11:49:02', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 10, 'sucursal_id' => null, 'numero' => '05', 'nombre' => 'MESA 5', 'capacidad' => 2, 'ubicacion_id' => 2, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:36:18', 'updated_at' => '2026-07-02 20:36:18', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 11, 'sucursal_id' => null, 'numero' => '03', 'nombre' => 'MESA 3', 'capacidad' => 3, 'ubicacion_id' => 4, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:36:41', 'updated_at' => '2026-07-02 20:36:41', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 12, 'sucursal_id' => null, 'numero' => '04', 'nombre' => 'MESA 4', 'capacidad' => 4, 'ubicacion_id' => 4, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-02 20:37:22', 'updated_at' => '2026-07-02 20:37:22', 'categoria_id' => 3, 'tenant_id' => 2],
            ['id' => 13, 'sucursal_id' => null, 'numero' => '10', 'nombre' => 'MESA ESPECIAL', 'capacidad' => 10, 'ubicacion_id' => 2, 'estado' => 'disponible', 'activa' => 1, 'pos_x' => null, 'pos_y' => null, 'created_at' => '2026-07-05 18:58:28', 'updated_at' => '2026-07-05 18:58:28', 'categoria_id' => 2, 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('mesas')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
