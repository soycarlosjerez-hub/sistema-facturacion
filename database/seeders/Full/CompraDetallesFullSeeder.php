<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompraDetallesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `compra_detalles` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('compra_detalles');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('compra_detalles')->truncate();

        $rows = [
            ['id' => 5, 'compra_id' => 2, 'producto_id' => 217, 'cantidad' => 72, 'precio_unitario' => 129.0, 'itbis_porcentaje' => 0.0, 'subtotal' => 9288.0, 'created_at' => '2026-08-13 14:59:25', 'updated_at' => '2026-08-13 14:59:25', 'tenant_id' => 7],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('compra_detalles')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
