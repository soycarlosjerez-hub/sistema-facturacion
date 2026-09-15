<?php

namespace Database\Seeders;

use App\Models\InstanceRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Crea y sincroniza roles de instancia y sus módulos para todas las
 * instancias activas (sin eliminar datos de negocio).
 *
 * Uso: php artisan db:seed --class=InstanceRolesCatalogSeeder
 * NO se ejecuta desde DatabaseSeeder (no tocar Full/* en prod).
 */
class InstanceRolesCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Limpieza pre-sincronización: renombrar/eliminar roles legacy.
        // Renombrar 'technico' -> 'tecnico' si existe.
        // Si 'tecnico' ya existe (ej. por re-seed), fusionar los módulos.
        if (DB::table('instance_roles')->where('business_instance_id', 10)->where('name', 'technico')->exists()) {
            if (DB::table('instance_roles')->where('business_instance_id', 10)->where('name', 'tecnico')->exists()) {
                // 'tecnico' ya existe: borrar 'technico' (sus módulos quedan con 'tecnico').
                DB::table('instance_roles')
                    ->where('business_instance_id', 10)
                    ->where('name', 'technico')
                    ->delete();
            } else {
                DB::table('instance_roles')
                    ->where('business_instance_id', 10)
                    ->where('name', 'technico')
                    ->update(['name' => 'tecnico']);
            }
        }

        // Eliminar rol genérico 'soporte' de MaganTech ANTES de sync (para que no se regenere).
        DB::table('instance_roles')
            ->where('business_instance_id', 10)
            ->where('name', 'soporte')
            ->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->synchronizeInstance(3, [
            'admin' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                'clientes', 'cajas', 'sucursales', 'almacenes', 'cuentas-bancarias',
                'reportes-caja', 'reportes-restaurante', 'reportes-resumen', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'payment-processors', 'delivery-companies',
                'delivery-dashboard', 'delivery-drivers', 'delivery-zones',
                'delivery-tracking', 'delivery-earnings',
                'auditoria', 'backups', 'plantilla-gastos',
            ],
            'gerente' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                'clientes', 'cajas', 'sucursales', 'almacenes', 'cuentas-bancarias',
                'reportes-caja', 'reportes-restaurante', 'reportes-resumen', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'payment-processors', 'delivery-companies',
                'auditoria', 'backups', 'plantilla-gastos',
            ],
            'mesero' => ['dashboard', 'restaurante', 'clientes'],
            'cocinero' => ['dashboard', 'restaurante-kds'],
            'bartender' => ['dashboard', 'restaurante'],
            'delivery' => [
                'dashboard', 'restaurante', 'clientes',
                'delivery-dashboard', 'delivery-drivers', 'delivery-zones',
                'delivery-tracking', 'delivery-earnings', 'delivery-companies',
            ],
            'cajero' => ['dashboard', 'restaurante', 'clientes', 'cajas', 'reportes-caja'],
            'contador' => [
                'dashboard', 'clientes', 'proveedores', 'kardex',
                'ncf', 'ecf', 'cuentas-bancarias',
                'reportes-caja', 'reportes-gastos', 'reportes-resumen', 'auditoria',
            ],
        ]);

        // ─── Inst. 5 — Gato Negro (lavadero) ───
        $this->synchronizeInstance(5, [
            'admin' => [
                'dashboard', 'lavadero', 'lavadero-servicios', 'lavadero-vehiculos',
                'lavadero-citas', 'lavadero-lavadores',
                'clientes', 'cajas', 'gastos', 'inventario', 'compras', 'proveedores',
                'reportes-ventas', 'reportes-caja', 'reportes-stock',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'sucursales', 'almacenes', 'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups', 'plantilla-gastos',
            ],
            'gerente' => [
                'dashboard', 'clientes', 'cajas', 'gastos', 'plantilla-gastos',
                'inventario', 'compras', 'proveedores', 'almacenes', 'sucursales',
                'cuentas-bancarias',
                'reportes-ventas', 'reportes-caja', 'reportes-stock',
                'reportes-resumen', 'reportes-gastos',
                'reportes-retenciones', 'reportes-fiscales',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups',
            ],
            'cajero' => ['dashboard', 'lavadero', 'lavadero-servicios', 'clientes', 'cajas', 'reportes-caja'],
            'lavador' => ['dashboard', 'lavadero', 'lavadero-vehiculos', 'lavadero-citas'],
            'vendedor' => ['dashboard', 'lavadero', 'lavadero-servicios', 'clientes'],
            'contador' => [
                'dashboard', 'clientes', 'proveedores', 'ncf', 'ecf', 'cuentas-bancarias',
                'reportes-caja', 'reportes-gastos', 'reportes-resumen',
                'reportes-retenciones', 'reportes-fiscales',
                'libros-ventas', 'libros-compras', 'formulario-14-14', 'auditoria',
            ],
            'almacen' => ['dashboard', 'inventario', 'compras', 'proveedores', 'almacenes', 'sucursales', 'reportes-stock'],
        ]);

        // ─── Inst. 6 — Tecno Plus (tecnología) ───
        $techRoles = $this->techRoles();
        $this->synchronizeInstance(6, $techRoles);

        // ─── Inst. 10 — MaganTech (tecnología) ───
        $this->synchronizeInstance(10, $techRoles);

        // ─── Inst. 7 — Colmado (retail) ───
        $this->synchronizeInstance(7, [
            'admin' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex',
                'ventas', 'devoluciones', 'ordenes', 'ordenes-kds',
                'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                'reportes-ventas', 'reportes-compras', 'reportes-stock',
                'reportes-utilidades', 'reportes-caja',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'delivery-companies', 'delivery-dashboard', 'delivery-drivers',
                'delivery-zones', 'delivery-tracking', 'delivery-earnings',
                'auditoria', 'backups', 'plantilla-gastos',
            ],
            'gerente' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex',
                'ventas', 'devoluciones', 'ordenes',
                'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                'reportes-ventas', 'reportes-compras', 'reportes-stock',
                'reportes-utilidades', 'reportes-caja',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups', 'plantilla-gastos',
            ],
            'cajero' => ['dashboard', 'ventas', 'devoluciones', 'ordenes', 'clientes', 'cobros', 'cajas', 'reportes-caja'],
            'vendedor' => ['dashboard', 'ventas', 'devoluciones', 'ordenes', 'ordenes-kds', 'clientes', 'inventario'],
            'almacen' => ['dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'almacenes', 'sucursales', 'reportes-compras', 'reportes-stock'],
            'contador' => [
                'dashboard', 'clientes', 'proveedores', 'ncf', 'ecf', 'cuentas-bancarias',
                'reportes-caja', 'reportes-gastos', 'reportes-resumen',
                'reportes-retenciones', 'reportes-fiscales',
                'libros-ventas', 'libros-compras', 'formulario-14-14', 'kardex', 'auditoria',
            ],
            'delivery' => [
                'dashboard', 'ventas', 'clientes', 'ordenes',
                'delivery-dashboard', 'delivery-drivers', 'delivery-zones',
                'delivery-tracking', 'delivery-earnings', 'delivery-companies',
            ],
        ]);

        // ─── Inst. 8 — Armada (mecánica) ───
        $this->synchronizeInstance(8, [
            'admin' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex',
                'listas-precio', 'ventas', 'devoluciones', 'ordenes',
                'cotizaciones', 'conduces',
                'clientes', 'cobros', 'cajas', 'gastos', 'plantilla-gastos',
                'sucursales', 'almacenes',
                'reportes-ventas', 'reportes-compras', 'reportes-stock',
                'reportes-utilidades', 'reportes-caja',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups',
            ],
            'gerente' => [
                'dashboard', 'inventario', 'compras', 'proveedores', 'kardex',
                'listas-precio', 'ventas', 'devoluciones', 'ordenes',
                'cotizaciones', 'conduces',
                'clientes', 'cobros', 'cajas', 'gastos', 'plantilla-gastos',
                'sucursales', 'almacenes',
                'reportes-ventas', 'reportes-compras', 'reportes-stock',
                'reportes-utilidades', 'reportes-caja',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups',
            ],
            'cajero' => ['dashboard', 'ventas', 'cotizaciones', 'clientes', 'cobros', 'cajas', 'reportes-caja'],
            'vendedor' => ['dashboard', 'inventario', 'ventas', 'cotizaciones', 'conduces', 'ordenes', 'devoluciones', 'clientes', 'kardex'],
            'mecanico' => ['dashboard', 'inventario', 'ventas', 'cotizaciones', 'conduces', 'ordenes', 'devoluciones', 'clientes', 'kardex'],
            'almacen' => ['dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'almacenes', 'sucursales', 'reportes-stock', 'reportes-compras'],
            'contador' => [
                'dashboard', 'clientes', 'proveedores', 'ncf', 'ecf', 'cuentas-bancarias',
                'reportes-caja', 'reportes-gastos', 'reportes-resumen',
                'reportes-retenciones', 'reportes-fiscales',
                'libros-ventas', 'libros-compras', 'formulario-14-14', 'kardex', 'auditoria',
            ],
        ]);

        // ─── Inst. 9 — Arte (arte_escultura) ───
        $this->synchronizeInstance(9, [
            'admin' => [
                'dashboard', 'arte', 'arte-obras', 'arte-artistas',
                'arte-colecciones', 'arte-exhibiciones', 'arte-consignaciones',
                'inventario', 'compras', 'proveedores', 'kardex',
                'ventas', 'devoluciones', 'ordenes',
                'clientes', 'cobros', 'cajas', 'gastos', 'plantilla-gastos',
                'sucursales', 'almacenes',
                'reportes-ventas', 'reportes-compras', 'reportes-stock',
                'reportes-utilidades', 'reportes-caja',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups',
            ],
            'gerente' => [
                'dashboard', 'arte', 'arte-obras', 'arte-artistas',
                'arte-colecciones', 'arte-exhibiciones', 'arte-consignaciones',
                'inventario', 'compras', 'proveedores', 'kardex',
                'ventas', 'devoluciones', 'ordenes',
                'clientes', 'cobros', 'cajas', 'gastos', 'plantilla-gastos',
                'sucursales', 'almacenes',
                'reportes-ventas', 'reportes-compras', 'reportes-stock',
                'reportes-utilidades', 'reportes-caja',
                'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                'cuentas-bancarias', 'reportes-gastos',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups',
            ],
            'vendedor-galeria' => ['dashboard', 'arte', 'arte-obras', 'arte-artistas', 'arte-colecciones', 'arte-exhibiciones', 'arte-consignaciones', 'ventas', 'clientes'],
            'cajero' => ['dashboard', 'ventas', 'devoluciones', 'clientes', 'cobros', 'cajas', 'reportes-caja'],
            'almacen' => ['dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'almacenes', 'reportes-stock'],
            'contador' => [
                'dashboard', 'clientes', 'proveedores', 'ncf', 'ecf', 'cuentas-bancarias',
                'reportes-caja', 'reportes-gastos', 'reportes-resumen',
                'reportes-retenciones', 'reportes-fiscales',
                'libros-ventas', 'libros-compras', 'formulario-14-14', 'kardex', 'auditoria',
            ],
        ]);

        // Renombrar 'technico' -> 'tecnico' en MaganTech.
        // (Ya hecho arriba, antes del sync).

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Roles de instancia sincronizados y limpiados.');
    }

    /**
     * Crea/sincroniza roles de instancia con sus módulos.
     *
     * @param  array<string, array<int, string>>  $roleModules
     */
    private function synchronizeInstance(int $instanceId, array $roleModules): void
    {
        $this->command->info("Instancia #{$instanceId}:");

        foreach ($roleModules as $roleName => $modules) {
            $instanceRole = InstanceRole::firstOrCreate(
                ['business_instance_id' => $instanceId, 'name' => $roleName],
                ['guard_name' => 'instance']
            );
            $instanceRole->syncModules($modules);
            $this->command->info("  {$roleName}: " . count($modules) . " módulos");
        }
    }

    /**
     * Roles de tecnología (tecnologia, tipo 10).
     *
     * @return array<string, array<int, string>>
     */
    private function techRoles(): array
    {
        $techBase = [
            'tecnologia', 'dashboard', 'inventario', 'compras', 'proveedores',
            'clientes', 'cajas', 'gastos',
            'equipos', 'tecnicas', 'tecnicos',
            'domotica', 'garantias',
            'marcas-tecnologicas', 'licencias-software', 'redes-config',
            'presupuestos', 'tecnica-especialidades', 'garantias-config',
            'sucursales', 'almacenes',
            'reportes-ventas', 'reportes-caja', 'reportes-stock',
            'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
            'reportes-gastos',
            'cuentas-bancarias',
            'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
            'libros-ventas', 'libros-compras', 'formulario-14-14',
            'configuracion-general', 'impresoras', 'payment-processors',
            'auditoria', 'backups', 'plantilla-gastos',
        ];

        return [
            'admin' => $techBase,
            'gerente' => [
                'dashboard', 'clientes', 'cajas', 'gastos', 'plantilla-gastos',
                'inventario', 'compras', 'proveedores', 'almacenes', 'sucursales',
                'cuentas-bancarias', 'tecnologia', 'equipos', 'presupuestos',
                'reportes-ventas', 'reportes-caja', 'reportes-stock',
                'reportes-resumen', 'reportes-gastos',
                'reportes-retenciones', 'reportes-fiscales',
                'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                'libros-ventas', 'libros-compras', 'formulario-14-14',
                'configuracion-general', 'impresoras', 'payment-processors',
                'auditoria', 'backups',
            ],
            'cajero' => ['dashboard', 'tecnologia', 'equipos', 'presupuestos', 'clientes', 'cajas', 'reportes-caja'],
            'tecnico' => ['dashboard', 'tecnologia', 'equipos', 'tecnicas', 'tecnicos', 'domotica', 'garantias', 'garantias-config', 'tecnica-especialidades', 'clientes'],
            'soporte-n1' => ['dashboard', 'tecnologia', 'equipos', 'tecnicos', 'garantias', 'redes-config', 'licencias-software', 'clientes'],
            'soporte-n2' => ['dashboard', 'tecnologia', 'equipos', 'tecnicos', 'garantias', 'redes-config', 'licencias-software', 'clientes'],
            'redes' => ['dashboard', 'tecnologia', 'redes-config', 'licencias-software', 'equipos', 'clientes'],
            'almacen-tech' => ['dashboard', 'inventario', 'compras', 'proveedores', 'almacenes', 'sucursales', 'marcas-tecnologicas', 'reportes-stock'],
            'contador' => [
                'dashboard', 'clientes', 'proveedores', 'ncf', 'ecf', 'cuentas-bancarias',
                'reportes-caja', 'reportes-gastos', 'reportes-stock',
                'reportes-resumen', 'reportes-retenciones', 'reportes-fiscales',
                'libros-ventas', 'libros-compras', 'formulario-14-14', 'auditoria',
            ],
            'vendedor-tecnico' => ['dashboard', 'tecnologia', 'equipos', 'presupuestos', 'clientes', 'cajas', 'reportes-caja'],
        ];
    }
}
