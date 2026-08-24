<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MesaCategoriasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `mesa_categorias` (3 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('mesa_categorias');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('mesa_categorias')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'test', 'color' => '#6b7280', 'icono' => null, 'orden' => 0, 'created_at' => '2026-07-01 15:10:54', 'updated_at' => '2026-07-01 15:10:54', 'tenant_id' => 1],
            ['id' => 2, 'nombre' => 'MESAS VIP', 'color' => '#e8eb1e', 'icono' => null, 'orden' => 0, 'created_at' => '2026-07-02 20:28:24', 'updated_at' => '2026-07-02 20:28:24', 'tenant_id' => 2],
            ['id' => 3, 'nombre' => 'MESAS NORMALES', 'color' => '#6b7280', 'icono' => null, 'orden' => 1, 'created_at' => '2026-07-02 20:28:38', 'updated_at' => '2026-07-02 20:28:38', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('mesa_categorias')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
