<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modulo;

class ModuloSeeder extends Seeder
{
    public function run(): void
    {
        $modulos = [
            // Core
            ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'categoria' => 'core', 'orden' => 0,
             'sidebar_route' => 'dashboard', 'sidebar_exact_route' => 'dashboard', 'sidebar_permission' => 'dashboard.view'],
            ['key' => 'inventario', 'label' => 'Inventario', 'icon' => 'bi-box-seam', 'categoria' => 'core', 'orden' => 1,
             'sidebar_route' => 'productos.index', 'sidebar_is_route' => 'productos.*', 'sidebar_exact_route' => 'productos.index', 'sidebar_permission' => 'productos.view'],
            ['key' => 'compras', 'label' => 'Compras', 'icon' => 'bi-cart-check', 'categoria' => 'core', 'orden' => 2,
             'sidebar_route' => 'compras.index', 'sidebar_is_route' => 'compras.*', 'sidebar_exact_route' => 'compras.index', 'sidebar_permission' => 'compras.view'],
            ['key' => 'proveedores', 'label' => 'Proveedores', 'icon' => 'bi-truck', 'categoria' => 'core', 'orden' => 3,
             'sidebar_route' => 'proveedores.index', 'sidebar_is_route' => 'proveedores.*', 'sidebar_exact_route' => 'proveedores.index', 'sidebar_permission' => 'proveedores.view'],
            ['key' => 'kardex', 'label' => 'Kardex', 'icon' => 'bi-journal-text', 'categoria' => 'core', 'orden' => 4,
             'sidebar_route' => 'kardex.index', 'sidebar_is_route' => 'kardex.*', 'sidebar_exact_route' => 'kardex.index', 'sidebar_permission' => 'kardex.view'],
            ['key' => 'listas-precio', 'label' => 'Listas de Precios', 'icon' => 'bi-tags', 'categoria' => 'core', 'orden' => 5,
             'sidebar_route' => 'listas-precio.index', 'sidebar_is_route' => 'listas-precio.*', 'sidebar_exact_route' => 'listas-precio.index', 'sidebar_permission' => 'listas-precio.view'],

            // Operaciones
            ['key' => 'ventas', 'label' => 'Terminal de Ventas', 'icon' => 'bi-cart-plus', 'categoria' => 'operaciones', 'orden' => 10,
             'sidebar_route' => 'ventas.create', 'sidebar_is_route' => 'ventas.*', 'sidebar_exact_route' => 'ventas.create', 'sidebar_permission' => 'ventas.create'],
            ['key' => 'cotizaciones', 'label' => 'Cotizaciones', 'icon' => 'bi-file-earmark-text', 'categoria' => 'operaciones', 'orden' => 11,
             'sidebar_route' => 'cotizaciones.index', 'sidebar_is_route' => 'cotizaciones.*', 'sidebar_exact_route' => 'cotizaciones.index', 'sidebar_permission' => 'cotizaciones.view'],
            ['key' => 'conduces', 'label' => 'Conduces', 'icon' => 'bi-truck', 'categoria' => 'operaciones', 'orden' => 12,
             'sidebar_route' => 'conduces.index', 'sidebar_is_route' => 'conduces.*', 'sidebar_exact_route' => 'conduces.index', 'sidebar_permission' => 'conduces.view'],
            ['key' => 'devoluciones', 'label' => 'Devoluciones', 'icon' => 'bi-arrow-return-left', 'categoria' => 'operaciones', 'orden' => 13,
             'sidebar_route' => 'devoluciones.index', 'sidebar_is_route' => 'devoluciones.*', 'sidebar_exact_route' => 'devoluciones.index', 'sidebar_permission' => 'devoluciones.view'],
            ['key' => 'gastos', 'label' => 'Gastos', 'icon' => 'bi-cash-coin', 'categoria' => 'operaciones', 'orden' => 14,
             'sidebar_route' => 'gastos.index', 'sidebar_is_route' => 'gastos.*', 'sidebar_exact_route' => 'gastos.index', 'sidebar_permission' => 'gastos.view'],
            ['key' => 'plantilla-gastos', 'label' => 'Plantillas de Gasto', 'icon' => 'bi-file-earmark-richtext', 'categoria' => 'operaciones', 'orden' => 15,
             'sidebar_route' => 'plantilla-gastos.index', 'sidebar_is_route' => 'plantilla-gastos.*', 'sidebar_exact_route' => 'plantilla-gastos.index', 'sidebar_permission' => 'plantilla-gastos.view'],

            // Clientes
            ['key' => 'clientes', 'label' => 'Clientes', 'icon' => 'bi-people', 'categoria' => 'clientes', 'orden' => 20,
             'sidebar_route' => 'clientes.index', 'sidebar_is_route' => 'clientes.*', 'sidebar_exact_route' => 'clientes.index', 'sidebar_permission' => 'clientes.view'],
            ['key' => 'cobros', 'label' => 'Cuentas por Cobrar', 'icon' => 'bi-wallet2', 'categoria' => 'clientes', 'orden' => 21,
             'sidebar_route' => 'clientes.cuentas', 'sidebar_is_route' => 'clientes.cuentas*', 'sidebar_exact_route' => 'clientes.cuentas', 'sidebar_permission' => 'cobros.view'],
            ['key' => 'cajas', 'label' => 'Cajas y Turnos', 'icon' => 'bi-cash-stack', 'categoria' => 'clientes', 'orden' => 22,
             'sidebar_route' => 'cajas.index', 'sidebar_is_route' => 'cajas.*', 'sidebar_exact_route' => 'cajas.index', 'sidebar_permission' => 'cajas.view'],

            // Organización
            ['key' => 'sucursales', 'label' => 'Sucursales', 'icon' => 'bi-building', 'categoria' => 'organizacion', 'orden' => 30,
             'sidebar_route' => 'sucursales.index', 'sidebar_is_route' => 'sucursales.*', 'sidebar_exact_route' => 'sucursales.index', 'sidebar_permission' => 'sucursales.view'],
            ['key' => 'almacenes', 'label' => 'Almacenes', 'icon' => 'bi-buildings', 'categoria' => 'organizacion', 'orden' => 31,
             'sidebar_route' => 'almacenes.index', 'sidebar_is_route' => 'almacenes.*', 'sidebar_exact_route' => 'almacenes.index', 'sidebar_permission' => 'almacenes.view'],

            // Lavadero
            ['key' => 'lavadero', 'label' => 'Terminal Lavadero', 'icon' => 'bi-droplet', 'categoria' => 'lavadero', 'orden' => 35,
             'sidebar_route' => 'lavadero.index', 'sidebar_is_route' => 'lavadero.*', 'sidebar_exact_route' => 'lavadero.index', 'sidebar_permission' => 'lavadero.view'],
            ['key' => 'lavadero-servicios', 'label' => 'Servicios de Lavado', 'icon' => 'bi-card-checklist', 'categoria' => 'lavadero', 'orden' => 36,
             'sidebar_route' => 'lavadero.servicios.index', 'sidebar_is_route' => 'lavadero.servicios.*', 'sidebar_exact_route' => 'lavadero.servicios.index', 'sidebar_permission' => 'lavadero.servicios'],
            ['key' => 'lavadero-vehiculos', 'label' => 'Vehículos', 'icon' => 'bi-car-front', 'categoria' => 'lavadero', 'orden' => 37,
             'sidebar_route' => 'lavadero.vehiculos.index', 'sidebar_is_route' => 'lavadero.vehiculos.*', 'sidebar_exact_route' => 'lavadero.vehiculos.index', 'sidebar_permission' => 'lavadero.vehiculos'],
            ['key' => 'lavadero-citas', 'label' => 'Citas / Turnos', 'icon' => 'bi-calendar-event', 'categoria' => 'lavadero', 'orden' => 38,
             'sidebar_route' => 'lavadero.citas.index', 'sidebar_is_route' => 'lavadero.citas.*', 'sidebar_exact_route' => 'lavadero.citas.index', 'sidebar_permission' => 'lavadero.citas'],
            ['key' => 'lavadero-lavadores', 'label' => 'Lavadores', 'icon' => 'bi-people', 'categoria' => 'lavadero', 'orden' => 39,
             'sidebar_route' => 'lavadero.lavadores.index', 'sidebar_is_route' => 'lavadero.lavadores.*', 'sidebar_exact_route' => 'lavadero.lavadores.index', 'sidebar_permission' => 'lavadero.lavadores'],

            // Restaurante
            ['key' => 'restaurante', 'label' => 'Restaurante (Terminal Mesas)', 'icon' => 'bi-cup-straw', 'categoria' => 'restaurante', 'orden' => 40,
             'sidebar_route' => 'restaurante.index', 'sidebar_is_route' => 'restaurante.*', 'sidebar_exact_route' => 'restaurante.index', 'sidebar_permission' => 'restaurante.view'],
            ['key' => 'restaurante-kds', 'label' => 'Pantalla Cocina (KDS)', 'icon' => 'bi-egg-fried', 'categoria' => 'restaurante', 'orden' => 41,
             'sidebar_route' => 'restaurante.kds.index', 'sidebar_is_route' => 'restaurante.kds.*', 'sidebar_exact_route' => 'restaurante.kds.index'],
            ['key' => 'restaurante-reservaciones', 'label' => 'Reservaciones', 'icon' => 'bi-calendar-check', 'categoria' => 'restaurante', 'orden' => 42,
             'sidebar_route' => 'restaurante.reservaciones.index', 'sidebar_is_route' => 'restaurante.reservaciones.*', 'sidebar_exact_route' => 'restaurante.reservaciones.index', 'sidebar_permission' => 'restaurante.reservaciones'],
            ['key' => 'restaurante-categorias', 'label' => 'Categorías de Mesa', 'icon' => 'bi-tags', 'categoria' => 'restaurante', 'orden' => 43,
             'sidebar_route' => 'restaurante.categorias.index', 'sidebar_is_route' => 'restaurante.categorias.*', 'sidebar_exact_route' => 'restaurante.categorias.index', 'sidebar_permission' => 'restaurante.categorias'],

            // Reportes
            ['key' => 'reportes-ventas', 'label' => 'Reporte Ventas', 'icon' => 'bi-receipt', 'categoria' => 'reportes', 'orden' => 50,
             'sidebar_route' => 'reportes.ventas', 'sidebar_is_route' => 'reportes.ventas*', 'sidebar_exact_route' => 'reportes.ventas'],
            ['key' => 'reportes-compras', 'label' => 'Reporte Compras', 'icon' => 'bi-cart-check', 'categoria' => 'reportes', 'orden' => 51,
             'sidebar_route' => 'reportes.compras', 'sidebar_is_route' => 'reportes.compras*', 'sidebar_exact_route' => 'reportes.compras'],
            ['key' => 'reportes-stock', 'label' => 'Reporte Inventario', 'icon' => 'bi-box-seam', 'categoria' => 'reportes', 'orden' => 52,
             'sidebar_route' => 'reportes.stock', 'sidebar_is_route' => 'reportes.stock*', 'sidebar_exact_route' => 'reportes.stock'],
            ['key' => 'reportes-utilidades', 'label' => 'Reporte Utilidades', 'icon' => 'bi-graph-up', 'categoria' => 'reportes', 'orden' => 53,
             'sidebar_route' => 'reportes.utilidades', 'sidebar_is_route' => 'reportes.utilidades*', 'sidebar_exact_route' => 'reportes.utilidades'],
            ['key' => 'reportes-gastos', 'label' => 'Reporte Gastos', 'icon' => 'bi-cash-coin', 'categoria' => 'reportes', 'orden' => 54,
             'sidebar_route' => 'reportes.gastos', 'sidebar_is_route' => 'reportes.gastos*', 'sidebar_exact_route' => 'reportes.gastos'],
            ['key' => 'reportes-caja', 'label' => 'Reporte Caja', 'icon' => 'bi-cash-stack', 'categoria' => 'reportes', 'orden' => 54,
             'sidebar_route' => 'reportes.caja', 'sidebar_is_route' => 'reportes.caja*', 'sidebar_exact_route' => 'reportes.caja'],
            ['key' => 'reportes-restaurante', 'label' => 'Reporte Restaurante', 'icon' => 'bi-cup-straw', 'categoria' => 'reportes', 'orden' => 55,
             'sidebar_route' => 'reportes.restaurante', 'sidebar_is_route' => 'reportes.restaurante*', 'sidebar_exact_route' => 'reportes.restaurante'],
            ['key' => 'reportes-retenciones', 'label' => 'Reporte Retenciones', 'icon' => 'bi-percent', 'categoria' => 'reportes', 'orden' => 56,
             'sidebar_route' => 'reportes.retenciones', 'sidebar_is_route' => 'reportes.retenciones*', 'sidebar_exact_route' => 'reportes.retenciones'],
            ['key' => 'reportes-fiscales', 'label' => '606/607 ITBIS', 'icon' => 'bi-file-earmark-text', 'categoria' => 'reportes', 'orden' => 57,
             'sidebar_route' => 'reportes.fiscales', 'sidebar_is_route' => 'reportes.fiscales*', 'sidebar_exact_route' => 'reportes.fiscales'],
            ['key' => 'reportes-resumen', 'label' => 'Resumen Anual', 'icon' => 'bi-bar-chart-line', 'categoria' => 'reportes', 'orden' => 58,
             'sidebar_route' => 'reportes.resumen', 'sidebar_is_route' => 'reportes.resumen*', 'sidebar_exact_route' => 'reportes.resumen'],

            // Configuración
            ['key' => 'configuracion-general', 'label' => 'Ajustes / Parámetros', 'icon' => 'bi-gear', 'categoria' => 'configuracion', 'orden' => 60,
             'sidebar_route' => 'settings.index', 'sidebar_is_route' => 'settings.*', 'sidebar_exact_route' => 'settings.index', 'sidebar_permission' => 'configuracion.view'],

            // Sistema e Integraciones
            ['key' => 'ncf', 'label' => 'Comprobantes (NCF)', 'icon' => 'bi-receipt', 'categoria' => 'sistema', 'orden' => 63,
             'sidebar_route' => 'ncf.index', 'sidebar_is_route' => 'ncf.*', 'sidebar_exact_route' => 'ncf.index', 'sidebar_permission' => 'ncf.view'],
            ['key' => 'ecf', 'label' => 'Facturación Electrónica (e-CF)', 'icon' => 'bi-cloud-arrow-up', 'categoria' => 'sistema', 'orden' => 64,
             'sidebar_route' => 'ecf.index', 'sidebar_is_route' => 'ecf.*', 'sidebar_exact_route' => 'ecf.index', 'sidebar_permission' => 'ecf.view'],
            ['key' => 'secuencias-ecf', 'label' => 'Secuencias e-CF', 'icon' => 'bi-hash', 'categoria' => 'sistema', 'orden' => 64,
             'sidebar_route' => 'secuencias-ecf.index', 'sidebar_is_route' => 'secuencias-ecf.*', 'sidebar_exact_route' => 'secuencias-ecf.index', 'sidebar_permission' => 'ecf.manage'],
            ['key' => 'certificados-digitales', 'label' => 'Certificados Digitales', 'icon' => 'bi-key', 'categoria' => 'sistema', 'orden' => 64,
             'sidebar_route' => 'certificados-digitales.index', 'sidebar_is_route' => 'certificados-digitales.*', 'sidebar_exact_route' => 'certificados-digitales.index', 'sidebar_permission' => 'ecf.certificados'],
            ['key' => 'impresoras', 'label' => 'Impresoras y Plantillas', 'icon' => 'bi-printer', 'categoria' => 'configuracion', 'orden' => 65,
             'sidebar_route' => 'impresoras.index', 'sidebar_is_route' => 'impresoras.*', 'sidebar_exact_route' => 'impresoras.index', 'sidebar_permission' => 'impresoras.view'],
            ['key' => 'payment-processors', 'label' => 'Métodos de Pago', 'icon' => 'bi-credit-card', 'categoria' => 'configuracion', 'orden' => 66,
             'sidebar_route' => 'payment-processors.index', 'sidebar_is_route' => 'payment-processors.*', 'sidebar_exact_route' => 'payment-processors.index', 'sidebar_permission' => 'payment-processors.view'],
            ['key' => 'cuentas-bancarias', 'label' => 'Cuentas Bancarias', 'icon' => 'bi-bank', 'categoria' => 'configuracion', 'orden' => 66,
             'sidebar_route' => 'cuentas-bancarias.index', 'sidebar_is_route' => 'cuentas-bancarias.*', 'sidebar_exact_route' => 'cuentas-bancarias.index', 'sidebar_permission' => 'cuentas-bancarias.view'],
            ['key' => 'delivery-companies', 'label' => 'Delivery / Plataformas', 'icon' => 'bi-truck', 'categoria' => 'configuracion', 'orden' => 66,
             'sidebar_route' => 'delivery-companies.index', 'sidebar_is_route' => 'delivery-companies.*', 'sidebar_exact_route' => 'delivery-companies.index', 'sidebar_permission' => 'delivery-companies.view'],
            ['key' => 'auditoria', 'label' => 'Registro de Auditoría', 'icon' => 'bi-clipboard-check', 'categoria' => 'sistema', 'orden' => 67,
             'sidebar_route' => 'auditoria.index', 'sidebar_is_route' => 'auditoria.*', 'sidebar_exact_route' => 'auditoria.index', 'sidebar_permission' => 'auditoria.view'],
            ['key' => 'backups', 'label' => 'Copias de Seguridad', 'icon' => 'bi-database-down', 'categoria' => 'sistema', 'orden' => 68,
             'sidebar_route' => 'backups.index', 'sidebar_is_route' => 'backups.*', 'sidebar_exact_route' => 'backups.index', 'sidebar_permission' => 'backups.view'],

            // Alquileres
            ['key' => 'alquileres', 'label' => 'Dashboard Alquileres', 'icon' => 'bi-building', 'categoria' => 'alquileres', 'orden' => 70,
             'sidebar_route' => 'alquileres.index', 'sidebar_is_route' => 'alquileres.*', 'sidebar_exact_route' => 'alquileres.index', 'sidebar_permission' => 'alquileres.view'],
            ['key' => 'alquileres-viviendas', 'label' => 'Viviendas', 'icon' => 'bi-house-door', 'categoria' => 'alquileres', 'orden' => 71,
             'sidebar_route' => 'alquileres.viviendas.index', 'sidebar_is_route' => 'alquileres.viviendas.*', 'sidebar_exact_route' => 'alquileres.viviendas.index', 'sidebar_permission' => 'alquileres.viviendas'],
            ['key' => 'alquileres-inquilinos', 'label' => 'Inquilinos', 'icon' => 'bi-people', 'categoria' => 'alquileres', 'orden' => 72,
             'sidebar_route' => 'alquileres.inquilinos.index', 'sidebar_is_route' => 'alquileres.inquilinos.*', 'sidebar_exact_route' => 'alquileres.inquilinos.index', 'sidebar_permission' => 'alquileres.inquilinos'],
            ['key' => 'alquileres-contratos', 'label' => 'Contratos', 'icon' => 'bi-file-earmark-text', 'categoria' => 'alquileres', 'orden' => 73,
             'sidebar_route' => 'alquileres.contratos.index', 'sidebar_is_route' => 'alquileres.contratos.*', 'sidebar_exact_route' => 'alquileres.contratos.index', 'sidebar_permission' => 'alquileres.contratos'],
            ['key' => 'alquileres-pagos', 'label' => 'Pagos de Alquiler', 'icon' => 'bi-cash-coin', 'categoria' => 'alquileres', 'orden' => 74,
             'sidebar_route' => 'alquileres.pagos.index', 'sidebar_is_route' => 'alquileres.pagos.*', 'sidebar_exact_route' => 'alquileres.pagos.index', 'sidebar_permission' => 'alquileres.pagos'],

            // Tattoo Studio
            ['key' => 'tattoo', 'label' => 'Terminal Tattoo', 'icon' => 'bi-brush', 'categoria' => 'tattoo', 'orden' => 80,
             'sidebar_route' => 'tattoo.index', 'sidebar_is_route' => 'tattoo.*', 'sidebar_exact_route' => 'tattoo.index', 'sidebar_permission' => 'tattoo.view'],
            ['key' => 'tattoo-artistas', 'label' => 'Artistas', 'icon' => 'bi-person-badge', 'categoria' => 'tattoo', 'orden' => 81,
             'sidebar_route' => 'tattoo.artistas.index', 'sidebar_is_route' => 'tattoo.artistas.*', 'sidebar_exact_route' => 'tattoo.artistas.index', 'sidebar_permission' => 'tattoo.artistas'],
            ['key' => 'tattoo-disenos', 'label' => 'Catálogo de Diseños', 'icon' => 'bi-images', 'categoria' => 'tattoo', 'orden' => 82,
             'sidebar_route' => 'tattoo.disenos.index', 'sidebar_is_route' => 'tattoo.disenos.*', 'sidebar_exact_route' => 'tattoo.disenos.index', 'sidebar_permission' => 'tattoo.disenos'],
            ['key' => 'tattoo-citas', 'label' => 'Citas / Agenda', 'icon' => 'bi-calendar-event', 'categoria' => 'tattoo', 'orden' => 83,
             'sidebar_route' => 'tattoo.citas.index', 'sidebar_is_route' => 'tattoo.citas.*', 'sidebar_exact_route' => 'tattoo.citas.index', 'sidebar_permission' => 'tattoo.citas'],
        ];

        foreach ($modulos as $modulo) {
            Modulo::updateOrCreate(['key' => $modulo['key']], $modulo);
        }
    }
}
