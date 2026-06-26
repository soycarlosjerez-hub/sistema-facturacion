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
        ];

        foreach ($modulos as $modulo) {
            Modulo::updateOrCreate(['key' => $modulo['key']], $modulo);
        }
    }
}
