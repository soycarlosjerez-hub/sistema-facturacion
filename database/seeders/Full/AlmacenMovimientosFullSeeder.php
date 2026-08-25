<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlmacenMovimientosFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `almacen_movimientos` (38 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('almacen_movimientos');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('almacen_movimientos')->truncate();

        $rows = [
            ['id' => 1, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 3, 'nota' => 'ANULACIÓN Venta #14 | Motivo: porque si', 'created_at' => '2026-07-07 22:57:02', 'updated_at' => '2026-07-07 22:57:02', 'tenant_id' => 2],
            ['id' => 2, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 3, 'nota' => 'ANULACIÓN Venta #12 | Motivo: 44545', 'created_at' => '2026-07-07 22:57:23', 'updated_at' => '2026-07-07 22:57:23', 'tenant_id' => 2],
            ['id' => 3, 'producto_id' => 141, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-07-10 12:24:38', 'updated_at' => '2026-07-10 12:24:38', 'tenant_id' => 2],
            ['id' => 4, 'producto_id' => 171, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #03', 'created_at' => '2026-07-10 23:15:24', 'updated_at' => '2026-07-10 23:15:24', 'tenant_id' => 2],
            ['id' => 5, 'producto_id' => 172, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #03', 'created_at' => '2026-07-10 23:15:29', 'updated_at' => '2026-07-10 23:15:29', 'tenant_id' => 2],
            ['id' => 6, 'producto_id' => 115, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-07-21 10:58:30', 'updated_at' => '2026-07-21 10:58:30', 'tenant_id' => 2],
            ['id' => 7, 'producto_id' => 217, 'detalle_compra_id' => null, 'user_id' => 29, 'almacen_id' => null, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Venta #55', 'created_at' => '2026-08-14 11:47:18', 'updated_at' => '2026-08-14 11:47:18', 'tenant_id' => 7],
            ['id' => 8, 'producto_id' => 171, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 2, 'nota' => 'Anulación orden Mesa #03: Anulación manual', 'created_at' => '2026-08-17 22:02:16', 'updated_at' => '2026-08-17 22:02:16', 'tenant_id' => 2],
            ['id' => 9, 'producto_id' => 172, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #03: Anulación manual', 'created_at' => '2026-08-17 22:02:17', 'updated_at' => '2026-08-17 22:02:17', 'tenant_id' => 2],
            ['id' => 10, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 23, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-17 22:25:08', 'updated_at' => '2026-08-17 22:25:08', 'tenant_id' => 2],
            ['id' => 11, 'producto_id' => 181, 'detalle_compra_id' => null, 'user_id' => 23, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-17 22:25:17', 'updated_at' => '2026-08-17 22:25:17', 'tenant_id' => 2],
            ['id' => 12, 'producto_id' => 173, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-17 22:46:33', 'updated_at' => '2026-08-17 22:46:33', 'tenant_id' => 2],
            ['id' => 13, 'producto_id' => 173, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-17 22:46:33', 'updated_at' => '2026-08-17 22:46:33', 'tenant_id' => 2],
            ['id' => 14, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-17 23:22:12', 'updated_at' => '2026-08-17 23:22:12', 'tenant_id' => 2],
            ['id' => 15, 'producto_id' => 145, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-17 23:22:16', 'updated_at' => '2026-08-17 23:22:16', 'tenant_id' => 2],
            ['id' => 16, 'producto_id' => 172, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #01', 'created_at' => '2026-08-17 23:41:01', 'updated_at' => '2026-08-17 23:41:01', 'tenant_id' => 2],
            ['id' => 17, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #01', 'created_at' => '2026-08-17 23:41:07', 'updated_at' => '2026-08-17 23:41:07', 'tenant_id' => 2],
            ['id' => 18, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #01', 'created_at' => '2026-08-17 23:41:07', 'updated_at' => '2026-08-17 23:41:07', 'tenant_id' => 2],
            ['id' => 19, 'producto_id' => 146, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #01', 'created_at' => '2026-08-17 23:42:45', 'updated_at' => '2026-08-17 23:42:45', 'tenant_id' => 2],
            ['id' => 20, 'producto_id' => 156, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-08-17 23:45:37', 'updated_at' => '2026-08-17 23:45:37', 'tenant_id' => 2],
            ['id' => 21, 'producto_id' => 172, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #01: Cierre manual desde interfaz', 'created_at' => '2026-08-17 23:58:29', 'updated_at' => '2026-08-17 23:58:29', 'tenant_id' => 2],
            ['id' => 22, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #01: Cierre manual desde interfaz', 'created_at' => '2026-08-17 23:58:29', 'updated_at' => '2026-08-17 23:58:29', 'tenant_id' => 2],
            ['id' => 23, 'producto_id' => 146, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #01: Cierre manual desde interfaz', 'created_at' => '2026-08-17 23:58:29', 'updated_at' => '2026-08-17 23:58:29', 'tenant_id' => 2],
            ['id' => 24, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #04: Cierre manual desde interfaz', 'created_at' => '2026-08-17 23:58:36', 'updated_at' => '2026-08-17 23:58:36', 'tenant_id' => 2],
            ['id' => 25, 'producto_id' => 145, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #04: Cierre manual desde interfaz', 'created_at' => '2026-08-17 23:58:36', 'updated_at' => '2026-08-17 23:58:36', 'tenant_id' => 2],
            ['id' => 26, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-08-17 23:58:42', 'updated_at' => '2026-08-17 23:58:42', 'tenant_id' => 2],
            ['id' => 27, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #02: Cierre manual desde interfaz', 'created_at' => '2026-08-18 00:00:58', 'updated_at' => '2026-08-18 00:00:58', 'tenant_id' => 2],
            ['id' => 28, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-08-18 00:17:01', 'updated_at' => '2026-08-18 00:17:01', 'tenant_id' => 2],
            ['id' => 29, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #02: Cierre manual desde interfaz', 'created_at' => '2026-08-18 00:17:44', 'updated_at' => '2026-08-18 00:17:44', 'tenant_id' => 2],
            ['id' => 30, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #01', 'created_at' => '2026-08-18 00:17:52', 'updated_at' => '2026-08-18 00:17:52', 'tenant_id' => 2],
            ['id' => 31, 'producto_id' => 129, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-08-18 11:07:08', 'updated_at' => '2026-08-18 11:07:08', 'tenant_id' => 2],
            ['id' => 32, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #01', 'created_at' => '2026-08-18 11:07:51', 'updated_at' => '2026-08-18 11:07:51', 'tenant_id' => 2],
            ['id' => 33, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #01: Cierre manual desde interfaz', 'created_at' => '2026-08-18 11:10:01', 'updated_at' => '2026-08-18 11:10:01', 'tenant_id' => 2],
            ['id' => 34, 'producto_id' => 109, 'detalle_compra_id' => null, 'user_id' => 23, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-18 11:36:25', 'updated_at' => '2026-08-18 11:36:25', 'tenant_id' => 2],
            ['id' => 35, 'producto_id' => 174, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #04', 'created_at' => '2026-08-18 11:47:04', 'updated_at' => '2026-08-18 11:47:04', 'tenant_id' => 2],
            ['id' => 36, 'producto_id' => 171, 'detalle_compra_id' => null, 'user_id' => 23, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-08-18 11:48:23', 'updated_at' => '2026-08-18 11:48:23', 'tenant_id' => 2],
            ['id' => 37, 'producto_id' => 171, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'entrada', 'cantidad' => 1, 'nota' => 'Anulación orden Mesa #02: Cierre manual desde interfaz', 'created_at' => '2026-08-18 11:49:05', 'updated_at' => '2026-08-18 11:49:05', 'tenant_id' => 2],
            ['id' => 38, 'producto_id' => 116, 'detalle_compra_id' => null, 'user_id' => 8, 'almacen_id' => 1, 'tipo' => 'salida', 'cantidad' => 1, 'nota' => 'Pedido restaurante - Mesa #02', 'created_at' => '2026-08-18 11:49:15', 'updated_at' => '2026-08-18 11:49:15', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('almacen_movimientos')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
