<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;
use App\Models\BusinessType;

class Sidebar
{
    public static function menu(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $isAdmin = $user->hasRole('admin');
        $can = fn(string $p) => $isAdmin || $user->can($p);

        $tipoNegocio = SystemSetting::tipoNegocio();
        $visibles = BusinessType::getModulosVisibles($tipoNegocio);
        $mod = fn(string $key) => in_array($key, $visibles);

        $items = [];

        $items[] = ['section' => 'Principal'];
        $items[] = [
            'route' => 'dashboard',
            'icon'  => 'bi-speedometer2',
            'label' => 'Dashboard',
            'show'  => $can('dashboard.view'),
            'exact_route' => 'dashboard',
        ];

        // Inventario
        if ($mod('inventario') || $mod('compras') || $mod('proveedores') || $mod('kardex') || $mod('listas-precio')) {
            $items[] = ['section' => 'Inventario'];
            if ($mod('inventario') && $can('productos.view')) {
                $items[] = ['route' => 'productos.index', 'icon' => 'bi-box-seam', 'label' => 'Productos', 'is_route' => 'productos.*', 'exact_route' => 'productos.index'];
                $items[] = ['route' => 'categorias.index', 'icon' => 'bi-tags', 'label' => 'Categorías', 'is_route' => 'categorias.*', 'exact_route' => 'categorias.index'];
            }
            if ($mod('listas-precio') && $can('listas-precio.view')) {
                $items[] = ['route' => 'listas-precio.index', 'icon' => 'bi-tags', 'label' => 'Listas de Precios', 'is_route' => 'listas-precio.*', 'exact_route' => 'listas-precio.index'];
            }
            if ($mod('compras') && $can('compras.view')) {
                $items[] = ['route' => 'compras.index', 'icon' => 'bi-cart-check', 'label' => 'Compras', 'is_route' => 'compras.*', 'exact_route' => 'compras.index'];
            }
            if ($mod('proveedores') && $can('proveedores.view')) {
                $items[] = ['route' => 'proveedores.index', 'icon' => 'bi-truck', 'label' => 'Proveedores', 'is_route' => 'proveedores.*', 'exact_route' => 'proveedores.index'];
            }
            if ($mod('kardex') && $can('kardex.view')) {
                $items[] = ['route' => 'kardex.index', 'icon' => 'bi-journal-text', 'label' => 'Kardex', 'is_route' => 'kardex.*', 'exact_route' => 'kardex.index'];
            }
        }

        // Operaciones
        $tieneOperaciones = $mod('ventas') || $mod('cotizaciones') || $mod('conduces') || $mod('devoluciones') || $mod('gastos');
        if ($tieneOperaciones && ($can('ventas.create') || $can('ventas.view') || $can('ventas.view.own') || $can('cotizaciones.view') || $can('conduces.view') || $can('devoluciones.view') || $can('gastos.view'))) {
            $items[] = ['section' => 'Operaciones'];
            if ($mod('ventas') && ($can('ventas.create') || $can('ventas.view') || $can('ventas.view.own'))) {
                $items[] = ['route' => 'ventas.create', 'icon' => 'bi-cart-plus', 'label' => 'Terminal de Ventas', 'is_route' => 'ventas.*', 'exact_route' => 'ventas.create'];
                if ($can('ventas.view')) {
                    $items[] = ['route' => 'ventas.index', 'icon' => 'bi-receipt', 'label' => 'Historial de Ventas', 'is_route' => 'ventas.*', 'exact_route' => 'ventas.index'];
                }
            }
            if ($mod('cotizaciones') && $can('cotizaciones.view')) {
                $items[] = ['route' => 'cotizaciones.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Cotizaciones', 'is_route' => 'cotizaciones.*', 'exact_route' => 'cotizaciones.index'];
            }
            if ($mod('conduces') && $can('conduces.view')) {
                $items[] = ['route' => 'conduces.index', 'icon' => 'bi-truck', 'label' => 'Conduces', 'is_route' => 'conduces.*', 'exact_route' => 'conduces.index'];
            }
            if ($mod('devoluciones') && $can('devoluciones.view')) {
                $items[] = ['route' => 'devoluciones.index', 'icon' => 'bi-arrow-return-left', 'label' => 'Devoluciones', 'is_route' => 'devoluciones.*', 'exact_route' => 'devoluciones.index'];
            }
            if ($mod('gastos') && $can('gastos.view')) {
                $items[] = ['route' => 'gastos.index', 'icon' => 'bi-cash-coin', 'label' => 'Gastos', 'is_route' => 'gastos.*', 'exact_route' => 'gastos.index'];
            }
        }

        // Clientes y Caja
        if ($mod('clientes') || $mod('cobros') || $mod('cajas')) {
            $items[] = ['section' => 'Clientes y Caja'];
            if ($mod('clientes') && $can('clientes.view')) {
                $items[] = ['route' => 'clientes.index', 'icon' => 'bi-people', 'label' => 'Clientes', 'is_route' => 'clientes.*', 'exact_route' => 'clientes.index'];
            }
            if ($mod('cobros') && $can('cobros.view')) {
                $items[] = ['route' => 'clientes.cuentas', 'icon' => 'bi-wallet2', 'label' => 'Cuentas por Cobrar', 'is_route' => 'clientes.cuentas*', 'exact_route' => 'clientes.cuentas'];
            }
            if ($mod('cajas') && ($can('cajas.view') || $can('cajas.open'))) {
                $items[] = ['route' => 'cajas.index', 'icon' => 'bi-cash-stack', 'label' => 'Cajas y Turnos', 'is_route' => 'cajas.*', 'exact_route' => 'cajas.index'];
            }
        }

        // Organización
        if ($mod('sucursales') && $can('sucursales.view')) {
            $items[] = ['section' => 'Organización'];
            $items[] = ['route' => 'sucursales.index', 'icon' => 'bi-building', 'label' => 'Sucursales', 'is_route' => 'sucursales.*', 'exact_route' => 'sucursales.index'];
        }

        // Almacén
        if ($mod('almacenes') && ($can('almacenes.view') || $can('almacenes.movements'))) {
            $items[] = ['section' => 'Almacén'];
            if ($can('almacenes.view')) {
                $items[] = ['route' => 'almacenes.index', 'icon' => 'bi-buildings', 'label' => 'Almacenes', 'is_route' => 'almacenes.*', 'exact_route' => 'almacenes.index'];
            }
            if ($can('almacenes.movements')) {
                $items[] = ['route' => 'almacenes.movimientos', 'icon' => 'bi-arrow-down-up', 'label' => 'Movimientos', 'is_route' => 'almacenes.movimientos*', 'exact_route' => 'almacenes.movimientos'];
            }
            if ($can('almacenes.view') || $can('almacenes.movements')) {
                $items[] = ['route' => 'almacenes.inventario', 'icon' => 'bi-box-seam', 'label' => 'Inventario por Almacén', 'is_route' => 'almacenes.inventario*', 'exact_route' => 'almacenes.inventario'];
            }
        }

        // Restaurante
        if ($mod('restaurante') && $can('restaurante.view')) {
            $items[] = ['section' => 'Restaurante'];
            $items[] = [
                'route' => 'restaurante.index',
                'icon'  => 'bi-cup-straw',
                'label' => 'Terminal de Mesas',
                'is_route' => 'restaurante.*',
                'exact_route' => 'restaurante.index',
            ];
            if ($can('restaurante.mesas.manage')) {
                $items[] = [
                    'route' => 'restaurante.mesas.index',
                    'icon'  => 'bi-grid-3x3-gap',
                    'label' => 'Gestión de Mesas',
                    'is_route' => 'restaurante.mesas.*',
                    'exact_route' => 'restaurante.mesas.index',
                ];
            }
            if ($mod('restaurante-reservaciones') && $can('restaurante.reservaciones')) {
                $items[] = [
                    'route' => 'restaurante.reservaciones.index',
                    'icon'  => 'bi-calendar-check',
                    'label' => 'Reservaciones',
                    'is_route' => 'restaurante.reservaciones.*',
                    'exact_route' => 'restaurante.reservaciones.index',
                ];
            }
            if ($mod('restaurante-categorias') && $can('restaurante.categorias')) {
                $items[] = [
                    'route' => 'restaurante.categorias.index',
                    'icon'  => 'bi-tags',
                    'label' => 'Categorías de Mesa',
                    'is_route' => 'restaurante.categorias.*',
                    'exact_route' => 'restaurante.categorias.index',
                ];
            }
            if ($mod('restaurante-kds')) {
                $items[] = [
                    'route' => 'restaurante.kds.index',
                    'icon'  => 'bi-egg-fried',
                    'label' => 'Pantalla Cocina (KDS)',
                    'is_route' => 'restaurante.kds.*',
                    'exact_route' => 'restaurante.kds.index',
                ];
            }
        }

        // Reportes
        if ($can('reportes.view')) {
            $items[] = ['section' => 'Reportes'];
            $items[] = [
                'route' => 'reportes.index',
                'icon'  => 'bi-grid',
                'label' => 'Todos los Reportes',
                'is_route' => 'reportes.*',
                'exact_route' => 'reportes.index',
            ];

            if ($mod('reportes-ventas')) {
                $items[] = ['route' => 'reportes.ventas', 'icon' => 'bi-receipt', 'label' => 'Ventas', 'is_route' => 'reportes.ventas*', 'exact_route' => 'reportes.ventas'];
            }
            if ($mod('reportes-compras')) {
                $items[] = ['route' => 'reportes.compras', 'icon' => 'bi-cart-check', 'label' => 'Compras', 'is_route' => 'reportes.compras*', 'exact_route' => 'reportes.compras'];
            }
            if ($mod('reportes-stock')) {
                $items[] = ['route' => 'reportes.stock', 'icon' => 'bi-box-seam', 'label' => 'Inventario', 'is_route' => 'reportes.stock*', 'exact_route' => 'reportes.stock'];
            }
            if ($mod('reportes-utilidades')) {
                $items[] = ['route' => 'reportes.utilidades', 'icon' => 'bi-graph-up', 'label' => 'Utilidades', 'is_route' => 'reportes.utilidades*', 'exact_route' => 'reportes.utilidades'];
            }
            if ($mod('reportes-caja')) {
                $items[] = ['route' => 'reportes.caja', 'icon' => 'bi-cash-stack', 'label' => 'Caja / Turnos', 'is_route' => 'reportes.caja*', 'exact_route' => 'reportes.caja'];
            }
            if ($mod('reportes-restaurante')) {
                $items[] = ['route' => 'reportes.restaurante', 'icon' => 'bi-cup-straw', 'label' => 'Restaurante', 'is_route' => 'reportes.restaurante*', 'exact_route' => 'reportes.restaurante'];
            }
            if ($mod('reportes-retenciones')) {
                $items[] = ['route' => 'reportes.retenciones', 'icon' => 'bi-percent', 'label' => 'Retenciones', 'is_route' => 'reportes.retenciones*', 'exact_route' => 'reportes.retenciones'];
            }
            if ($mod('reportes-fiscales')) {
                $items[] = ['route' => 'reportes.fiscales', 'icon' => 'bi-file-earmark-text', 'label' => '606/607 ITBIS', 'is_route' => 'reportes.fiscales*', 'exact_route' => 'reportes.fiscales'];
            }
            if ($mod('reportes-resumen')) {
                $items[] = ['route' => 'reportes.resumen', 'icon' => 'bi-bar-chart-line', 'label' => 'Resumen Anual', 'is_route' => 'reportes.resumen*', 'exact_route' => 'reportes.resumen'];
            }
        }

        // Sistema
        if ($can('auditoria.view') || $can('backups.view')) {
            $items[] = ['section' => 'Sistema'];
            if ($can('auditoria.view')) {
                $items[] = ['route' => 'audit-logs.index', 'icon' => 'bi-journal-text', 'label' => 'Auditoría', 'is_route' => 'audit-logs.*', 'exact_route' => 'audit-logs.index'];
            }
            if ($can('backups.view')) {
                $items[] = ['route' => 'backups.index', 'icon' => 'bi-cloud-arrow-down', 'label' => 'Backups', 'is_route' => 'backups.*', 'exact_route' => 'backups.index'];
            }
        }

        // Configuración
        if ($can('ncf.view') || $can('ecf.view') || $can('configuracion.view') || $can('usuarios.view') || $can('roles.view') || $can('payment-processors.view')) {
            $items[] = ['section' => 'Configuración'];
            if ($can('ncf.view')) {
                $items[] = ['route' => 'ncf.index', 'icon' => 'bi-receipt-cutoff', 'label' => 'Comprobantes (NCF)', 'is_route' => 'ncf.*', 'exact_route' => 'ncf.index'];
            }
            if ($can('ecf.view')) {
                $items[] = ['route' => 'ecf.index', 'icon' => 'bi-shield-check', 'label' => 'e-CF (DGII)', 'is_route' => 'ecf.*', 'exact_route' => 'ecf.index'];
            }
            if ($can('ecf.manage')) {
                $items[] = ['route' => 'secuencias-ecf.index', 'icon' => 'bi-hash', 'label' => 'Secuencias e-CF', 'is_route' => 'secuencias-ecf.*', 'exact_route' => 'secuencias-ecf.index'];
            }
            if ($can('ecf.certificados')) {
                $items[] = ['route' => 'certificados-digitales.index', 'icon' => 'bi-key', 'label' => 'Certificados Digitales', 'is_route' => 'certificados-digitales.*', 'exact_route' => 'certificados-digitales.index'];
            }
            if ($can('payment-processors.view')) {
                $items[] = ['route' => 'payment-processors.index', 'icon' => 'bi-credit-card', 'label' => 'Procesadores de Pago', 'is_route' => 'payment-processors.*', 'exact_route' => 'payment-processors.index'];
            }
            if ($can('impresoras.view')) {
                $items[] = ['route' => 'impresoras.index', 'icon' => 'bi-printer', 'label' => 'Impresoras', 'is_route' => 'impresoras.*', 'exact_route' => 'impresoras.index'];
            }
            if ($can('configuracion.view')) {
                $items[] = ['route' => 'configuracion.index', 'icon' => 'bi-sliders', 'label' => 'Parámetros', 'is_route' => 'configuracion.*', 'exact_route' => 'configuracion.index'];
                $items[] = ['route' => 'business-types.index', 'icon' => 'bi-diagram-3', 'label' => 'Tipos de Negocio', 'is_route' => 'business-types.*', 'exact_route' => 'business-types.index'];
                $items[] = ['route' => 'modulos.index', 'icon' => 'bi-grid', 'label' => 'Módulos', 'is_route' => 'modulos.*', 'exact_route' => 'modulos.index'];
                $items[] = ['route' => 'configuracion.index', 'url' => route('configuracion.index') . '#correo-smtp', 'icon' => 'bi-envelope-at', 'label' => 'Correo SMTP', 'is_route' => 'configuracion.index', 'exact_route' => 'configuracion.index'];
            }
            if ($can('usuarios.view')) {
                $items[] = ['route' => 'usuarios.index', 'icon' => 'bi-shield-lock', 'label' => 'Usuarios', 'is_route' => 'usuarios.*'];
            }
            if ($can('roles.view')) {
                $items[] = ['route' => 'roles.index', 'icon' => 'bi-shield-shaded', 'label' => 'Roles y Permisos', 'is_route' => 'roles.*'];
            }
        }

        return array_values(array_filter($items, fn($i) => !isset($i['show']) || $i['show'] !== false));
    }
}
