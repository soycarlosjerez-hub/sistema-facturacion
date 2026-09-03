<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\InstanceRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstanceRolesSeeder extends Seeder
{
    private array $allModulos = [
        'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
        'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
        'clientes', 'cajas', 'sucursales', 'almacenes', 'cuentas-bancarias',
        'reportes-caja', 'reportes-restaurante', 'reportes-resumen', 'reportes-gastos',
        'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades',
        'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
        'configuracion-general', 'payment-processors', 'delivery-companies',
        'delivery-dashboard', 'delivery-drivers', 'delivery-zones', 'delivery-tracking', 'delivery-earnings',
        'ventas', 'conduces', 'ordenes', 'ordenes-kds', 'cobros',
        'devoluciones', 'gastos', 'plantilla-gastos', 'cotizaciones',
        'auditoria', 'backups',
        'lavadero-vehiculos', 'lavadero-citas', 'lavadero-lavadores',
        'tecnologia', 'equipos', 'tecnicas', 'tecnicos', 'domotica', 'garantias',
        'marca-tecnologicas', 'licencias-software', 'redes-config', 'presupuestos',
        'tecnica-especialidades', 'garantias-config',
        'arte', 'arte-obras', 'arte-artistas', 'arte-colecciones', 'arte-exhibiciones', 'arte-consignaciones',
        'alquileres', 'alquileres-viviendas', 'alquileres-inquilinos', 'alquileres-contratos', 'alquileres-pagos',
        'climatizacion', 'climatizacion-tipos-equipos', 'climatizacion-instalaciones',
        'climatizacion-contratos', 'climatizacion-mantenimientos', 'climatizacion-ordenes-emergencia', 'climatizacion-garantias',
        'tattoo', 'tattoo-artistas', 'tattoo-disenos', 'tattoo-citas',
        'libros-ventas', 'libros-compras', 'reportes-retenciones', 'reportes-fiscales', 'formulario-14-14',
        'sgc-datos',
    ];

    private array $roleModules = [
        'gerente' => [
            'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
            'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
            'clientes', 'cajas', 'sucursales', 'almacenes', 'cuentas-bancarias',
            'reportes-caja', 'reportes-restaurante', 'reportes-resumen', 'reportes-gastos',
            'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
            'configuracion-general', 'impresoras', 'payment-processors', 'delivery-companies',
            'auditoria', 'backups', 'plantilla-gastos',
        ],
        'mesero' => [
            'dashboard', 'restaurante', 'clientes',
        ],
        'cocinero' => [
            'dashboard', 'restaurante-kds',
        ],
        'bartender' => [
            'dashboard', 'restaurante',
        ],
        'delivery' => [
            'dashboard', 'restaurante', 'clientes',
            'delivery-dashboard', 'delivery-tracking', 'delivery-earnings',
            'delivery-zones', 'delivery-companies',
            'conduces', 'ordenes',
        ],
        'cajero' => [
            'dashboard', 'restaurante', 'clientes', 'cajas', 'reportes-caja',
            'ventas', 'conduces', 'ordenes', 'cobros',
        ],
        'contador' => [
            'dashboard', 'clientes', 'proveedores', 'kardex',
            'ncf', 'ecf', 'cuentas-bancarias',
            'reportes-caja', 'reportes-gastos', 'reportes-resumen',
            'auditoria',
        ],
    ];

    public function run(): void
    {
        $instances = BusinessInstance::whereHas('businessType', fn($q) => $q->where('slug', 'restaurante'))->get();

        if ($instances->isEmpty()) {
            $this->command->warn('No se encontraron instancias de tipo restaurante.');
            return;
        }

        foreach ($instances as $instance) {
            $this->command->info("Procesando instancia: {$instance->nombre}");

            foreach ($this->roleModules as $roleName => $modulosVisibles) {
                $instanceRole = InstanceRole::firstOrCreate(
                    ['business_instance_id' => $instance->id, 'name' => $roleName],
                    ['guard_name' => 'instance']
                );

                // Sync modules for this role
                $instanceRole->syncModules($modulosVisibles);

                // Assign instance_role_id to matching users
                User::where('business_instance_id', $instance->id)
                    ->where('role', $roleName)
                    ->update(['instance_role_id' => $instanceRole->id]);

                $this->command->info("  Rol de instancia '{$roleName}' creado y asignado.");
            }
        }

        $this->command->info('InstanceRoles creados y asignados correctamente.');
    }
}
