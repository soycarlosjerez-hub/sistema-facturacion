<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrdenDetallesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `orden_detalles` (5 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('orden_detalles');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('orden_detalles')->truncate();

        $rows = [
            ['id' => 20, 'tenant_id' => 2, 'orden_id' => 41, 'producto_id' => 170, 'almacen_id' => null, 'cantidad' => 1, 'precio_unitario' => 210.0, 'subtotal' => 210.0, 'notas' => null, 'curso' => 'fuerte', 'estado_cocina' => 'entregado', 'cocina_updated_at' => '2026-08-17 23:49:17', 'created_at' => '2026-07-23 17:49:37', 'updated_at' => '2026-08-17 23:49:17'],
            ['id' => 21, 'tenant_id' => 2, 'orden_id' => 42, 'producto_id' => 171, 'almacen_id' => null, 'cantidad' => 18, 'precio_unitario' => 105.0, 'subtotal' => 1890.0, 'notas' => null, 'curso' => 'fuerte', 'estado_cocina' => 'entregado', 'cocina_updated_at' => '2026-08-18 00:17:29', 'created_at' => '2026-08-06 20:20:45', 'updated_at' => '2026-08-18 00:17:29'],
            ['id' => 22, 'tenant_id' => 2, 'orden_id' => 43, 'producto_id' => 147, 'almacen_id' => null, 'cantidad' => 40, 'precio_unitario' => 693.0, 'subtotal' => 27720.0, 'notas' => null, 'curso' => 'fuerte', 'estado_cocina' => 'entregado', 'cocina_updated_at' => '2026-08-18 00:17:29', 'created_at' => '2026-08-06 20:22:14', 'updated_at' => '2026-08-18 00:17:29'],
            ['id' => 23, 'tenant_id' => 2, 'orden_id' => 44, 'producto_id' => 156, 'almacen_id' => null, 'cantidad' => 30, 'precio_unitario' => 385.0, 'subtotal' => 11550.0, 'notas' => null, 'curso' => 'fuerte', 'estado_cocina' => 'entregado', 'cocina_updated_at' => '2026-08-18 00:17:29', 'created_at' => '2026-08-09 21:46:56', 'updated_at' => '2026-08-18 00:17:29'],
            ['id' => 24, 'tenant_id' => 2, 'orden_id' => 44, 'producto_id' => 123, 'almacen_id' => null, 'cantidad' => 25, 'precio_unitario' => 455.0, 'subtotal' => 11375.0, 'notas' => null, 'curso' => 'fuerte', 'estado_cocina' => 'entregado', 'cocina_updated_at' => '2026-08-18 00:17:29', 'created_at' => '2026-08-09 21:46:57', 'updated_at' => '2026-08-18 00:17:29'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('orden_detalles')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
