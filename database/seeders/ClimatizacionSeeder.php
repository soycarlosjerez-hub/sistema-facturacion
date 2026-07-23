<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ClimatizacionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear productos de ejemplo para climatización
        $productos = [
            [
                'nombre' => 'Split 12000 BTU Frío',
                'codigo' => 'CLIMA-SPLIT-12F',
                'marca' => 'LG',
                'modelo' => 'LUAH121Y5',
                'capacidad_btu' => 12000,
                'tipo_equipo' => 'mini-split',
                'categoria_clima' => 'residencial',
                'precio_compra' => 18000,
                'precio_venta' => 25000,
                'stock' => 5,
            ],
            [
                'nombre' => 'Split 12000 BTU Calor/Frío Inverter',
                'codigo' => 'CLIMA-SPLIT-12CF',
                'marca' => 'Samsung',
                'modelo' => 'AR12TXHQASWR',
                'capacidad_btu' => 12000,
                'tipo_equipo' => 'split-inverter',
                'categoria_clima' => 'residencial',
                'precio_compra' => 25000,
                'precio_venta' => 35000,
                'stock' => 3,
            ],
            [
                'nombre' => 'Cassette 24000 BTU 4 Vías',
                'codigo' => 'CLIMA-CASSETTE-24',
                'marca' => 'Daikin',
                'modelo' => 'FTXM-Q25MV1',
                'capacidad_btu' => 24000,
                'tipo_equipo' => 'cassette-4-vias',
                'categoria_clima' => 'comercial',
                'precio_compra' => 55000,
                'precio_venta' => 75000,
                'stock' => 2,
            ],
            [
                'nombre' => 'Multi Split 3 Evaporadoras 12000',
                'codigo' => 'CLIMA-MULTI-3X12',
                'marca' => 'LG',
                'modelo' => 'AM-Q241VEA',
                'capacidad_btu' => 24000,
                'tipo_equipo' => 'multi-split',
                'categoria_clima' => 'residencial',
                'precio_compra' => 65000,
                'precio_venta' => 90000,
                'stock' => 1,
            ],
            [
                'nombre' => 'Conductos 18000 BTU',
                'codigo' => 'CLIMA-CONDUCTO-18',
                'marca' => 'Carrier',
                'modelo' => '42MQC-1J',
                'capacidad_btu' => 18000,
                'tipo_equipo' => 'conductos',
                'categoria_clima' => 'comercial',
                'precio_compra' => 45000,
                'precio_venta' => 62000,
                'stock' => 2,
            ],
        ];

        foreach ($productos as $prod) {
            Producto::firstOrCreate(
                ['codigo' => $prod['codigo']],
                $prod
            );
        }
    }
}
