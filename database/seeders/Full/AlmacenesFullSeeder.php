<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlmacenesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `almacenes` (3 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('almacenes');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('almacenes')->truncate();

        $rows = [
            ['id' => 1, 'sucursal_id' => null, 'nombre' => 'General', 'ubicacion' => 'Principal', 'created_at' => '2026-07-07 15:47:14', 'updated_at' => '2026-07-07 15:47:14', 'tenant_id' => 2],
            ['id' => 2, 'sucursal_id' => null, 'nombre' => 'PRINCIPAL', 'ubicacion' => null, 'created_at' => '2026-07-23 12:24:31', 'updated_at' => '2026-07-23 12:24:31', 'tenant_id' => 5],
            ['id' => 3, 'sucursal_id' => 5, 'nombre' => 'deposito ensueño', 'ubicacion' => 'santiago', 'created_at' => '2026-07-23 22:53:24', 'updated_at' => '2026-07-23 23:01:03', 'tenant_id' => 4],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('almacenes')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
