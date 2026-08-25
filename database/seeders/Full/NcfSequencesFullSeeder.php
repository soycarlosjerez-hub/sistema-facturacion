<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NcfSequencesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `ncf_sequences` (39 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('ncf_sequences');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('ncf_sequences')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Factura de Crédito Fiscal', 'prefijo' => 'B01', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 2, 'nombre' => 'Factura de Consumo', 'prefijo' => 'B02', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 3, 'nombre' => 'Regímenes Especiales', 'prefijo' => 'B14', 'desde' => 1, 'hasta' => 100, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 4, 'nombre' => 'Gubernamentales', 'prefijo' => 'B15', 'desde' => 1, 'hasta' => 100, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 5, 'nombre' => 'Secuencia G01 - Tipo 01', 'prefijo' => 'G01', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2030-04-01', 'activo' => 1, 'created_at' => '2026-07-01 15:10:46', 'updated_at' => '2026-07-01 15:10:46', 'tenant_id' => 1],
            ['id' => 6, 'nombre' => 'Secuencia G01 - Tipo 01', 'prefijo' => 'G01', 'desde' => 1, 'hasta' => 100, 'actual' => 1, 'fecha_vencimiento' => '2026-07-02', 'activo' => 1, 'created_at' => '2026-07-02 20:34:27', 'updated_at' => '2026-07-02 20:34:27', 'tenant_id' => 2],
            ['id' => 7, 'nombre' => 'Factura de Crédito Fiscal', 'prefijo' => 'B01', 'desde' => 1, 'hasta' => 100000, 'actual' => 2, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-07-02 20:34:43', 'updated_at' => '2026-08-17 22:36:47', 'tenant_id' => 2],
            ['id' => 8, 'nombre' => 'Secuencia G01 - Tipo 01', 'prefijo' => 'G01', 'desde' => 1, 'hasta' => 100, 'actual' => 1, 'fecha_vencimiento' => '2026-11-30', 'activo' => 1, 'created_at' => '2026-07-23 12:36:46', 'updated_at' => '2026-07-23 12:36:46', 'tenant_id' => 5],
            ['id' => 9, 'nombre' => 'Secuencia b01 - Tipo 01', 'prefijo' => 'b01', 'desde' => 1, 'hasta' => 1, 'actual' => 1, 'fecha_vencimiento' => '2026-07-30', 'activo' => 1, 'created_at' => '2026-07-23 22:57:51', 'updated_at' => '2026-07-23 22:57:51', 'tenant_id' => 4],
            ['id' => 10, 'nombre' => 'Secuencia B11 - Tipo 01', 'prefijo' => 'B11', 'desde' => 5, 'hasta' => 200, 'actual' => 5, 'fecha_vencimiento' => '2026-08-12', 'activo' => 1, 'created_at' => '2026-08-12 15:01:34', 'updated_at' => '2026-08-12 15:01:34', 'tenant_id' => 7],
            ['id' => 11, 'nombre' => 'Factura de Crédito Fiscal', 'prefijo' => 'B01', 'desde' => 1, 'hasta' => 100000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 15:43:26', 'updated_at' => '2026-08-14 15:43:26', 'tenant_id' => 7],
            ['id' => 12, 'nombre' => 'Factura de Consumo', 'prefijo' => 'B02', 'desde' => 1, 'hasta' => 500000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 15:43:26', 'updated_at' => '2026-08-14 15:43:26', 'tenant_id' => 7],
            ['id' => 13, 'nombre' => 'Nota de Débito', 'prefijo' => 'B03', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 15:43:26', 'updated_at' => '2026-08-14 15:43:26', 'tenant_id' => 7],
            ['id' => 14, 'nombre' => 'Nota de Crédito', 'prefijo' => 'B04', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 15, 'nombre' => 'Comprobante de Compras', 'prefijo' => 'B05', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:52:58', 'tenant_id' => 7],
            ['id' => 16, 'nombre' => 'Nota de Crédito Fiscal', 'prefijo' => 'B06', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 17, 'nombre' => 'Regímenes Especiales de Tributación', 'prefijo' => 'B07', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 18, 'nombre' => 'Gubernamental', 'prefijo' => 'B08', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 19, 'nombre' => 'Gastos Menores', 'prefijo' => 'B12', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 20, 'nombre' => 'Regímenes Especiales de Gobierno', 'prefijo' => 'B14', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 21, 'nombre' => 'Pago al Exterior', 'prefijo' => 'B15', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 22, 'nombre' => 'Venta a Zonas Francas', 'prefijo' => 'B16', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 23, 'nombre' => 'Ventas Turísticas', 'prefijo' => 'B17', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 24, 'nombre' => 'Factura Gubernamental', 'prefijo' => 'B31', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 15:43:27', 'updated_at' => '2026-08-14 15:43:27', 'tenant_id' => 7],
            ['id' => 25, 'nombre' => 'Factura de Consumo', 'prefijo' => 'B02', 'desde' => 1, 'hasta' => 500000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 26, 'nombre' => 'Nota de Débito', 'prefijo' => 'B03', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 27, 'nombre' => 'Nota de Crédito', 'prefijo' => 'B04', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 1, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 28, 'nombre' => 'Comprobante de Compras', 'prefijo' => 'B05', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 29, 'nombre' => 'Nota de Crédito Fiscal', 'prefijo' => 'B06', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 30, 'nombre' => 'Regímenes Especiales de Tributación', 'prefijo' => 'B07', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 31, 'nombre' => 'Gubernamental', 'prefijo' => 'B08', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 32, 'nombre' => 'Ingresos', 'prefijo' => 'B11', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 33, 'nombre' => 'Gastos Menores', 'prefijo' => 'B12', 'desde' => 1, 'hasta' => 10000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 34, 'nombre' => 'Regímenes Especiales de Gobierno', 'prefijo' => 'B14', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 35, 'nombre' => 'Pago al Exterior', 'prefijo' => 'B15', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 36, 'nombre' => 'Venta a Zonas Francas', 'prefijo' => 'B16', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 37, 'nombre' => 'Ventas Turísticas', 'prefijo' => 'B17', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 38, 'nombre' => 'Factura Gubernamental', 'prefijo' => 'B31', 'desde' => 1, 'hasta' => 1000, 'actual' => 1, 'fecha_vencimiento' => '2026-12-31', 'activo' => 0, 'created_at' => '2026-08-14 17:32:53', 'updated_at' => '2026-08-14 17:32:53', 'tenant_id' => 2],
            ['id' => 39, 'nombre' => 'Secuencia G01 - Tipo 01', 'prefijo' => 'G01', 'desde' => 1, 'hasta' => 100, 'actual' => 1, 'fecha_vencimiento' => '2026-08-14', 'activo' => 1, 'created_at' => '2026-08-14 17:57:16', 'updated_at' => '2026-08-14 17:57:16', 'tenant_id' => 9],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('ncf_sequences')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
