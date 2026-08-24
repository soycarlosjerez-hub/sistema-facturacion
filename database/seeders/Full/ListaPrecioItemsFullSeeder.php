<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListaPrecioItemsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `lista_precio_items` (5 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('lista_precio_items');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('lista_precio_items')->truncate();

        $rows = [
            ['id' => 1, 'lista_precio_id' => 1, 'producto_id' => 135, 'precio' => 450.0, 'created_at' => '2026-07-21 22:56:27', 'updated_at' => '2026-07-21 22:56:27', 'tenant_id' => null],
            ['id' => 3, 'lista_precio_id' => 1, 'producto_id' => 171, 'precio' => 1.0, 'created_at' => '2026-07-21 22:57:32', 'updated_at' => '2026-07-22 00:55:06', 'tenant_id' => 2],
            ['id' => 9, 'lista_precio_id' => 1, 'producto_id' => 170, 'precio' => 50.0, 'created_at' => '2026-07-22 00:53:58', 'updated_at' => '2026-07-22 00:55:06', 'tenant_id' => 2],
            ['id' => 13, 'lista_precio_id' => 1, 'producto_id' => 147, 'precio' => 500.0, 'created_at' => '2026-07-22 00:54:12', 'updated_at' => '2026-07-22 00:55:06', 'tenant_id' => 2],
            ['id' => 16, 'lista_precio_id' => 1, 'producto_id' => 161, 'precio' => 200.0, 'created_at' => '2026-07-22 00:55:06', 'updated_at' => '2026-07-22 00:55:06', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('lista_precio_items')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
