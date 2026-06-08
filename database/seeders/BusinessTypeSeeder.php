<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use Illuminate\Database\Seeder;

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
                    'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                    'clientes', 'cajas', 'reportes-caja', 'reportes-restaurante', 'reportes-resumen',
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
                    'inventario', 'compras', 'proveedores', 'kardex',
                    'ventas', 'devoluciones',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
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
                    'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'ventas', 'conduces',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
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
                    'inventario',
                    'cotizaciones', 'gastos',
                    'clientes', 'cobros', 'cajas',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
                ],
            ],
            [
                'slug' => 'mixto',
                'nombre' => 'Mixto (Restaurante + Retail)',
                'descripcion' => 'Negocio que combina restaurante y venta al por menor',
                'color' => 'secondary',
                'icon' => 'bi-grid',
                'activo' => true,
                'orden' => 5,
                'modulos' => [
                    'inventario', 'compras', 'proveedores', 'kardex', 'listas-precio',
                    'ventas', 'devoluciones',
                    'cotizaciones', 'conduces', 'gastos',
                    'restaurante', 'restaurante-kds', 'restaurante-reservaciones', 'restaurante-categorias',
                    'clientes', 'cobros', 'cajas', 'sucursales', 'almacenes',
                    'reportes-ventas', 'reportes-compras', 'reportes-stock', 'reportes-utilidades', 'reportes-caja', 'reportes-restaurante', 'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
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

        BusinessType::flush();
    }
}
