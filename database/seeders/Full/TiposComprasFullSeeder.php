<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TiposComprasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `tipos_compras` (9 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('tipos_compras');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('tipos_compras')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Compra al contado', 'created_at' => '2026-07-01 15:04:07', 'updated_at' => '2026-07-01 15:04:07'],
            ['id' => 2, 'nombre' => 'Compra a crédito', 'created_at' => '2026-07-01 15:04:07', 'updated_at' => '2026-07-01 15:04:07'],
            ['id' => 3, 'nombre' => 'Compra interna', 'created_at' => '2026-07-01 15:04:07', 'updated_at' => '2026-07-01 15:04:07'],
            ['id' => 4, 'nombre' => 'Compra externa', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08'],
            ['id' => 5, 'nombre' => 'Compra de inventario', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08'],
            ['id' => 6, 'nombre' => 'Compra de activos fijos', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08'],
            ['id' => 7, 'nombre' => 'Compra directa', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08'],
            ['id' => 8, 'nombre' => 'Compra por contrato', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08'],
            ['id' => 9, 'nombre' => 'Compra de emergencia', 'created_at' => '2026-07-01 15:04:08', 'updated_at' => '2026-07-01 15:04:08'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('tipos_compras')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
