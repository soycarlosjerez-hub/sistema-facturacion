<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
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
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes para crédito fiscal - Clientes con RNC que requieren crédito tributario',
            ],
            [
                'nombre' => 'Consumo (DGII)',
                'tipo_ecf' => 'E32',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes de Consumo - Ventas a consumidores finales',
            ],
            [
                'nombre' => 'Nota de Débito (DGII)',
                'tipo_ecf' => 'E33',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Notas de Débito - Corrección al alza de comprobantes',
            ],
            [
                'nombre' => 'Nota de Crédito (DGII)',
                'tipo_ecf' => 'E34',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Notas de Crédito - Anulación de comprobantes',
            ],
            [
                'nombre' => 'Compras (DGII)',
                'tipo_ecf' => 'E41',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes de compras recibidas',
            ],
            [
                'nombre' => 'Gastos Menores (DGII)',
                'tipo_ecf' => 'E43',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes para gastos menores',
            ],
            [
                'nombre' => 'Regímenes Especiales (DGII)',
                'tipo_ecf' => 'E44',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes para regímenes especiales',
            ],
            [
                'nombre' => 'Gubernamentales (DGII)',
                'tipo_ecf' => 'E45',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes para entidades gubernamentales',
            ],
            [
                'nombre' => 'Exportaciones (DGII)',
                'tipo_ecf' => 'E46',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes para exportaciones',
            ],
            [
                'nombre' => 'Pagos al Exterior (DGII)',
                'tipo_ecf' => 'E47',
                'desde' => 1,
                'hasta' => 500000,
                'actual' => 0,
                'fecha_vencimiento' => Carbon::now()->addYears(5),
                'activo' => true,
                'descripcion' => 'Comprobantes para pagos al exterior',
            ],
        ];

        $instancias = BusinessInstance::all();

        foreach ($instancias as $instancia) {
            foreach ($secuencias as $data) {
                $existing = SecuenciaEcf::withoutGlobalScopes()
                    ->where('tipo_ecf', $data['tipo_ecf'])
                    ->where('tenant_id', $instancia->id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'nombre'            => $data['nombre'],
                        'hasta'             => $data['hasta'],
                        'fecha_vencimiento' => $data['fecha_vencimiento'],
                        'activo'            => $data['activo'],
                        'descripcion'       => $data['descripcion'],
                    ]);
                } else {
                    SecuenciaEcf::withoutGlobalScopes()->create(
                        array_merge($data, ['tenant_id' => $instancia->id])
                    );
                }
            }
        }

        $total = count($secuencias) * $instancias->count();
        $this->command->info("✓ {$total} secuencias e-CF creadas para {$instancias->count()} instancias");
    }
}
