<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\NcfSequence;
use Illuminate\Console\Command;

class NcfSequenceSeedCommand extends Command
{
    protected $signature = 'secuencias:ncf:seed
                            {id : ID de la instancia (BusinessInstance)}
                            {--force : Forzar sin confirmacion}';

    protected $description = 'Sembrar secuencias NCF para una instancia especifica';

    public function handle(): int
    {
        $instanceId = $this->argument('id');
        $force = $this->option('force');

        $instancia = BusinessInstance::withTrashed()->find($instanceId);

        if (! $instancia) {
            $this->error("Instancia con ID {$instanceId} no encontrada.");
            return Command::FAILURE;
        }

        if ($instancia->trashed()) {
            $this->error("Instancia '{$instancia->nombre}' ha sido eliminada (trashed).");
            return Command::FAILURE;
        }

        $this->info("Instancia: {$instancia->nombre} (ID: {$instancia->id})");
        $this->line(" Tipo de negocio: {" . ($instancia->businessType->nombre ?? 'N/A') . "}");

        if (! $force) {
            if (! $this->confirm("¿Desea sembrar las secuencias NCF para esta instancia?")) {
                $this->info('Operacion cancelada.');
                return Command::FAILURE;
            }
        }

        $secuencias = [
            [
                'nombre' => 'Factura de Crédito Fiscal',
                'prefijo' => 'B01',
                'desde' => 1,
                'hasta' => 100000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Factura de Consumo',
                'prefijo' => 'B02',
                'desde' => 1,
                'hasta' => 500000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Nota de Débito',
                'prefijo' => 'B03',
                'desde' => 1,
                'hasta' => 10000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Nota de Crédito',
                'prefijo' => 'B04',
                'desde' => 1,
                'hasta' => 10000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => true,
            ],
            [
                'nombre' => 'Comprobante de Compras',
                'prefijo' => 'B05',
                'desde' => 1,
                'hasta' => 10000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Nota de Crédito Fiscal',
                'prefijo' => 'B06',
                'desde' => 1,
                'hasta' => 10000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Regímenes Especiales de Tributación',
                'prefijo' => 'B07',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Gubernamental',
                'prefijo' => 'B08',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Ingresos',
                'prefijo' => 'B11',
                'desde' => 1,
                'hasta' => 10000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Gastos Menores',
                'prefijo' => 'B12',
                'desde' => 1,
                'hasta' => 10000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Regímenes Especiales de Gobierno',
                'prefijo' => 'B14',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Pago al Exterior',
                'prefijo' => 'B15',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Venta a Zonas Francas',
                'prefijo' => 'B16',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Ventas Turísticas',
                'prefijo' => 'B17',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
            [
                'nombre' => 'Factura Gubernamental',
                'prefijo' => 'B31',
                'desde' => 1,
                'hasta' => 1000,
                'fecha_vencimiento' => '2026-12-31',
                'activo' => false,
            ],
        ];

        $createCount = 0;
        $existingCount = 0;
        $updateCount = 0;

        $this->info("\nProcesando " . count($secuencias) . " secuencias NCF...\n");
        $this->newLine();

        foreach ($secuencias as $data) {
            $exists = NcfSequence::withoutGlobalScopes()
                ->where('tenant_id', $instancia->id)
                ->where('prefijo', $data['prefijo'])
                ->first();

            if ($exists) {
                $exists->update(array_merge($data, ['actual' => $data['desde']]));
                $updateCount++;
                $this->line("<fg=yellow>• Actualizado:</> {$data['prefijo']} - {$data['nombre']}");
            } else {
                NcfSequence::withoutGlobalScopes()->create(
                    array_merge($data, ['actual' => $data['desde'], 'tenant_id' => $instancia->id])
                );
                $createCount++;
                $this->line("<fg=green>• Creado:</> {$data['prefijo']} - {$data['nombre']}");
            }
        }

        $this->newLine();
        $this->table(
            ['Resumen'],
            [
                ['Instancia: ' . $instancia->nombre],
                ['Creadas: ' . $createCount],
                ['Actualizadas: ' . $updateCount],
                ['Total: ' . $createCount + $updateCount],
            ]
        );

        $this->info("\n¡Secuencias NCF sembradas exitosamente!");
        return Command::SUCCESS;
    }
}
