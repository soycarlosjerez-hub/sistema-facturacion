<?php

namespace Database\Seeders;

use App\Models\SecuenciaEcf;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SecuenciaEcfSeeder extends Seeder
{
    public function run(): void
    {
        $secuencias = [
            [
                'nombre' => 'Crédito Fiscal (DGII)',
                'tipo_ecf' => 'E31',
                'desde' => 1,
                'hasta' => 10000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'activo' => true,
                'descripcion' => 'Comprobantes para crédito fiscal - Clientes con RNC que requieren crédito tributario',
            ],
            [
                'nombre' => 'Consumo (DGII)',
                'tipo_ecf' => 'E32',
                'desde' => 1,
                'hasta' => 50000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'activo' => true,
                'descripcion' => 'Comprobantes de Consumo - Ventas a consumidores finales',
            ],
            [
                'nombre' => 'Nota de Crédito (DGII)',
                'tipo_ecf' => 'E34',
                'desde' => 1,
                'hasta' => 2000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'activo' => true,
                'descripcion' => 'Notas de Crédito - Anulación de comprobantes',
            ],
            [
                'nombre' => 'Nota de Débito (DGII)',
                'tipo_ecf' => 'E33',
                'desde' => 1,
                'hasta' => 2000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'activo' => true,
                'descripcion' => 'Notas de Débito - Corrección al alza de comprobantes',
            ],
            [
                'nombre' => 'Compras (DGII)',
                'tipo_ecf' => 'E41',
                'desde' => 1,
                'hasta' => 5000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(2),
                'activo' => true,
                'descripcion' => 'Comprobantes de compras recibidas',
            ],
        ];

        foreach ($secuencias as $data) {
            SecuenciaEcf::updateOrCreate(
                ['tipo_ecf' => $data['tipo_ecf']],
                $data
            );
        }

        $this->command->info('✓ ' . count($secuencias) . ' secuencias e-CF creadas');
    }
}
