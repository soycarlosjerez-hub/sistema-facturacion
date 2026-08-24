<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TiposVentasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `tipos_ventas` (12 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('tipos_ventas');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('tipos_ventas')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Contado', 'descripcion' => 'Pago inmediato al momento de la compra', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 2, 'nombre' => 'Crédito', 'descripcion' => 'Pago a crédito en una fecha posterior', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 3, 'nombre' => 'Transferencia bancaria', 'descripcion' => 'Pago realizado mediante transferencia electrónica', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 4, 'nombre' => 'Tarjeta de débito', 'descripcion' => 'Pago con tarjeta de débito', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 5, 'nombre' => 'Tarjeta de crédito', 'descripcion' => 'Pago con tarjeta de crédito', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 6, 'nombre' => 'Cheque', 'descripcion' => 'Pago mediante cheque', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 7, 'nombre' => 'Depósito bancario', 'descripcion' => 'Pago realizado mediante un depósito en cuenta bancaria', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 8, 'nombre' => 'Pago móvil', 'descripcion' => 'Pago mediante plataformas móviles como Zelle, PayPal, etc.', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 9, 'nombre' => 'Contra entrega', 'descripcion' => 'Pago al momento de recibir el producto (COD)', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 10, 'nombre' => 'Venta mixta', 'descripcion' => 'Combinación de múltiples métodos de pago', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 11, 'nombre' => 'Fiado', 'descripcion' => 'El cliente se lleva el producto y paga después sin condiciones de crédito formales', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 12, 'nombre' => 'Financiamiento interno', 'descripcion' => 'Pago en cuotas pactadas directamente con la empresa', 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('tipos_ventas')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
