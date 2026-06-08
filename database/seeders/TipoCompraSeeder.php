<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoCompra;

class TipoCompraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $tipos = [
            ['nombre' => 'Compra al contado'],
            ['nombre' => 'Compra a crédito'],
            ['nombre' => 'Compra interna'],
            ['nombre' => 'Compra externa'],
            ['nombre' => 'Compra de inventario'],
            ['nombre' => 'Compra de activos fijos'],
            ['nombre' => 'Compra directa'],
            ['nombre' => 'Compra por contrato'],
            ['nombre' => 'Compra de emergencia'],
        ];

        foreach ($tipos as $tipo) {
            TipoCompra::updateOrCreate(['nombre' => $tipo['nombre']]);
        }
    }
}
