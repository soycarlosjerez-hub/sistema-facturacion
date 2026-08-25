<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MesaUbicacionesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `mesa_ubicaciones` (4 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('mesa_ubicaciones');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('mesa_ubicaciones')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => 1, 'nombre' => 'test', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-01 15:10:50', 'updated_at' => '2026-07-01 15:10:50'],
            ['id' => 2, 'tenant_id' => 2, 'nombre' => 'SALON PRINCIPAL', 'descripcion' => 'ESTE SALON ES EL DEL AREA DEL BAR', 'activa' => 1, 'created_at' => '2026-07-02 20:27:17', 'updated_at' => '2026-07-02 20:27:17'],
            ['id' => 3, 'tenant_id' => 2, 'nombre' => 'SALON SECUNDARIO', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 20:27:35', 'updated_at' => '2026-07-02 20:27:35'],
            ['id' => 4, 'tenant_id' => 2, 'nombre' => 'TERRAZA', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 20:27:47', 'updated_at' => '2026-07-02 20:27:47'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('mesa_ubicaciones')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
