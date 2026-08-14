<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\SecuenciaEcf;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SecuenciaEcfSeedCommand extends Command
{
    protected $signature = 'secuencias:ecf:seed
                            {id : ID de la instancia (BusinessInstance)}
                            {--force : Forzar sin confirmacion}';

    protected $description = 'Sembrar secuencias e-CF de DGII para una instancia especifica';

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
        $this->line(" Tipo de negocio: " . ($instancia->businessType->nombre ?? 'N/A'));

        if (! $force) {
            if (! $this->confirm("¿Desea sembrar las secuencias e-CF para esta instancia?")) {
                $this->info('Operacion cancelada.');
                return Command::FAILURE;
            }
        }

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

        $createCount = 0;
        $existingCount = 0;
        $updateCount = 0;

        $this->info("\nProcesando " . count($secuencias) . " secuencias e-CF...\n");
        $this->newLine();

        foreach ($secuencias as $data) {
            $exists = SecuenciaEcf::withoutGlobalScopes()
                ->where('tenant_id', $instancia->id)
                ->where('tipo_ecf', $data['tipo_ecf'])
                ->first();

            if ($exists) {
                $exists->update($data);
                $updateCount++;
                $this->line("<fg=yellow>• Actualizado:</> {$data['tipo_ecf']} - {$data['nombre']}");
            } else {
                SecuenciaEcf::withoutGlobalScopes()->create(
                    array_merge($data, ['tenant_id' => $instancia->id])
                );
                $createCount++;
                $this->line("<fg=green>• Creado:</> {$data['tipo_ecf']} - {$data['nombre']}");
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

        $this->info("\n¡Secuencias e-CF sembradas exitosamente!");
        return Command::SUCCESS;
    }
}
