<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NcfSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ncf_sequences')->insert([
            [
                'nombre' => 'Factura de Crédito Fiscal',
                'prefijo' => 'B01',
                'desde' => 1,
                'hasta' => 1000,
                'actual' => 1,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Factura de Consumo',
                'prefijo' => 'B02',
                'desde' => 1,
                'hasta' => 10000,
                'actual' => 1,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Regímenes Especiales',
                'prefijo' => 'B14',
                'desde' => 1,
                'hasta' => 100,
                'actual' => 1,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Gubernamentales',
                'prefijo' => 'B15',
                'desde' => 1,
                'hasta' => 100,
                'actual' => 1,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
        ]);
    }
}
