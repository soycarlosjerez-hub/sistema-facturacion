<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SesionCajasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `sesion_cajas` (14 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('sesion_cajas');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('sesion_cajas')->truncate();

        $rows = [
            ['id' => 1, 'caja_id' => 2, 'user_id' => 8, 'fecha_apertura' => '2026-07-02 22:21:56', 'fecha_cierre' => '2026-07-05 20:56:00', 'monto_inicial' => 5000.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 5000.0, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-07-02 20:21:56', 'updated_at' => '2026-07-05 18:56:00', 'tenant_id' => 2],
            ['id' => 2, 'caja_id' => 2, 'user_id' => 8, 'fecha_apertura' => '2026-07-05 21:38:32', 'fecha_cierre' => '2026-07-06 19:07:04', 'monto_inicial' => 2000.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 2000.0, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-07-05 19:38:32', 'updated_at' => '2026-07-06 17:07:04', 'tenant_id' => 2],
            ['id' => 4, 'caja_id' => 2, 'user_id' => 8, 'fecha_apertura' => '2026-07-06 23:59:18', 'fecha_cierre' => '2026-07-11 01:14:28', 'monto_inicial' => 0.0, 'ventas_efectivo' => 9705.5, 'ventas_tarjeta' => 1734.6, 'ventas_transferencia' => 1404.2, 'monto_declarado' => 9705.5, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-07-06 21:59:18', 'updated_at' => '2026-07-10 23:14:28', 'tenant_id' => 2],
            ['id' => 5, 'caja_id' => 5, 'user_id' => 8, 'fecha_apertura' => '2026-07-10 13:09:52', 'fecha_cierre' => '2026-07-12 14:05:04', 'monto_inicial' => 0.0, 'ventas_efectivo' => 3213.14, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 3213.14, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-07-10 11:09:52', 'updated_at' => '2026-07-12 12:05:04', 'tenant_id' => 2],
            ['id' => 6, 'caja_id' => 5, 'user_id' => 8, 'fecha_apertura' => '2026-07-12 14:15:47', 'fecha_cierre' => '2026-08-18 00:27:41', 'monto_inicial' => 2000.0, 'ventas_efectivo' => 8036.98, 'ventas_tarjeta' => 350.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 10100.0, 'descuadre' => 63.02, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-07-12 12:15:47', 'updated_at' => '2026-08-17 22:27:41', 'tenant_id' => 2],
            ['id' => 8, 'caja_id' => 2, 'user_id' => 8, 'fecha_apertura' => '2026-08-03 14:11:29', 'fecha_cierre' => '2026-08-18 00:28:39', 'monto_inicial' => 2000.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 2000.0, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-08-03 12:11:29', 'updated_at' => '2026-08-17 22:28:39', 'tenant_id' => 2],
            ['id' => 9, 'caja_id' => 3, 'user_id' => 8, 'fecha_apertura' => '2026-08-03 14:11:34', 'fecha_cierre' => '2026-08-18 00:29:09', 'monto_inicial' => 2000.0, 'ventas_efectivo' => 4957.18, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 6957.18, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-08-03 12:11:34', 'updated_at' => '2026-08-17 22:29:09', 'tenant_id' => 2],
            ['id' => 10, 'caja_id' => 8, 'user_id' => 29, 'fecha_apertura' => '2026-08-12 17:03:01', 'fecha_cierre' => null, 'monto_inicial' => 2000.0, 'ventas_efectivo' => 160.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => null, 'descuadre' => null, 'estado' => 'abierta', 'notas' => null, 'created_at' => '2026-08-12 15:03:01', 'updated_at' => '2026-08-14 11:47:18', 'tenant_id' => 7],
            ['id' => 11, 'caja_id' => 9, 'user_id' => 30, 'fecha_apertura' => '2026-08-14 20:29:38', 'fecha_cierre' => '2026-08-18 00:28:15', 'monto_inicial' => 0.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 0.0, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-08-14 18:29:38', 'updated_at' => '2026-08-17 22:28:15', 'tenant_id' => 9],
            ['id' => 12, 'caja_id' => 11, 'user_id' => 23, 'fecha_apertura' => '2026-08-18 00:24:29', 'fecha_cierre' => null, 'monto_inicial' => 500.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => null, 'descuadre' => null, 'estado' => 'abierta', 'notas' => null, 'created_at' => '2026-08-17 22:24:29', 'updated_at' => '2026-08-17 22:24:29', 'tenant_id' => 2],
            ['id' => 13, 'caja_id' => 9, 'user_id' => 30, 'fecha_apertura' => '2026-08-18 00:29:09', 'fecha_cierre' => null, 'monto_inicial' => 100.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => null, 'descuadre' => null, 'estado' => 'abierta', 'notas' => null, 'created_at' => '2026-08-17 22:29:09', 'updated_at' => '2026-08-17 22:29:09', 'tenant_id' => 9],
            ['id' => 14, 'caja_id' => 3, 'user_id' => 8, 'fecha_apertura' => '2026-08-18 00:33:05', 'fecha_cierre' => '2026-08-18 00:33:25', 'monto_inicial' => 2000.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => 2000.0, 'descuadre' => 0.0, 'estado' => 'cerrada', 'notas' => null, 'created_at' => '2026-08-17 22:33:05', 'updated_at' => '2026-08-17 22:33:25', 'tenant_id' => 2],
            ['id' => 15, 'caja_id' => 2, 'user_id' => 8, 'fecha_apertura' => '2026-08-18 00:33:09', 'fecha_cierre' => null, 'monto_inicial' => 2000.0, 'ventas_efectivo' => 0.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => null, 'descuadre' => null, 'estado' => 'abierta', 'notas' => null, 'created_at' => '2026-08-17 22:33:09', 'updated_at' => '2026-08-17 22:33:09', 'tenant_id' => 2],
            ['id' => 16, 'caja_id' => 5, 'user_id' => 8, 'fecha_apertura' => '2026-08-18 00:33:13', 'fecha_cierre' => null, 'monto_inicial' => 2000.0, 'ventas_efectivo' => 275.0, 'ventas_tarjeta' => 0.0, 'ventas_transferencia' => 0.0, 'monto_declarado' => null, 'descuadre' => null, 'estado' => 'abierta', 'notas' => null, 'created_at' => '2026-08-17 22:33:13', 'updated_at' => '2026-08-17 22:36:47', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('sesion_cajas')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
