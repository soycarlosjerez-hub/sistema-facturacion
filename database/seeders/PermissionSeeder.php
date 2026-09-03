<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'productos.toggle',
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
            'ordenes' => [
                'ordenes.view',
                'ordenes.create',
                'ordenes.update',
                'ordenes.pay',
                'ordenes.cancel',
            ],
            'kds' => [
                'kds.view',
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

                'lavadero.insumos',
                'lavadero.insumos.create',
                'lavadero.insumos.edit',
                'lavadero.insumos.delete',
                'lavadero.insumos.consumo',
                'lavadero.insumos.reabaste',

                'lavadero.costo_real',

                'lavadero.utilidades',
                'lavadero.utilidades.create',
                'lavadero.utilidades.edit',
                'lavadero.utilidades.delete',

                'lavadero.productividad',
                'lavadero.productividad.registrar',

                'lavadero.membresias',
                'lavadero.membresias.create',
                'lavadero.membresias.edit',
                'lavadero.membresias.delete',

                'lavadero.membresia-usuarios',

                'lavadero.dashboard-new',
            ],
            'tattoo' => [
                'tattoo.view',
                'tattoo.artistas',
                'tattoo.disenos',
                'tattoo.citas',

                'arte.view',
                'arte.obras',
                'arte.artistas',
                'arte.colecciones',
                'arte.exhibiciones',
                'arte.consignaciones',
            ],
            'arte' => [
                'arte.view',
                'arte.obras',
                'arte.artistas',
                'arte.colecciones',
                'arte.exhibiciones',
                'arte.consignaciones',
            ],
            'cuentas-bancarias' => [
                'cuentas-bancarias.view',
                'cuentas-bancarias.create',
                'cuentas-bancarias.edit',
                'cuentas-bancarias.delete',
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
            'climatizacion' => [
                'climatizacion.view',
                'climatizacion.create',
                'climatizacion.edit',
                'climatizacion.delete',
                'climatizacion.export',
                'climatizacion.import',
            ],
            'climatizacion::tipo_clima' => [
                'view_any_climatizacion::tipo_clima',
                'view_climatizacion::tipo_clima',
                'create_climatizacion::tipo_clima',
                'update_climatizacion::tipo_clima',
                'delete_climatizacion::tipo_clima',
            ],
            'climatizacion::instalacion' => [
                'view_any_climatizacion::instalacion',
                'view_climatizacion::instalacion',
                'create_climatizacion::instalacion',
                'update_climatizacion::instalacion',
                'delete_climatizacion::instalacion',
            ],
            'climatizacion::mantenimiento' => [
                'view_any_climatizacion::mantenimiento',
                'view_climatizacion::mantenimiento',
                'create_climatizacion::mantenimiento',
                'update_climatizacion::mantenimiento',
                'delete_climatizacion::mantenimiento',
            ],
            'climatizacion::contrato_mantenimiento' => [
                'view_any_climatizacion::contrato_mantenimiento',
                'view_climatizacion::contrato_mantenimiento',
                'create_climatizacion::contrato_mantenimiento',
                'update_climatizacion::contrato_mantenimiento',
                'delete_climatizacion::contrato_mantenimiento',
            ],
            'climatizacion::ticket_garantia' => [
                'view_any_climatizacion::ticket_garantia',
                'view_climatizacion::ticket_garantia',
                'create_climatizacion::ticket_garantia',
                'update_climatizacion::ticket_garantia',
                'delete_climatizacion::ticket_garantia',
            ],
            'climatizacion::orden_emergencia' => [
                'view_any_climatizacion::orden_emergencia',
                'view_climatizacion::orden_emergencia',
                'create_climatizacion::orden_emergencia',
                'update_climatizacion::orden_emergencia',
                'delete_climatizacion::orden_emergencia',
            ],
            'delivery-dashboard' => [
                'delivery-dashboard.view',
            ],
            'delivery-drivers' => [
                'delivery-drivers.view',
                'delivery-drivers.create',
                'delivery-drivers.edit',
                'delivery-drivers.delete',
            ],
            'delivery-zones' => [
                'delivery-zones.view',
                'delivery-zones.create',
                'delivery-zones.edit',
                'delivery-zones.delete',
            ],
            'delivery-tracking' => [
                'delivery-tracking.view',
                'delivery-tracking.create',
                'delivery-tracking.edit',
                'delivery-tracking.assign',
            ],
            'delivery-earnings' => [
                'delivery-earnings.view',
                'delivery-earnings.export',
            ],
            'sgc-documentos' => [
                'sgc-documentos.view',
                'sgc-documentos.create',
                'sgc-documentos.edit',
                'sgc-documentos.delete',
                'sgc-documentos.download',
            ],
            'sgc-datos' => [
                'sgc-datos.view',
                'sgc-datos.dashboard',
                'sgc-datos.proveedores',
            ],
            'tecnicas' => [
                'tecnicas.view',
                'tecnicas.create',
                'tecnicas.edit',
                'tecnicas.delete',
            ],
            'equipos' => [
                'equipos.view',
                'equipos.create',
                'equipos.edit',
                'equipos.delete',
            ],
            'tecnicos' => [
                'tecnicos.view',
                'tecnicos.create',
                'tecnicos.edit',
                'tecnicos.delete',
            ],
            'domotica' => [
                'domotica.view',
                'domotica.create',
                'domotica.edit',
                'domotica.delete',
            ],
            'garantias' => [
                'garantias.view',
                'garantias.create',
                'garantias.edit',
                'garantias.delete',
            ],
            'marcas-tecnologicas' => [
                'marca-tecnologicas.view',
                'marca-tecnologicas.create',
                'marca-tecnologicas.edit',
                'marca-tecnologicas.delete',
            ],
            'licencias-software' => [
                'licencias-software.view',
                'licencias-software.create',
                'licencias-software.edit',
                'licencias-software.delete',
            ],
            'redes-config' => [
                'redes-config.view',
                'redes-config.create',
                'redes-config.edit',
                'redes-config.delete',
            ],
            'presupuestos' => [
                'presupuestos.view',
                'presupuestos.create',
                'presupuestos.edit',
                'presupuestos.delete',
            ],
            'tecnica-especialidades' => [
                'tecnica-especialidades.view',
                'tecnica-especialidades.create',
                'tecnica-especialidades.edit',
                'tecnica-especialidades.delete',
            ],
            'garantias-config' => [
                'garantias-config.view',
                'garantias-config.create',
                'garantias-config.edit',
                'garantias-config.delete',
            ],
        ];

        $allPermissions = collect($permissionsByModule)
            ->flatten()
            ->values()
            ->all();

        // Inserción masiva de permisos (única query) en vez de firstOrCreate por permiso.
        DB::table(config('permission.table_names.permissions', 'permissions'))
            ->insertOrIgnore(
                collect($allPermissions)->map(fn ($name) => [
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );

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
                
                // System configuration (to change company name, etc.)
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
                'ordenes.view',
                'ordenes.create',
                'ordenes.update',
                'ordenes.pay',
                'ordenes.cancel',
                'clientes.view',
                'cobros.view',
                'cajas.view',
                'cajas.open',
                'almacenes.view',
                'almacenes.movements',
                'lavadero.view',
                'lavadero.insumos',
                'lavadero.insumos.create',
                'lavadero.insumos.edit',
                'lavadero.insumos.delete',
                'lavadero.insumos.consumo',
                'lavadero.insumos.reabaste',
                'lavadero.costo_real',
                'lavadero.utilidades',
                'lavadero.utilidades.create',
                'lavadero.utilidades.edit',
                'lavadero.utilidades.delete',
                'lavadero.productividad',
                'lavadero.productividad.registrar',
                'lavadero.membresias',
                'lavadero.membresias.create',
                'lavadero.membresias.edit',
                'lavadero.membresias.delete',
                'lavadero.membresia-usuarios',
                'lavadero.dashboard-new',
                'tattoo.view',
                'arte.view',
                'arte.obras',
                'arte.artistas',
                'arte.colecciones',
                'arte.exhibiciones',
                'arte.consignaciones',
                'restaurante.view',
                'sucursales.view',
                'ncf.view',
                'ecf.view',
                'payment-processors.view',
                'cuentas-bancarias.view',
                'cuentas-bancarias.create',
                'cuentas-bancarias.edit',

                'usuarios.view',
                'roles.view',

                // Climatización
                'climatizacion.view',
                'climatizacion.create',
                'climatizacion.edit',
                'climatizacion.delete',
                'climatizacion.export',
                'climatizacion.import',
                'view_any_climatizacion::tipo_clima',
                'view_climatizacion::tipo_clima',
                'create_climatizacion::tipo_clima',
                'update_climatizacion::tipo_clima',
                'delete_climatizacion::tipo_clima',
                'view_any_climatizacion::instalacion',
                'view_climatizacion::instalacion',
                'create_climatizacion::instalacion',
                'update_climatizacion::instalacion',
                'delete_climatizacion::instalacion',
                'view_any_climatizacion::mantenimiento',
                'view_climatizacion::mantenimiento',
                'create_climatizacion::mantenimiento',
                'update_climatizacion::mantenimiento',
                'delete_climatizacion::mantenimiento',
                'view_any_climatizacion::contrato_mantenimiento',
                'view_climatizacion::contrato_mantenimiento',
                'create_climatizacion::contrato_mantenimiento',
                'update_climatizacion::contrato_mantenimiento',
                'delete_climatizacion::contrato_mantenimiento',
                'view_any_climatizacion::ticket_garantia',
                'view_climatizacion::ticket_garantia',
                'create_climatizacion::ticket_garantia',
                'update_climatizacion::ticket_garantia',
                'delete_climatizacion::ticket_garantia',
                'view_any_climatizacion::orden_emergencia',
                'view_climatizacion::orden_emergencia',
                'create_climatizacion::orden_emergencia',
                'update_climatizacion::orden_emergencia',
                'delete_climatizacion::orden_emergencia',
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

                'ordenes.view',
                'ordenes.create',
                'ordenes.update',
                'ordenes.pay',
                'ordenes.cancel',

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
                'lavadero.insumos',
                'lavadero.insumos.create',
                'lavadero.insumos.edit',
                'lavadero.insumos.delete',
                'lavadero.insumos.consumo',
                'lavadero.insumos.reabaste',
                'lavadero.costo_real',
                'lavadero.utilidades',
                'lavadero.utilidades.create',
                'lavadero.utilidades.edit',
                'lavadero.utilidades.delete',
                'lavadero.productividad',
                'lavadero.productividad.registrar',
                'lavadero.membresias',
                'lavadero.membresias.create',
                'lavadero.membresias.edit',
                'lavadero.membresias.delete',
                'lavadero.membresia-usuarios',
                'lavadero.dashboard-new',

                'tattoo.view',
                'tattoo.artistas',
                'tattoo.disenos',
                'tattoo.citas',

                'arte.view',
                'arte.obras',
                'arte.artistas',
                'arte.colecciones',
                'arte.exhibiciones',
                'arte.consignaciones',

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
                'productos.toggle',

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

                'cuentas-bancarias.view',
                'cuentas-bancarias.create',
                'cuentas-bancarias.edit',

                'usuarios.view',
                'roles.view',

                'tecnicas.view',
                'tecnicas.create',
                'tecnicas.edit',
                'tecnicas.delete',
                'equipos.view',
                'equipos.create',
                'equipos.edit',
                'equipos.delete',
                'tecnicos.view',
                'tecnicos.create',
                'tecnicos.edit',
                'tecnicos.delete',
                'domotica.view',
                'domotica.create',
                'domotica.edit',
                'domotica.delete',
                'marca-tecnologicas.view',
                'marca-tecnologicas.create',
                'marca-tecnologicas.edit',
                'marca-tecnologicas.delete',
                'licencias-software.view',
                'licencias-software.create',
                'licencias-software.edit',
                'licencias-software.delete',
                'redes-config.view',
                'redes-config.create',
                'redes-config.edit',
                'redes-config.delete',
                'presupuestos.view',
                'presupuestos.create',
                'presupuestos.edit',
                'presupuestos.delete',
                'tecnica-especialidades.view',
                'tecnica-especialidades.create',
                'tecnica-especialidades.edit',
                'tecnica-especialidades.delete',
                'garantias-config.view',
                'garantias-config.create',
                'garantias-config.edit',
                'garantias-config.delete',

                'delivery-companies.view',
                'delivery-companies.create',
                'delivery-companies.edit',
                'delivery-dashboard.view',
                'delivery-drivers.view',
                'delivery-drivers.create',
                'delivery-drivers.edit',
                'delivery-drivers.delete',
                'delivery-zones.view',
                'delivery-zones.create',
                'delivery-zones.edit',
                'delivery-zones.delete',
                'delivery-tracking.view',
                'delivery-tracking.create',
                'delivery-tracking.edit',
                'delivery-tracking.assign',
                'delivery-earnings.view',
                'delivery-earnings.export',
            ],

            'gerente' => [
                'dashboard.view',
                'reportes.view',
                'reportes.export',
                'reportes.restaurante',
                'sgc-datos.view',
                'sgc-datos.dashboard',

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

                'ordenes.view',
                'ordenes.create',
                'ordenes.update',
                'ordenes.pay',
                'ordenes.cancel',

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
                'lavadero.insumos',
                'lavadero.insumos.create',
                'lavadero.insumos.edit',
                'lavadero.insumos.delete',
                'lavadero.insumos.consumo',
                'lavadero.insumos.reabaste',
                'lavadero.costo_real',
                'lavadero.utilidades',
                'lavadero.utilidades.create',
                'lavadero.utilidades.edit',
                'lavadero.utilidades.delete',
                'lavadero.productividad',
                'lavadero.productividad.registrar',
                'lavadero.membresias',
                'lavadero.membresias.create',
                'lavadero.membresias.edit',
                'lavadero.membresias.delete',
                'lavadero.membresia-usuarios',
                'lavadero.dashboard-new',

                'tattoo.view',
                'tattoo.artistas',
                'tattoo.disenos',
                'tattoo.citas',

                'arte.view',
                'arte.obras',
                'arte.artistas',
                'arte.colecciones',
                'arte.exhibiciones',
                'arte.consignaciones',

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
                'productos.toggle',

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

                'cuentas-bancarias.view',
                'cuentas-bancarias.create',
                'cuentas-bancarias.edit',

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

                'ordenes.view',
                'ordenes.create',
                'ordenes.update',
                'ordenes.pay',
                'ordenes.cancel',

                'lavadero.view',
                'lavadero.servicios',
                'lavadero.vehiculos',
                'lavadero.citas',
                'lavadero.lavadores',
                'lavadero.insumos',
                'lavadero.insumos.create',
                'lavadero.insumos.edit',
                'lavadero.insumos.delete',
                'lavadero.insumos.consumo',
                'lavadero.insumos.reabaste',
                'lavadero.costo_real',
                'lavadero.utilidades',
                'lavadero.utilidades.create',
                'lavadero.utilidades.edit',
                'lavadero.utilidades.delete',
                'lavadero.productividad',
                'lavadero.productividad.registrar',
                'lavadero.membresias',
                'lavadero.membresias.create',
                'lavadero.membresias.edit',
                'lavadero.membresias.delete',
                'lavadero.membresia-usuarios',
                'lavadero.dashboard-new',

                'tattoo.view',
                'tattoo.artistas',
                'tattoo.disenos',
                'tattoo.citas',

                'arte.view',
                'arte.obras',
                'arte.artistas',
                'arte.colecciones',
                'arte.exhibiciones',
                'arte.consignaciones',

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
                'productos.toggle',

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
                'productos.toggle',

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

                'cuentas-bancarias.view',

                'restaurante.view',
                'restaurante.cajas',
            ],

            'mesero' => [
                'dashboard.view',
                'reportes.view',

                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.reservaciones',

                'clientes.view',
                'clientes.create',
            ],

            'cocinero' => [
                'dashboard.view',

                'restaurante.view',
            ],

            'bartender' => [
                'dashboard.view',

                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.descuento',
            ],

            'delivery' => [
                'dashboard.view',

                'restaurante.view',
                'restaurante.cobrar',

                'clientes.view',
                'clientes.create',

                'delivery-dashboard.view',
                'delivery-tracking.view',
                'delivery-tracking.assign',
                'delivery-earnings.view',
                'delivery-zones.view',
                'delivery-companies.view',

                'ordenes.view',
                'conduces.view',
                'conduces.deliver',
            ],

            'cajero' => [
                'dashboard.view',
                'reportes.view',

                'restaurante.view',
                'restaurante.cobrar',
                'restaurante.cajas',

                'clientes.view',
                'clientes.create',
                'cobros.view',
                'cobros.create',

                'cajas.view',
                'cajas.open',
                'cajas.close',
                'cajas.cerrar',

                'ventas.view.own',
                'ventas.create',

                'conduces.view',
                'conduces.create',
                'conduces.deliver',
            ],
        ];

        $permIdByName = DB::table(config('permission.table_names.permissions', 'permissions'))
            ->pluck('id', 'name');

        $roleRows = collect(array_keys($rolePermissions))->map(fn ($name) => [
            'name' => $name,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        DB::table(config('permission.table_names.roles', 'roles'))
            ->insertOrIgnore($roleRows);

        $roleIdByName = DB::table(config('permission.table_names.roles', 'roles'))
            ->pluck('id', 'name');

        // Asignación masiva de permisos por rol (única query por rol) en vez de
        // syncPermissions(), que re-consulta cada permiso por nombre y es muy lento.
        foreach ($rolePermissions as $roleName => $perms) {
            $roleId = $roleIdByName[$roleName] ?? null;
            if (!$roleId) {
                continue;
            }

            $pivotRows = collect($perms)
                ->map(fn ($name) => $permIdByName[$name] ?? null)
                ->filter()
                ->map(fn ($permId) => ['permission_id' => $permId, 'role_id' => $roleId])
                ->all();

            DB::table(config('permission.table_names.role_has_permissions', 'role_has_permissions'))
                ->where('role_id', $roleId)
                ->delete();

            DB::table(config('permission.table_names.role_has_permissions', 'role_has_permissions'))
                ->insertOrIgnore($pivotRows);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
