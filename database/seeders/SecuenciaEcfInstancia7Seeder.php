<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\SecuenciaEcf;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SecuenciaEcfInstancia7Seeder extends Seeder
{
    /**
     * Instancia (tenant) destino. Este seeder solo aplica a la instancia 7.
     */
    protected int $tenantId = 7;

    public function run(): void
    {
        $tenantId = $this->tenantId;

        $instancia = BusinessInstance::withTrashed()->find($tenantId);
        if (! $instancia || $instancia->trashed()) {
            $this->command->warn(
                "Instancia {$tenantId} no encontrada (o eliminada). Se omite SecuenciaEcfInstancia7Seeder."
            );
            return;
        }

        $this->command->info("Instancia {$tenantId} encontrada: {$instancia->nombre}");

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

        $creados = 0;
        foreach ($secuencias as $data) {
            $secuencia = SecuenciaEcf::withoutGlobalScopes()->firstOrCreate(
                ['tenant_id' => $tenantId, 'tipo_ecf' => $data['tipo_ecf']],
                array_merge($data, ['tenant_id' => $tenantId])
            );

            if ($secuencia->wasRecentlyCreated) {
                $creados++;
                $this->command->info("Secuencia creada para la instancia {$tenantId}: {$data['tipo_ecf']} - {$data['nombre']}");
            } else {
                $this->command->info("Secuencia ya existe para la instancia {$tenantId}: {$data['tipo_ecf']} - {$data['nombre']}");
            }
        }

        $this->command->info(
            "SecuenciaEcfInstancia7Seeder finalizado. {$creados} secuencias nuevas para la instancia {$tenantId}."
        );
    }
}
