<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceRole;
use App\Models\InstanceRoleModule;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateInstanceRoles extends Command
{
    protected $signature = 'instance-roles:generate {instance_id?} {--force : Sobreescribir roles existentes}';
    protected $description = 'Genera InstanceRoles con módulos completos para una instancia específica';

    private array $roleDefinitions = [
        'gerente' => [
            'label' => 'Gerente',
            'modules' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                'clientes', 'cajas', 'sucursales', 'almacenes', 'cuentas-bancarias',
                'reportes-caja', 'reportes-restaurante', 'reportes-resumen', 'reportes-gastos',
                'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades',
                'reportes-retenciones', 'reportes-fiscales',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras',
                'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors', 'delivery-companies',
                'auditoria', 'backups', 'plantilla-gastos',
            ],
        ],
        'contador' => [
            'label' => 'Contador',
            'modules' => [
                'dashboard', 'clientes', 'proveedores', 'kardex',
                'reportes-caja', 'reportes-gastos', 'reportes-resumen',
                'reportes-ventas', 'reportes-compras', 'reportes-utilidades',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-stock',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras',
                'formulario-14-14',
                'cuentas-bancarias', 'auditoria',
            ],
        ],
        'cajero' => [
            'label' => 'Cajero',
            'modules' => [
                'dashboard', 'clientes', 'cajas', 'reportes-caja',
                'reportes-resumen',
                'ncf', 'ecf',
            ],
        ],
        'mesero' => [
            'label' => 'Mesero',
            'modules' => [
                'dashboard', 'clientes',
            ],
        ],
        'cocinero' => [
            'label' => 'Cocinero',
            'modules' => [
                'dashboard',
            ],
        ],
        'almacen' => [
            'label' => 'Almacén',
            'modules' => [
                'dashboard', 'inventario', 'kardex', 'almacenes',
                'reportes-stock',
            ],
        ],
    ];

    public function handle(): int
    {
        $instanceId = $this->argument('instance_id');
        $force = $this->option('force');

        if ($instanceId) {
            $instance = BusinessInstance::with('businessType')->find($instanceId);
            if (!$instance) {
                $this->error("Instancia ID {$instanceId} no encontrada");
                return 1;
            }
            return $this->processInstance($instance, $force);
        }

        $instances = BusinessInstance::with('businessType')->get();
        if ($instances->isEmpty()) {
            $this->warn('No hay instancias en la base de datos');
            return 0;
        }

        $this->info("Procesando {$instances->count()} instancia(s)...");
        foreach ($instances as $instance) {
            $this->newLine();
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Instancia: {$instance->nombre} (ID: {$instance->id})");
            $this->info("Tipo: " . ($instance->businessType ? $instance->businessType->nombre : 'Sin tipo'));
            $this->processInstance($instance, $force);
        }

        return 0;
    }

    private function processInstance(BusinessInstance $instance, bool $force): int
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($this->roleDefinitions as $roleKey => $definition) {
            $role = InstanceRole::firstOrCreate(
                ['business_instance_id' => $instance->id, 'name' => $roleKey],
                ['guard_name' => 'instance']
            );

            if ($role->wasRecentlyCreated) {
                $created++;
                $this->info("  ✓ Creado: {$definition['label']}");
            } else {
                $this->line("  - Existente: {$definition['label']}");
            }

            $currentModuleCount = $role->modules->count();
            $targetModuleCount = count($definition['modules']);

            if ($force || $currentModuleCount === 0) {
                $role->modules()->delete();
                $orden = 0;
                foreach ($definition['modules'] as $moduloKey) {
                    $role->modules()->create([
                        'modulo_key' => $moduloKey,
                        'is_visible' => true,
                        'orden' => $orden++,
                    ]);
                }
                $updated++;
                $this->line("    → Actualizado: {$targetModuleCount} módulos asignados");
            } else {
                $skipped++;
                $this->line("    → Saltado (ya tiene {$currentModuleCount} módulos, usa --force para actualizar)");
            }

            // Asignar usuarios que coincidan con el role key
            $affectedUsers = User::where('business_instance_id', $instance->id)
                ->where('role', $roleKey)
                ->update(['instance_role_id' => $role->id]);

            if ($affectedUsers > 0) {
                $this->line("    → {$affectedUsers} usuario(s) asignado(s) a este rol");
            }
        }

        $this->newLine();
        $this->info("  Resultado: {$created} cread@s, {$updated} actualizad@s, {$skipped} saltados");

        return 0;
    }
}
