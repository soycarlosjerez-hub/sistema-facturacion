<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $basicoModules = [
            'dashboard', 'ventas', 'inventario', 'compras', 'proveedores',
            'clientes', 'cobros', 'cajas', 'gastos',
            'kardex', 'listas-precio',
            'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
            'impresoras', 'payment-processors', 'cuentas-bancarias',
            'configuracion-general', 'auditoria', 'backups',
            'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-gastos',
        ];

        $profesionalModules = array_values(array_unique(array_merge($basicoModules, [
            'sucursales', 'cotizaciones', 'conduces', 'devoluciones',
            'plantilla-gastos',
            'reportes-utilidades', 'reportes-caja', 'reportes-retenciones',
            'reportes-fiscales', 'reportes-resumen',
            'libros-ventas', 'libros-compras', 'libros-retenciones', 'formulario-14-14',
        ])));

        $plans = [
            [
                'nombre' => 'Básico',
                'slug' => 'basico',
                'descripcion' => 'Ideal para pequeños negocios.',
                'precio_mensual' => 2000.00,
                'precio_implementacion' => 5000.00,
                'precio_lanzamiento' => 7500.00,
                'max_usuarios' => 1,
                'max_sucursales' => 0,
                'max_empresas' => 1,
                'modulos' => $basicoModules,
                'features' => [
                    'Facturación + e-CF/DGII',
                    'Clientes y productos',
                    'Inventario',
                    'Compras y gastos',
                    'Cuentas por cobrar',
                    'Reportes básicos',
                    '1 usuario',
                    '1 empresa',
                    'Hosting + backups',
                ],
                'recomendado' => false,
                'orden' => 1,
            ],
            [
                'nombre' => 'Profesional',
                'slug' => 'profesional',
                'descripcion' => 'Ideal para PYMES.',
                'precio_mensual' => 3000.00,
                'precio_implementacion' => 7500.00,
                'precio_lanzamiento' => 7500.00,
                'max_usuarios' => 5,
                'max_sucursales' => 1,
                'max_empresas' => 1,
                'modulos' => $profesionalModules,
                'features' => [
                    'Todo lo del Básico',
                    'Más usuarios',
                    'Reportes avanzados',
                    'Cuentas por pagar',
                    'Inventario avanzado',
                    'Dashboard',
                    'Importación/exportación',
                    'Soporte prioritario',
                ],
                'recomendado' => true,
                'orden' => 2,
            ],
            [
                'nombre' => 'Empresarial',
                'slug' => 'empresarial',
                'descripcion' => 'Para empresas con mayor volumen.',
                'precio_mensual' => 4000.00,
                'precio_implementacion' => 10000.00,
                'precio_lanzamiento' => 7500.00,
                'max_usuarios' => 15,
                'max_sucursales' => 5,
                'max_empresas' => 1,
                'modulos' => [],
                'features' => [
                    'Todo lo del Profesional',
                    'Múltiples usuarios',
                    'Sucursales',
                    'Permisos por usuario',
                    'Reportes financieros avanzados',
                    'Mayor volumen de facturación',
                    'Soporte prioritario',
                ],
                'recomendado' => false,
                'orden' => 3,
            ],
            [
                'nombre' => 'Corporativo',
                'slug' => 'corporativo',
                'descripcion' => 'Solución personalizada para grandes empresas.',
                'precio_mensual' => 6000.00,
                'precio_implementacion' => 15000.00,
                'precio_lanzamiento' => 7500.00,
                'max_usuarios' => null,
                'max_sucursales' => null,
                'max_empresas' => null,
                'modulos' => [],
                'features' => [
                    'Varias sucursales',
                    'Grandes volúmenes de facturación',
                    'Integraciones',
                    'Configuración personalizada',
                    'Usuarios según necesidad',
                    'Soporte personalizado',
                    'Mantenimiento especializado',
                ],
                'recomendado' => false,
                'orden' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        Plan::flush();
    }
}
