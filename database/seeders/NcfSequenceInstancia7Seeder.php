<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\NcfSequence;
use Illuminate\Database\Seeder;

class NcfSequenceInstancia7Seeder extends Seeder
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
                "Instancia {$tenantId} no encontrada (o eliminada). Se omite NcfSequenceInstancia7Seeder."
            );
            return;
        }

        $this->command->info("Instancia {$tenantId} encontrada: {$instancia->nombre}");

        $secuencias = [
            [
                'nombre' => 'Factura de Crédito Fiscal',
                'prefijo' => 'B01',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Factura de Consumo',
                'prefijo' => 'B02',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Nota de Débito',
                'prefijo' => 'B03',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Nota de Crédito',
                'prefijo' => 'B04',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Comprobante de Compras',
                'prefijo' => 'B05',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Nota de Crédito Fiscal',
                'prefijo' => 'B06',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Regímenes Especiales',
                'prefijo' => 'B14',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Gubernamental',
                'prefijo' => 'B15',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Exportación / Zonas Francas',
                'prefijo' => 'B16',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Ventas Turísticas',
                'prefijo' => 'B17',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
            [
                'nombre' => 'Factura Gubernamental',
                'prefijo' => 'B31',
                'desde' => 1,
                'hasta' => 99999999,
                'fecha_vencimiento' => '2026-12-31',
            ],
        ];

        $creados = 0;
        foreach ($secuencias as $data) {
            $sequence = NcfSequence::withoutGlobalScopes()->firstOrCreate(
                ['tenant_id' => $tenantId, 'prefijo' => $data['prefijo']],
                array_merge($data, [
                    'actual' => $data['desde'],
                    'activo' => true,
                    'tenant_id' => $tenantId,
                ])
            );

            if ($sequence->wasRecentlyCreated) {
                $creados++;
                $this->command->info("Secuencia creada para la instancia {$tenantId}: {$data['prefijo']} ({$data['nombre']})");
            } else {
                $this->command->info("Secuencia ya existe para la instancia {$tenantId}: {$data['prefijo']} ({$data['nombre']})");
            }
        }

        $this->command->info(
            "NcfSequenceInstancia7Seeder finalizado. {$creados} secuencias nuevas para la instancia {$tenantId}."
        );
    }
}