<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NcfSeeder extends Seeder
{
    public function run()
    {
        DB::table('ncf_sequences')->truncate();

        DB::table('ncf_sequences')->insert([
            [
                'nombre' => 'Crédito Fiscal (B01)',
                'prefijo' => 'B01',
                'desde' => 1,
                'hasta' => 1000,
                'actual' => 0,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Consumo (B02)',
                'prefijo' => 'B02',
                'desde' => 1,
                'hasta' => 5000,
                'actual' => 0,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Regímenes Especiales (B14)',
                'prefijo' => 'B14',
                'desde' => 1,
                'hasta' => 500,
                'actual' => 0,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Gubernamental (B15)',
                'prefijo' => 'B15',
                'desde' => 1,
                'hasta' => 500,
                'actual' => 0,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
