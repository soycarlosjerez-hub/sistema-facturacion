<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CajasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `cajas` (8 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('cajas');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('cajas')->truncate();

        $rows = [
            ['id' => 2, 'sucursal_id' => 2, 'nombre' => 'CAJA PRINCIPAL', 'codigo' => 'CP01', 'ubicacion' => null, 'activo' => 1, 'estado' => 'abierta', 'created_at' => '2026-07-02 20:20:52', 'updated_at' => '2026-08-17 22:33:09', 'tenant_id' => 2],
            ['id' => 3, 'sucursal_id' => 2, 'nombre' => 'CAJA BAR', 'codigo' => 'CB01', 'ubicacion' => null, 'activo' => 1, 'estado' => 'cerrada', 'created_at' => '2026-07-02 20:21:09', 'updated_at' => '2026-08-17 22:33:25', 'tenant_id' => 2],
            ['id' => 5, 'sucursal_id' => 2, 'nombre' => 'órdenes', 'codigo' => 'ORD', 'ubicacion' => null, 'activo' => 1, 'estado' => 'abierta', 'created_at' => '2026-07-10 11:09:52', 'updated_at' => '2026-08-17 22:33:13', 'tenant_id' => 2],
            ['id' => 6, 'sucursal_id' => 4, 'nombre' => 'CAJA 01', 'codigo' => null, 'ubicacion' => null, 'activo' => 1, 'estado' => 'abierta', 'created_at' => '2026-07-23 12:24:22', 'updated_at' => '2026-07-23 17:00:51', 'tenant_id' => 5],
            ['id' => 7, 'sucursal_id' => 5, 'nombre' => 'ruben severino', 'codigo' => 'C01', 'ubicacion' => 'santiago', 'activo' => 1, 'estado' => 'cerrada', 'created_at' => '2026-07-23 22:59:14', 'updated_at' => '2026-07-23 22:59:14', 'tenant_id' => 4],
            ['id' => 8, 'sucursal_id' => null, 'nombre' => 'Rayne', 'codigo' => '1234', 'ubicacion' => null, 'activo' => 1, 'estado' => 'abierta', 'created_at' => '2026-08-12 15:02:18', 'updated_at' => '2026-08-12 15:03:01', 'tenant_id' => 7],
            ['id' => 9, 'sucursal_id' => 6, 'nombre' => 'caja 01', 'codigo' => null, 'ubicacion' => null, 'activo' => 1, 'estado' => 'abierta', 'created_at' => '2026-08-14 17:55:54', 'updated_at' => '2026-08-17 22:29:09', 'tenant_id' => 9],
            ['id' => 11, 'sucursal_id' => 2, 'nombre' => 'CAJA MARIA', 'codigo' => 'CM01', 'ubicacion' => null, 'activo' => 1, 'estado' => 'abierta', 'created_at' => '2026-08-17 22:23:52', 'updated_at' => '2026-08-17 22:24:29', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('cajas')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
