<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Category;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class MecanicaProductosSeeder extends Seeder
{
    public function run(): void
    {
        // Helper para obtener el id de la categoria nueva (tabla polimorfica 'categories')
        // Los productos referencian categorias_id que apunta a 'categories' tras la migracion
        $catAceites   = $this->getCategoryId('Aceites y Lubricantes');
        $catFiltros   = $this->getCategoryId('Filtros');
        $catServicios = $this->getCategoryId('Servicios de Mecanica');
        $catOtros     = $this->getCategoryId('Otros Repuestos');

        if (!$catAceites || !$catFiltros || !$catServicios) {
            $this->command->warn("Categorias de mecanica no encontradas. Correr MecanicaCategoriasSeeder primero.");
            return;
        }

        // ITBIS repuestos en RD = 18% (ley 11-92). Servicios de mecanica tambien gravados al 18%.
        $itbis = 18.00;

        $productos = [
            // === ACEITES (5 viscosidades solicitadas) ===
            [
                'nombre'           => 'Aceite 5W20 1Qt',
                'descripcion'      => 'Aceite de motor sintetico 5W20, botella de 1 cuarto',
                'categoria_id'     => $catAceites,
                'marca'            => 'Generic',
                'modelo'           => '5W20-1QT',
                'precio'           => 650.00,
                'precio_compra'    => 450.00,
                'stock'            => 48,
                'stock_minimo'     => 6,
                'unidad_medida'    => 'Quart',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000017',
            ],
            [
                'nombre'           => 'Aceite 5W30 1Qt',
                'descripcion'      => 'Aceite de motor sintetico 5W30, botella de 1 cuarto',
                'categoria_id'     => $catAceites,
                'marca'            => 'Generic',
                'modelo'           => '5W30-1QT',
                'precio'           => 650.00,
                'precio_compra'    => 450.00,
                'stock'            => 48,
                'stock_minimo'     => 6,
                'unidad_medida'    => 'Quart',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000024',
            ],
            [
                'nombre'           => 'Aceite 10W30 1Qt',
                'descripcion'      => 'Aceite de motor semisintetico 10W30, botella de 1 cuarto',
                'categoria_id'     => $catAceites,
                'marca'            => 'Generic',
                'modelo'           => '10W30-1QT',
                'precio'           => 580.00,
                'precio_compra'    => 400.00,
                'stock'            => 48,
                'stock_minimo'     => 6,
                'unidad_medida'    => 'Quart',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000031',
            ],
            [
                'nombre'           => 'Aceite 10W40 1Qt',
                'descripcion'      => 'Aceite de motor mineral 10W40, botella de 1 cuarto',
                'categoria_id'     => $catAceites,
                'marca'            => 'Generic',
                'modelo'           => '10W40-1QT',
                'precio'           => 580.00,
                'precio_compra'    => 400.00,
                'stock'            => 48,
                'stock_minimo'     => 6,
                'unidad_medida'    => 'Quart',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000048',
            ],
            [
                'nombre'           => 'Aceite 20W50 1Qt',
                'descripcion'      => 'Aceite de motor mineral 20W50 (alta viscosidad), botella de 1 cuarto',
                'categoria_id'     => $catAceites,
                'marca'            => 'Generic',
                'modelo'           => '20W50-1QT',
                'precio'           => 550.00,
                'precio_compra'    => 380.00,
                'stock'            => 48,
                'stock_minimo'     => 6,
                'unidad_medida'    => 'Quart',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000055',
            ],

            // === FILTROS ===
            [
                'nombre'           => 'Filtro de Aceite (Estandar)',
                'descripcion'      => 'Filtro de aceite generico para motor',
                'categoria_id'     => $catFiltros,
                'marca'            => 'Generic',
                'modelo'           => 'FO-STD',
                'precio'           => 350.00,
                'precio_compra'    => 180.00,
                'stock'            => 30,
                'stock_minimo'     => 5,
                'unidad_medida'    => 'Unidad',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000062',
            ],
            [
                'nombre'           => 'Filtro de Aire (Estandar)',
                'descripcion'      => 'Filtro de aire generico para motor',
                'categoria_id'     => $catFiltros,
                'marca'            => 'Generic',
                'modelo'           => 'FA-STD',
                'precio'           => 450.00,
                'precio_compra'    => 250.00,
                'stock'            => 24,
                'stock_minimo'     => 5,
                'unidad_medida'    => 'Unidad',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000079',
            ],
            [
                'nombre'           => 'Filtro de Gasolina (Estandar)',
                'descripcion'      => 'Filtro de gasolina generico para motor',
                'categoria_id'     => $catFiltros,
                'marca'            => 'Generic',
                'modelo'           => 'FG-STD',
                'precio'           => 400.00,
                'precio_compra'    => 220.00,
                'stock'            => 20,
                'stock_minimo'     => 5,
                'unidad_medida'    => 'Unidad',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000086',
            ],

            // === SERVICIOS DE MECANICA (vendibles desde POS, como productos intangibles) ===
            [
                'nombre'           => 'Cambio de Aceite (Mano de Obra)',
                'descripcion'      => 'Servicio de cambio de aceite de motor. No incluye aceite ni filtro.',
                'categoria_id'     => $catServicios,
                'marca'            => null,
                'modelo'           => 'SVC-CAMBIO-ACEITE',
                'precio'           => 300.00,
                'precio_compra'    => 0.00,
                'stock'            => 9999,
                'stock_minimo'     => 0,
                'unidad_medida'    => 'Servicio',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000093',
            ],
            [
                'nombre'           => 'Cambio de Filtro (Mano de Obra)',
                'descripcion'      => 'Servicio de cambio de filtro (aceite, aire o gasolina). No incluye el filtro.',
                'categoria_id'     => $catServicios,
                'marca'            => null,
                'modelo'           => 'SVC-CAMBIO-FILTRO',
                'precio'           => 200.00,
                'precio_compra'    => 0.00,
                'stock'            => 9999,
                'stock_minimo'     => 0,
                'unidad_medida'    => 'Servicio',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000109',
            ],
            // Hace sentido tener un paquete
            [
                'nombre'           => 'Paquete Cambio de Aceite + Filtro (Mano de Obra)',
                'descripcion'      => 'Servicio combinado de cambio de aceite + cambio de filtro. No incluye repuestos.',
                'categoria_id'     => $catServicios,
                'marca'            => null,
                'modelo'           => 'SVC-PACK-ACEITE-FILTRO',
                'precio'           => 450.00,
                'precio_compra'    => 0.00,
                'stock'            => 9999,
                'stock_minimo'     => 0,
                'unidad_medida'    => 'Servicio',
                'itbis_porcentaje' => $itbis,
                'codigo_barras'    => '7501000000116',
            ],
        ];

        $creados = 0;
        foreach ($productos as $p) {
            // Buscar por nombre para evitar duplicados al re-correr el seeder
            $existing = Producto::where('nombre', $p['nombre'])->first();
            if ($existing) {
                $this->command->info("Producto ya existe: {$p['nombre']}");
                continue;
            }

            Producto::create(array_merge($p, [
                'activo' => true,
            ]));
            $creados++;
            $this->command->info("Producto creado: {$p['nombre']}");
        }

        $this->command->info("MecanicaProductosSeeder finalizado. {$creados} productos nuevos.");
    }

    /**
     * Obtener el id de la categoria nueva (tabla polimorfica 'categories') por nombre.
     * Sera null si no existe (el caller decide como manejarlo).
     */
    private function getCategoryId(string $nombre): ?int
    {
        $cat = Category::where('nombre', $nombre)->orderBy('id', 'desc')->first();
        return $cat?->id;
    }
}
