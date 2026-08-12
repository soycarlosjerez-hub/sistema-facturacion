<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'slug' => 'restaurante',
                'nombre' => 'Restaurante / Bar / Café',
                'descripcion' => 'Negocio de comida y bebida con terminal de mesas',
                'color' => 'info',
                'icon' => 'bi-cup-straw',
                'activo' => true,
                'orden' => 1,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                    'clientes', 'cajas', 'sucursales', 'almacenes', 'cuentas-bancarias',
                    'reportes-caja', 'reportes-restaurante', 'reportes-resumen', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors', 'delivery-companies',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'retail',
                'nombre' => 'Colmado / Minimarket / Retail',
                'descripcion' => 'Venta al por menor de productos generales',
                'color' => 'success',
                'icon' => 'bi-cart-plus',
                'activo' => true,
                'orden' => 2,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores', 'kardex',
                    'ventas', 'devoluciones', 'ordenes', 'ordenes-kds',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors', 'delivery-companies',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'mayorista',
                'nombre' => 'Mayorista / Distribuidor',
                'descripcion' => 'Venta por mayor y distribución de productos',
                'color' => 'warning',
                'icon' => 'bi-truck',
                'activo' => true,
                'orden' => 3,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'ventas', 'conduces', 'ordenes', 'ordenes-kds',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'servicios',
                'nombre' => 'Servicios Profesionales',
                'descripcion' => 'Prestación de servicios profesionales y consultoría',
                'color' => 'primary',
                'icon' => 'bi-briefcase',
                'activo' => true,
                'orden' => 4,
                'modulos' => [
                    'dashboard', 'inventario',
                    'cotizaciones', 'gastos',
                    'clientes', 'cobros', 'cajas',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups', 'plantilla-gastos',
                    'alquileres', 'alquileres-viviendas', 'alquileres-inquilinos', 'alquileres-contratos', 'alquileres-pagos',
                ],
            ],
             ['slug' => 'lavadero',
                 'nombre' => 'Lavadero de Carro',
                 'descripcion' => 'Servicio de lavado y detallado de vehículos',
                 'color' => 'primary',
                 'icon' => 'bi-droplet',
                 'activo' => true,
                 'orden' => 5,
                 'modulos' => [
                     'dashboard', 'lavadero', 'lavadero-servicios', 'lavadero-vehiculos', 'lavadero-citas', 'lavadero-lavadores',
                     'clientes', 'cajas', 'gastos',
                     'inventario', 'compras', 'proveedores',
                     'reportes-ventas', 'reportes-caja', 'reportes-stock',
                     'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                     'sucursales', 'almacenes',
                     'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'mixto',
                'nombre' => 'Mixto (Restaurante + Retail)',
                'descripcion' => 'Negocio que combina restaurante y venta al por menor',
                'color' => 'secondary',
                'icon' => 'bi-grid',
                'activo' => true,
                'orden' => 6,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'ventas', 'devoluciones', 'ordenes', 'ordenes-kds',
                    'cotizaciones', 'conduces', 'gastos',
                    'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-restaurante', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors', 'delivery-companies',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'climatizacion',
                'nombre' => 'Climatización / HVAC',
                'descripcion' => 'Servicios de climatización, aire acondicionado y mantenimiento',
                'color' => 'purple',
                'icon' => 'bi-wind',
                'activo' => true,
                'orden' => 8,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores',
                    'clientes', 'cajas', 'gastos',
                    'climatizacion', 'climatizacion-tipos-equipos', 'climatizacion-instalaciones',
                    'climatizacion-contratos', 'climatizacion-mantenimientos',
                    'climatizacion-ordenes-emergencia', 'climatizacion-garantias',
                    'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-caja', 'reportes-stock',
                    'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'tecnologia',
                'nombre' => 'Tienda de Celulares / Reparaciones',
                'descripcion' => 'Venta de celulares, accesorios y servicios técnicos de reparación',
                'color' => 'danger',
                'icon' => 'bi-phone',
                'activo' => true,
                'orden' => 9,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores',
                    'clientes', 'cajas', 'gastos',
                    'equipos', 'tecnicas', 'tecnicos',
                    'domotica', 'garantias',
                    'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-caja', 'reportes-stock',
                    'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups', 'plantilla-gastos',
                ],
            ],
            [
                'slug' => 'mecanica',
                'nombre' => 'Repuesto de Mecanica',
                'descripcion' => 'Venta de repuestos automotrices y servicios de mecánica (cambio de aceite y filtros)',
                'color' => 'warning',
                'icon' => 'bi-tools',
                'activo' => true,
                'orden' => 10,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'ventas', 'devoluciones', 'ordenes', 'cotizaciones', 'conduces',
                    'clientes', 'cobros', 'cajas', 'gastos', 'plantilla-gastos',
                    'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades',
                    'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups',
                ],
            ],
            [
                'slug' => 'arte_escultura',
                'nombre' => 'Arte / Escultura / Galería',
                'descripcion' => 'Galería de arte y escultura — gestión de obras, encargos, consignaciones y exhibiciones',
                'color' => 'purple',
                'icon' => 'bi-palette',
                'activo' => true,
                'orden' => 11,
                'modulos' => [
                    'dashboard', 'arte', 'arte-obras', 'arte-artistas', 'arte-colecciones',
                    'arte-exhibiciones', 'arte-consignaciones',
                    'inventario', 'compras', 'proveedores', 'kardex',
                    'ventas', 'devoluciones', 'ordenes',
                    'clientes', 'cobros', 'cajas', 'gastos', 'plantilla-gastos',
                    'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades',
                    'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors',
                    'auditoria', 'backups',
                ],
            ],
            [
                'slug' => 'embutidos',
                'nombre' => 'Embutidos / Charcutería',
                'descripcion' => 'Venta de embutidos, carnes frías y productos de charcutería',
                'color' => 'danger',
                'icon' => 'bi-egg-fried',
                'activo' => true,
                'orden' => 12,
                'modulos' => [
                    'dashboard', 'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'ventas', 'cotizaciones', 'devoluciones', 'ordenes',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades',
                    'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                    'cuentas-bancarias', 'reportes-gastos', 'plantilla-gastos',
                    'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
                    'libros-ventas', 'libros-compras', 'formulario-14-14',
                    'configuracion-general', 'impresoras', 'payment-processors', 'delivery-companies',
                    'auditoria', 'backups',
                ],
            ],
        ];

        foreach ($tipos as $tipoData) {
            $modulos = $tipoData['modulos'];
            unset($tipoData['modulos']);

            $tipo = BusinessType::updateOrCreate(
                ['slug' => $tipoData['slug']],
                $tipoData
            );

            BusinessTypeModule::where('business_type_id', $tipo->id)->delete();

            foreach ($modulos as $i => $moduloKey) {
                BusinessTypeModule::create([
                    'business_type_id' => $tipo->id,
                    'modulo_key' => $moduloKey,
                    'visible' => true,
                    'orden' => $i,
                ]);
            }
        }

        // Eliminar tipos de negocio que ya no están definidos en el seeder
        // (por ejemplo: tattoo / tatto). Sus instancias pasan al tipo predeterminado.
        $slugsValidos = collect($tipos)->pluck('slug')->all();
        $tipoRestaurante = BusinessType::where('slug', 'restaurante')->first();

        $obsoletos = BusinessType::whereNotIn('slug', $slugsValidos)
            ->where('slug', '!=', 'restaurante')
            ->get();

        if ($obsoletos->isNotEmpty() && $tipoRestaurante) {
            DB::transaction(function () use ($obsoletos, $tipoRestaurante) {
                foreach ($obsoletos as $tipo) {
                    // Reasignar TODAS las instancias (incluidas soft-deleted) al tipo predeterminado,
                    // evitando el scope global de SoftDeletes del modelo.
                    DB::table('business_instances')
                        ->where('business_type_id', $tipo->id)
                        ->update(['business_type_id' => $tipoRestaurante->id]);

                    // Los usuarios con business_type_id apuntan con ON DELETE SET NULL,
                    // pero lo limpiamos explícitamente por seguridad.
                    DB::table('users')
                        ->where('business_type_id', $tipo->id)
                        ->update(['business_type_id' => null]);

                    // Limpiar relaciones polimórficas huérfanas en categorizables.
                    DB::table('categorizables')
                        ->where('categorizable_type', BusinessType::class)
                        ->where('categorizable_id', $tipo->id)
                        ->delete();

                    $tipo->modules()->delete();
                    $tipo->delete();
                }
            });
        }

        BusinessType::flush();
    }
}
