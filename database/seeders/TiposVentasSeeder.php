<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposVentasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $tipos = [
            ['nombre' => 'Contado', 'descripcion' => 'Pago inmediato al momento de la compra'],
            ['nombre' => 'Crédito', 'descripcion' => 'Pago a crédito en una fecha posterior'],
            ['nombre' => 'Transferencia bancaria', 'descripcion' => 'Pago realizado mediante transferencia electrónica'],
            ['nombre' => 'Tarjeta de débito', 'descripcion' => 'Pago con tarjeta de débito'],
            ['nombre' => 'Tarjeta de crédito', 'descripcion' => 'Pago con tarjeta de crédito'],
            ['nombre' => 'Cheque', 'descripcion' => 'Pago mediante cheque'],
            ['nombre' => 'Depósito bancario', 'descripcion' => 'Pago realizado mediante un depósito en cuenta bancaria'],
            ['nombre' => 'Pago móvil', 'descripcion' => 'Pago mediante plataformas móviles como Zelle, PayPal, etc.'],
            ['nombre' => 'Contra entrega', 'descripcion' => 'Pago al momento de recibir el producto (COD)'],
            ['nombre' => 'Venta mixta', 'descripcion' => 'Combinación de múltiples métodos de pago'],
            ['nombre' => 'Fiado', 'descripcion' => 'El cliente se lleva el producto y paga después sin condiciones de crédito formales'],
            ['nombre' => 'Financiamiento interno', 'descripcion' => 'Pago en cuotas pactadas directamente con la empresa'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_ventas')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
