<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlansFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `plans` (4 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('plans');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('plans')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Básico', 'slug' => 'basico', 'descripcion' => 'Ideal para pequeños negocios.', 'precio_mensual' => 2000.0, 'precio_implementacion' => 5000.0, 'precio_lanzamiento' => 7500.0, 'max_usuarios' => 1, 'max_sucursales' => 0, 'max_empresas' => 1, 'max_almacenes' => 1, 'max_productos' => 500, 'max_clientes' => 500, 'max_proveedores' => 100, 'max_ventas_mensuales' => 500, 'max_compras_mensuales' => 100, 'max_gastos_mensuales' => 100, 'max_cajas' => 1, 'max_cotizaciones_mensuales' => 50, 'max_conduces_mensuales' => 50, 'max_devoluciones_mensuales' => 20, 'max_ordenes_mensuales' => 200, 'max_mesas' => 10, 'features' => '["Facturación + e-CF/DGII", "Clientes y productos", "Inventario", "Compras y gastos", "Cuentas por cobrar", "Reportes básicos", "1 usuario", "1 empresa", "Hosting + backups"]', 'modulos' => '["dashboard", "ventas", "inventario", "compras", "proveedores", "clientes", "cobros", "cajas", "gastos", "kardex", "listas-precio", "sucursales", "ncf", "ecf", "secuencias-ecf", "certificados-digitales", "impresoras", "payment-processors", "cuentas-bancarias", "configuracion-general", "auditoria", "backups", "reportes-ventas", "reportes-compras", "reportes-stock", "reportes-gastos", "delivery-companies", "delivery-dashboard", "delivery-drivers", "delivery-zones", "delivery-tracking", "delivery-earnings"]', 'activo' => 1, 'recomendado' => 0, 'orden' => 1, 'created_at' => '2026-08-11 14:25:08', 'updated_at' => '2026-08-14 18:06:53'],
            ['id' => 2, 'nombre' => 'Profesional', 'slug' => 'profesional', 'descripcion' => 'Ideal para PYMES.', 'precio_mensual' => 3000.0, 'precio_implementacion' => 7500.0, 'precio_lanzamiento' => 7500.0, 'max_usuarios' => 5, 'max_sucursales' => 1, 'max_empresas' => 1, 'max_almacenes' => 3, 'max_productos' => 2000, 'max_clientes' => 2000, 'max_proveedores' => 500, 'max_ventas_mensuales' => 2000, 'max_compras_mensuales' => 500, 'max_gastos_mensuales' => 500, 'max_cajas' => 3, 'max_cotizaciones_mensuales' => 200, 'max_conduces_mensuales' => 200, 'max_devoluciones_mensuales' => 100, 'max_ordenes_mensuales' => 1000, 'max_mesas' => 30, 'features' => '["Todo lo del Básico", "Más usuarios", "Reportes avanzados", "Cuentas por pagar", "Inventario avanzado", "Dashboard", "Importación/exportación", "Soporte prioritario"]', 'modulos' => '["dashboard", "ventas", "inventario", "compras", "proveedores", "clientes", "cobros", "cajas", "gastos", "kardex", "listas-precio", "sucursales", "ncf", "ecf", "secuencias-ecf", "certificados-digitales", "impresoras", "payment-processors", "cuentas-bancarias", "configuracion-general", "auditoria", "backups", "reportes-ventas", "reportes-compras", "reportes-stock", "reportes-gastos", "delivery-companies", "delivery-dashboard", "delivery-drivers", "delivery-zones", "delivery-tracking", "delivery-earnings", "cotizaciones", "conduces", "devoluciones", "plantilla-gastos", "reportes-utilidades", "reportes-caja", "reportes-retenciones", "reportes-fiscales", "reportes-resumen", "libros-ventas", "libros-compras", "libros-retenciones", "formulario-14-14"]', 'activo' => 1, 'recomendado' => 1, 'orden' => 2, 'created_at' => '2026-08-11 14:25:08', 'updated_at' => '2026-08-14 17:40:29'],
            ['id' => 3, 'nombre' => 'Empresarial', 'slug' => 'empresarial', 'descripcion' => 'Para empresas con mayor volumen.', 'precio_mensual' => 4000.0, 'precio_implementacion' => 10000.0, 'precio_lanzamiento' => 7500.0, 'max_usuarios' => 15, 'max_sucursales' => 5, 'max_empresas' => 1, 'max_almacenes' => 10, 'max_productos' => 10000, 'max_clientes' => 10000, 'max_proveedores' => 2000, 'max_ventas_mensuales' => 10000, 'max_compras_mensuales' => 2000, 'max_gastos_mensuales' => 2000, 'max_cajas' => 10, 'max_cotizaciones_mensuales' => 1000, 'max_conduces_mensuales' => 1000, 'max_devoluciones_mensuales' => 500, 'max_ordenes_mensuales' => 5000, 'max_mesas' => 100, 'features' => '["Todo lo del Profesional", "Múltiples usuarios", "Sucursales", "Permisos por usuario", "Reportes financieros avanzados", "Mayor volumen de facturación", "Soporte prioritario"]', 'modulos' => '[]', 'activo' => 1, 'recomendado' => 0, 'orden' => 3, 'created_at' => '2026-08-11 14:25:08', 'updated_at' => '2026-08-14 13:27:24'],
            ['id' => 4, 'nombre' => 'Corporativo', 'slug' => 'corporativo', 'descripcion' => 'Solución personalizada para grandes empresas.', 'precio_mensual' => 6000.0, 'precio_implementacion' => 15000.0, 'precio_lanzamiento' => 7500.0, 'max_usuarios' => null, 'max_sucursales' => null, 'max_empresas' => null, 'max_almacenes' => null, 'max_productos' => null, 'max_clientes' => null, 'max_proveedores' => null, 'max_ventas_mensuales' => null, 'max_compras_mensuales' => null, 'max_gastos_mensuales' => null, 'max_cajas' => null, 'max_cotizaciones_mensuales' => null, 'max_conduces_mensuales' => null, 'max_devoluciones_mensuales' => null, 'max_ordenes_mensuales' => null, 'max_mesas' => null, 'features' => '["Varias sucursales", "Grandes volúmenes de facturación", "Integraciones", "Configuración personalizada", "Usuarios según necesidad", "Soporte personalizado", "Mantenimiento especializado"]', 'modulos' => '[]', 'activo' => 1, 'recomendado' => 0, 'orden' => 4, 'created_at' => '2026-08-11 14:25:08', 'updated_at' => '2026-08-11 14:25:08'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('plans')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
