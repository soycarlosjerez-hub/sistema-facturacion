<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByModule = [
            'dashboard' => [
                'dashboard.view',
            ],
            'owner' => [
                'owner.dashboard',
                'owner.instances.view',
                'owner.instances.create',
                'owner.instances.edit',
                'owner.instances.delete',
                'owner.business-types.view',
                'owner.business-types.create',
                'owner.business-types.edit',
                'owner.business-types.delete',
                'owner.users.view',
                'owner.users.manage',
            ],
            'reportes' => [
                'reportes.view',
                'reportes.export',
                'reportes.restaurante',
            ],
            'ventas' => [
                'ventas.view',
                'ventas.create',
                'ventas.view.own',
                'ventas.anular',
                'ventas.export',
            ],
            'cotizaciones' => [
                'cotizaciones.view',
                'cotizaciones.create',
                'cotizaciones.edit',
                'cotizaciones.delete',
                'cotizaciones.export',
                'cotizaciones.convertir',
                'cotizaciones.enviar',
            ],
            'conduces' => [
                'conduces.view',
                'conduces.create',
                'conduces.edit',
                'conduces.delete',
                'conduces.print',
                'conduces.deliver',
            ],
            'restaurante' => [
                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.anular',
                'restaurante.descuento',
                'restaurante.categorias',
                'restaurante.reservaciones',
                'restaurante.cajas',
                'restaurante.mesas.manage',
            ],
            'cajas' => [
                'cajas.view',
                'cajas.create',
                'cajas.edit',
                'cajas.delete',
                'cajas.open',
                'cajas.close',
                'cajas.view.report',
            ],
            'clientes' => [
                'clientes.view',
                'clientes.create',
                'clientes.edit',
                'clientes.delete',
            ],
            'cobros' => [
                'cobros.view',
                'cobros.create',
                'cobros.export',
            ],
            'productos' => [
                'productos.view',
                'productos.create',
                'productos.edit',
                'productos.delete',
                'productos.import',
                'productos.export',
            ],
            'compras' => [
                'compras.view',
                'compras.create',
                'compras.edit',
                'compras.delete',
                'compras.export',
            ],
            'proveedores' => [
                'proveedores.view',
                'proveedores.create',
                'proveedores.edit',
                'proveedores.delete',
            ],
            'almacenes' => [
                'almacenes.view',
                'almacenes.create',
                'almacenes.edit',
                'almacenes.delete',
                'almacenes.movements',
            ],
            'kardex' => [
                'kardex.view',
                'kardex.export',
            ],
            'ncf' => [
                'ncf.view',
                'ncf.manage',
            ],
            'ecf' => [
                'ecf.view',
                'ecf.manage',
                'ecf.send',
                'ecf.certificados',
            ],
            'impresoras' => [
                'impresoras.view',
                'impresoras.create',
                'impresoras.edit',
                'impresoras.delete',
                'impresoras.print',
            ],
            'gastos' => [
                'gastos.view',
                'gastos.create',
                'gastos.edit',
                'gastos.delete',
            ],
            'plantilla-gastos' => [
                'plantilla-gastos.view',
                'plantilla-gastos.create',
                'plantilla-gastos.edit',
                'plantilla-gastos.delete',
            ],
            'auditoria' => [
                'auditoria.view',
            ],
            'sucursales' => [
                'sucursales.view',
                'sucursales.create',
                'sucursales.edit',
                'sucursales.delete',
            ],
            'devoluciones' => [
                'devoluciones.view',
                'devoluciones.create',
                'devoluciones.confirmar',
                'devoluciones.delete',
            ],
            'listas-precio' => [
                'listas-precio.view',
                'listas-precio.create',
                'listas-precio.edit',
                'listas-precio.delete',
            ],
            'retail' => [
                'retail.terminal',
                'retail.inventario',
                'retail.compras',
                'retail.devoluciones',
            ],
            'mayorista' => [
                'mayorista.precios_volumen',
                'mayorista.conduces',
                'mayorista.listado_precios',
            ],
            'servicios' => [
                'servicios.cotizaciones',
                'servicios.facturacion_horas',
                'servicios.gastos',
            ],
            'payment-processors' => [
                'payment-processors.view',
                'payment-processors.create',
                'payment-processors.edit',
                'payment-processors.delete',
            ],
            'backups' => [
                'backups.view',
                'backups.create',
                'backups.delete',
            ],
            'lavadero' => [
                'lavadero.view',
                'lavadero.servicios',
                'lavadero.vehiculos',
                'lavadero.citas',
                'lavadero.lavadores',
            ],
            'configuracion' => [
                'configuracion.view',
                'configuracion.edit',
            ],
            'usuarios' => [
                'usuarios.view',
                'usuarios.manage',
            ],
            'roles' => [
                'roles.view',
                'roles.manage',
            ],
        ];

        $allPermissions = [];
        foreach ($permissionsByModule as $module => $perms) {
            foreach ($perms as $p) {
                $perm = Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
                $allPermissions[] = $perm->name;
            }
        }

        $rolePermissions = [
            'admin' => $allPermissions,

            'root' => $allPermissions,

            'owner' => [
                // Owner-specific permissions
                'owner.dashboard',
                'owner.instances.view',
                'owner.instances.create',
                'owner.instances.edit',
                'owner.instances.delete',
                'owner.business-types.view',
                'owner.business-types.create',
                'owner.business-types.edit',
                'owner.business-types.delete',
                'owner.users.view',
                'owner.users.manage',
                
                // System configuration (to change "Colmado Premium", etc.)
                'configuracion.view',
                'configuracion.edit',
                
                // System monitoring
                'auditoria.view',
                'backups.view',
                'backups.create',
                
                // Reports (for system oversight)
                'reportes.view',
                'reportes.export',
                
                // Business modules (to see sidebar sections)
                'productos.view',
                'listas-precio.view',
                'compras.view',
                'proveedores.view',
                'kardex.view',
                'ventas.view',
                'ventas.create',
                'ventas.anular',
                'cotizaciones.view',
                'cotizaciones.create',
                'conduces.view',
                'devoluciones.view',
                'gastos.view',
                'plantilla-gastos.view',
                'clientes.view',
                'cobros.view',
                'cajas.view',
                'cajas.open',
                'almacenes.view',
                'almacenes.movements',
                'lavadero.view',
                'restaurante.view',
                'sucursales.view',
                'ncf.view',
                'ecf.view',
                'impresoras.view',
                'payment-processors.view',
                'usuarios.view',
                'roles.view',
            ],

            'admin-business' => [
                'dashboard.view',
                'reportes.view',
                'reportes.export',
                'reportes.restaurante',

                'ventas.view',
                'ventas.create',
                'ventas.anular',
                'ventas.export',

                'cotizaciones.view',
                'cotizaciones.create',
                'cotizaciones.edit',
                'cotizaciones.delete',
                'cotizaciones.export',
                'cotizaciones.convertir',

                'conduces.view',
                'conduces.create',
                'conduces.edit',
                'conduces.print',
                'conduces.deliver',

                'devoluciones.view',
                'devoluciones.create',
                'devoluciones.confirmar',

                'listas-precio.view',
                'listas-precio.create',
                'listas-precio.edit',

                'gastos.view',
                'gastos.create',
                'gastos.edit',

                'plantilla-gastos.view',
                'plantilla-gastos.create',
                'plantilla-gastos.edit',

                'auditoria.view',
                'backups.view',
                'backups.create',

                'payment-processors.view',
                'payment-processors.create',
                'payment-processors.edit',

                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.anular',
                'restaurante.descuento',
                'restaurante.categorias',
                'restaurante.reservaciones',
                'restaurante.cajas',
                'restaurante.mesas.manage',

                'lavadero.view',
                'lavadero.servicios',
                'lavadero.vehiculos',
                'lavadero.citas',
                'lavadero.lavadores',

                'cajas.view',
                'cajas.create',
                'cajas.edit',
                'cajas.open',
                'cajas.close',
                'cajas.view.report',

                'clientes.view',
                'clientes.create',
                'clientes.edit',

                'cobros.view',
                'cobros.create',
                'cobros.export',

                'productos.view',
                'productos.create',
                'productos.edit',
                'productos.import',
                'productos.export',

                'compras.view',
                'compras.create',
                'compras.edit',
                'compras.export',

                'proveedores.view',
                'proveedores.create',
                'proveedores.edit',

                'almacenes.view',
                'almacenes.create',
                'almacenes.edit',
                'almacenes.movements',

                'kardex.view',
                'kardex.export',

                'ncf.view',
                'ncf.manage',

                'ecf.view',
                'ecf.manage',
                'ecf.send',
                'ecf.certificados',

                'sucursales.view',
                'sucursales.create',
                'sucursales.edit',

                'configuracion.view',

                'usuarios.view',
                'roles.view',
            ],

            'gerente' => [
                'dashboard.view',
                'reportes.view',
                'reportes.export',
                'reportes.restaurante',

                'ventas.view',
                'ventas.create',
                'ventas.anular',
                'ventas.export',

                'cotizaciones.view',
                'cotizaciones.create',
                'cotizaciones.edit',
                'cotizaciones.delete',
                'cotizaciones.export',
                'cotizaciones.convertir',

                'conduces.view',
                'conduces.create',
                'conduces.edit',
                'conduces.print',
                'conduces.deliver',

                'devoluciones.view',
                'devoluciones.create',
                'devoluciones.confirmar',

                'listas-precio.view',
                'listas-precio.create',
                'listas-precio.edit',

                'gastos.view',
                'gastos.create',
                'gastos.edit',

                'plantilla-gastos.view',
                'plantilla-gastos.create',
                'plantilla-gastos.edit',

                'auditoria.view',
                'backups.view',
                'backups.create',

                'payment-processors.view',
                'payment-processors.create',
                'payment-processors.edit',

                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.anular',
                'restaurante.descuento',
                'restaurante.categorias',
                'restaurante.reservaciones',
                'restaurante.cajas',
                'restaurante.mesas.manage',

                'lavadero.view',
                'lavadero.servicios',
                'lavadero.vehiculos',
                'lavadero.citas',
                'lavadero.lavadores',

                'cajas.view',
                'cajas.create',
                'cajas.edit',
                'cajas.open',
                'cajas.close',
                'cajas.view.report',

                'clientes.view',
                'clientes.create',
                'clientes.edit',

                'cobros.view',
                'cobros.create',
                'cobros.export',

                'productos.view',
                'productos.create',
                'productos.edit',
                'productos.import',
                'productos.export',

                'compras.view',
                'compras.create',
                'compras.edit',
                'compras.export',

                'proveedores.view',
                'proveedores.create',
                'proveedores.edit',

                'almacenes.view',
                'almacenes.create',
                'almacenes.edit',
                'almacenes.movements',

                'kardex.view',
                'kardex.export',

                'ncf.view',
                'ncf.manage',

                'ecf.view',
                'ecf.manage',
                'ecf.send',
                'ecf.certificados',

                'sucursales.view',
                'sucursales.create',
                'sucursales.edit',

                'configuracion.view',

                'usuarios.view',
                'roles.view',
            ],

            'vendedor' => [
                'dashboard.view',

                'reportes.view',
                'reportes.restaurante',

                'ventas.view.own',
                'ventas.create',

                'cotizaciones.view',
                'cotizaciones.create',
                'cotizaciones.edit',
                'cotizaciones.convertir',
                'conduces.view',
                'conduces.create',
                'conduces.edit',
                'conduces.print',
                'conduces.deliver',

                'devoluciones.view',
                'devoluciones.create',

                'listas-precio.view',

                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.anular',
                'restaurante.descuento',
                'restaurante.mesas.manage',
                'restaurante.cajas',
                'restaurante.categorias',
                'restaurante.reservaciones',

                'lavadero.view',
                'lavadero.servicios',
                'lavadero.vehiculos',
                'lavadero.citas',
                'lavadero.lavadores',

                'retail.terminal',
                'retail.inventario',
                'retail.compras',
                'retail.devoluciones',

                'mayorista.precios_volumen',
                'mayorista.conduces',
                'mayorista.listado_precios',

                'servicios.cotizaciones',
                'servicios.facturacion_horas',
                'servicios.gastos',

                'cajas.view',
                'cajas.open',
                'cajas.close',

                'clientes.view',
                'clientes.create',
                'clientes.edit',

                'cobros.create',

                'productos.view',
                'productos.create',
                'productos.edit',
                'productos.export',

                'compras.view',
                'compras.create',
                'compras.edit',
                'compras.export',

                'proveedores.view',
                'proveedores.create',
                'proveedores.edit',

                'almacenes.view',
                'almacenes.create',
                'almacenes.edit',
                'almacenes.movements',

                'kardex.view',
                'kardex.export',

                'gastos.view',
                'gastos.create',
                'gastos.edit',

                'plantilla-gastos.view',
                'plantilla-gastos.create',
                'plantilla-gastos.edit',
            ],

            'almacen' => [
                'dashboard.view',
                'reportes.view',

                'productos.view',
                'productos.create',
                'productos.edit',
                'productos.import',
                'productos.export',

                'compras.view',
                'compras.create',
                'compras.edit',
                'compras.export',

                'proveedores.view',
                'proveedores.create',
                'proveedores.edit',

                'almacenes.view',
                'almacenes.create',
                'almacenes.edit',
                'almacenes.movements',

                'kardex.view',
                'kardex.export',
            ],

            'contador' => [
                'dashboard.view',
                'reportes.view',
                'reportes.export',

                'ventas.view',
                'ventas.export',

                'cotizaciones.view',
                'cotizaciones.export',

                'cajas.view',
                'cajas.view.report',

                'clientes.view',

                'cobros.view',
                'cobros.export',

                'productos.view',
                'productos.export',

                'compras.view',
                'compras.export',

                'proveedores.view',

                'kardex.view',
                'kardex.export',

                'ncf.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
