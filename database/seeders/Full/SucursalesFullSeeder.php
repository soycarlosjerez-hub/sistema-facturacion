<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SucursalesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `sucursales` (5 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('sucursales');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('sucursales')->truncate();

        $rows = [
            ['id' => 1, 'codigo' => 'SUC-001', 'nombre' => 'test', 'direccion' => null, 'telefono' => null, 'email' => null, 'rnc' => null, 'activa' => 1, 'es_matriz' => 0, 'created_at' => '2026-07-01 15:10:04', 'updated_at' => '2026-07-01 15:10:04', 'deleted_at' => null, 'tenant_id' => 1],
            ['id' => 2, 'codigo' => 'SUC-01-GAS', 'nombre' => 'SUCURSAL PRINCIPAL GASCUE', 'direccion' => null, 'telefono' => null, 'email' => null, 'rnc' => null, 'activa' => 1, 'es_matriz' => 0, 'created_at' => '2026-07-02 20:33:54', 'updated_at' => '2026-07-02 20:33:54', 'deleted_at' => null, 'tenant_id' => 2],
            ['id' => 4, 'codigo' => 'SUC-001', 'nombre' => 'VILLA OLGA', 'direccion' => null, 'telefono' => null, 'email' => null, 'rnc' => null, 'activa' => 1, 'es_matriz' => 0, 'created_at' => '2026-07-23 12:24:08', 'updated_at' => '2026-07-23 12:24:08', 'deleted_at' => null, 'tenant_id' => 5],
            ['id' => 5, 'codigo' => 'suc-001', 'nombre' => 'multiservicios faudel', 'direccion' => null, 'telefono' => null, 'email' => null, 'rnc' => null, 'activa' => 1, 'es_matriz' => 0, 'created_at' => '2026-07-23 22:53:16', 'updated_at' => '2026-07-23 22:53:16', 'deleted_at' => null, 'tenant_id' => 4],
            ['id' => 6, 'codigo' => 'SUC-001', 'nombre' => 'PRINCIPAL', 'direccion' => null, 'telefono' => null, 'email' => null, 'rnc' => null, 'activa' => 1, 'es_matriz' => 0, 'created_at' => '2026-08-14 17:55:45', 'updated_at' => '2026-08-14 17:55:45', 'deleted_at' => null, 'tenant_id' => 9],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('sucursales')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
