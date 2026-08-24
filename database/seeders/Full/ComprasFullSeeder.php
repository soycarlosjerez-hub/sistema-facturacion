<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComprasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `compras` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('compras');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('compras')->truncate();

        $rows = [
            ['id' => 2, 'tenant_id' => 7, 'sucursal_id' => null, 'proveedor_id' => 13, 'almacen_id' => null, 'user_id' => 29, 'tipo_compra_id' => 1, 'total' => 9288.0, 'subtotal' => 9288.0, 'itbis_total' => 0.0, 'observaciones' => null, 'aplica_retencion_isr' => 0, 'aplica_retencion_itbis' => 0, 'retencion_isr' => 0.0, 'retencion_itbis' => 0.0, 'total_neto' => 9288.0, 'ecf_documento_id' => null, 'created_at' => '2026-08-13 14:59:25', 'updated_at' => '2026-08-13 14:59:25', 'fecha' => '2026-08-13'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('compras')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
