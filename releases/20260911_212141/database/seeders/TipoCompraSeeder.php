<?php

namespace Database\Seeders;

use App\Models\TipoCompra;
use Illuminate\Database\Seeder;

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
