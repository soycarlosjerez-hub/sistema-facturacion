<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Categoria;
use App\Models\CategoriaSub;
use App\Models\CategorySubcategory;
use App\Models\Producto;
use App\Models\LavaderoServicio;
use App\Models\LavaderoPaquete;
use App\Models\LavaderoPaqueteItem;
use App\Models\VehiculoTipo;
use Illuminate\Database\Seeder;

class LavaderoBusinessSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Busca el BusinessType "lavadero"
        $businessType = BusinessType::where('slug', 'lavadero')
            ->orWhere('key', 'lavadero')
            ->first();

        if (!$businessType) {
            $this->command->error('No se encontró el tipo de negocio "lavadero". Ejecuta primero php artisan db:seed --class=BusinessTypeSeeder');

            return;
        }

        $businessTypeId = $businessType->id;

        $this->command->info('Sembrando datos para el tipo de negocio: Lavadero (ID=' . $businessTypeId . ')');

        // ============================================
        // 2. PAQUETES DE LAVADO
        // ============================================
        $paquetes = [
            [
                'nombre'        => 'Lavado Básico',
                'descripcion'   => 'Exterior + Secado rápido',
                'precio'        => 150,
                'duracion_minutos' => 20,
                'aplicable_a_tipo' => 'auto_pequeno',
                'configuracion' => [
                    'precio_auto_grande' => 200,
                    'precio_suv'         => 200,
                ],
            ],
            [
                'nombre'        => 'Lavado Completo',
                'descripcion'   => 'Exterior + Interior + Secado',
                'precio'        => 300,
                'duracion_minutos' => 35,
                'aplicable_a_tipo' => 'auto_pequeno',
                'configuracion' => [
                    'precio_auto_grande' => 400,
                    'precio_suv'         => 400,
                ],
            ],
            [
                'nombre'        => 'Lavado Premium',
                'descripcion'   => 'Completo + Motor + Cerado',
                'precio'        => 500,
                'duracion_minutos' => 50,
                'aplicable_a_tipo' => 'auto_pequeno',
                'configuracion' => [
                    'precio_auto_grande' => 650,
                    'precio_suv'         => 650,
                ],
            ],
            [
                'nombre'        => 'Lavado Deluxe',
                'descripcion'   => 'Completo + Motor + Cerado + Pulido + Desodorización',
                'precio'        => 800,
                'duracion_minutos' => 70,
                'aplicable_a_tipo' => 'auto_pequeno',
                'configuracion' => [
                    'precio_auto_grande' => 800,
                    'precio_suv'         => 1100,
                    'precio_camion'      => 1100,
                ],
            ],
            [
                'nombre'        => 'Motor Only',
                'descripcion'   => 'Limpieza profunda del motor',
                'precio'        => 200,
                'duracion_minutos' => 25,
                'aplicable_a_tipo' => 'todos',
            ],
            [
                'nombre'        => 'Interior Only',
                'descripcion'   => 'Aspiradora + Tablero + Puertas',
                'precio'        => 250,
                'duracion_minutos' => 30,
                'aplicable_a_tipo' => 'todos',
            ],
        ];

        $paquetesCreados = [];

        foreach ($paquetes as $i => $paqData) {
            $paqData['business_type_id'] = $businessTypeId;
            $paqData['activo'] = true;
            $paqData['orden'] = $i + 1;

            $paquete = LavaderoPaquete::updateOrCreate(
                ['business_type_id' => $businessTypeId, 'nombre' => $paqData['nombre']],
                $paqData
            );

            $paquetesCreadas[] = $paquete;
        }

        // ============================================
        // 3. SERVICIOS ADICIONALES (Accesorios de Lavado)
        // ============================================
        $serviciosAdicionales = [
            ['nombre' => 'Eliminación de manchas', 'descripcion' => 'Limpieza profesional de manchas específicas en tapicería', 'precio' => 150, 'duracion_minutos' => 15, 'categoria' => 'tapiceria'],
            ['nombre' => 'Desodorización avanzada', 'descripcion' => 'Tratamiento con ozono para eliminar olores', 'precio' => 150, 'duracion_minutos' => 20, 'categoria' => 'desodorizacion'],
            ['nombre' => 'Encerado premium', 'descripcion' => 'Cera de protección UV con acabado espejo', 'precio' => 200, 'duracion_minutos' => 15, 'categoria' => 'cerado'],
            ['nombre' => 'Pulido de faros', 'descripcion' => 'Restauración de opacidad en faros delanteros', 'precio' => 150, 'duracion_minutos' => 20, 'categoria' => 'pulido'],
            ['nombre' => 'Lavado de tapicería', 'descripcion' => 'Shampoo profundo con máquina de extracción', 'precio' => 300, 'duracion_minutos' => 45, 'categoria' => 'tapiceria'],
            ['nombre' => 'Lavado de motor profundo', 'descripcion' => 'Desengrasante profesional + protección de caucho', 'precio' => 250, 'duracion_minutos' => 30, 'categoria' => 'motor'],
        ];

        foreach ($serviciosAdicionales as $i => $servData) {
            $servData['activo'] = true;
            $servData['orden'] = $i + 1;

            LavaderoServicio::updateOrCreate(
                ['nombre' => $servData['nombre']],
                $servData
            );
        }

        // ============================================
        // 4. TIPOS DE VEHÍCULO
        // ============================================
        $vehiculosTipos = [
            ['nombre' => 'Automóvil Pequeño', 'slug' => 'auto_pequeno', 'icono' => 'bi-car-front', 'color' => '#0d6efd', 'orden' => 1],
            ['nombre' => 'Automóvil Grande', 'slug' => 'auto_grande', 'icono' => 'bi-car-front', 'color' => '#6610f2', 'orden' => 2],
            ['nombre' => 'SUV Compacta', 'slug' => 'suv_compacta', 'icono' => 'bi-car-front', 'color' => '#6f42c1', 'orden' => 3],
            ['nombre' => 'SUV Grande', 'slug' => 'suv_grande', 'icono' => 'bi-truck', 'color' => '#d63384', 'orden' => 4],
            ['nombre' => 'Camioneta Pickup', 'slug' => 'pickup', 'icono' => 'bi-truck', 'color' => '#fd7e14', 'orden' => 5],
            ['nombre' => 'Van / Minivan', 'slug' => 'van', 'icono' => 'bi-van', 'color' => '#20c997', 'orden' => 6],
            ['nombre' => 'Camión Ligero', 'slug' => 'camion_ligero', 'icono' => 'bi-truck', 'color' => '#198754', 'orden' => 7],
            ['nombre' => 'Moto / Scooter', 'slug' => 'moto', 'icono' => 'bi-bicycle', 'color' => '#ffc107', 'orden' => 8],
        ];

        foreach ($vehiculosTipos as $i => $vtData) {
            $vtData['activo'] = true;
            $vtData['orden'] = $i + 1;

            VehiculoTipo::updateOrCreate(
                ['slug' => $vtData['slug']],
                $vtData
            );
        }

        // ============================================
        // 5. CATEGORÍAS Y SUBCATEGORÍAS TIENDA
        // ============================================

        // 5a) CATEGORÍAS (Category model - polimórfico)
        $categoriasPrincipales = [
            'Alimentos/Bebidas' => [
                'color' => '#e74c3c',
                'icono' => 'bi-cup-hot',
            ],
            'Accesorios Vehiculares' => [
                'color' => '#3498db',
                'icono' => 'bi-tools',
            ],
        ];

        $categoriasMap = [];

        foreach ($categoriasPrincipales as $catNombre => $catConfig) {
            $categoria = Category::firstOrCreate(
                ['nombre' => $catNombre, 'tenant_id' => null],
                [
                    'descripcion' => '',
                    'activa' => true,
                    'color' => $catConfig['color'],
                    'icono' => $catConfig['icono'],
                    'orden' => 1,
                    'configuracion' => [],
                ]
            );

            // Asocia al business_type lavadero
            $categoria->businessTypes()->attach($businessTypeId, ['configuracion' => [], 'soft_delete_enabled' => false]);

            $categoriasMap[$catNombre] = $categoria->id;
        }

        // 5b) SUBCATEGORÍAS (CategorySubcategory model)
        $subcategoriasDefinicion = [
            'Alimentos/Bebidas' => [
                'Snacks' => ['Papas', 'Cacahuates', 'Palomitas', 'Chicharrón'],
                'Comida Rápida' => ['Hot Dog', 'Empanada', 'Pastelito', 'Tostón Relleno'],
                'Postres' => ['Helado', 'Flan', 'Pastel de Chocolate'],
                'Bebidas Calientes' => ['Café Americano', 'Capuchino', 'Latte'],
                'Bebidas Frías' => ['Coca-Cola', 'Sprite', 'Jugo Natural', 'Agua'],
            ],
            'Accesorios Vehiculares' => [
                'Cuidado Exterior' => ['Shampoo', 'Cera', 'Pulimento', 'Protector Caucho'],
                'Cuidado Interior' => ['Desodorizante', 'Protector Tablero', 'Limpia Nylon'],
                'Limpieza' => ['Toalla Microfibra', 'Guante de Lavado', 'Cepillo'],
                'Ambientadores' => ['Colgante', 'Ventilación', 'Wood'],
                'Tecnología' => ['Cargador USB', 'Soporte Celular', 'Cámara Reversa'],
            ],
        ];

        $subcategoriasMap = [];

        foreach ($subcategoriasDefinicion as $catNombre => $subs) {
            if (!isset($categoriasMap[$catNombre])) {
                continue;
            }

            foreach ($subs as $subNombre => $productos) {
                // Crea la subcategoría principal
                $sub = CategorySubcategory::updateOrCreate(
                    ['parent_id' => null, 'category_id' => $categoriasMap[$catNombre], 'business_type_id' => $businessTypeId, 'nombre' => $subNombre],
                    [
                        'orden' => 1,
                        'activa' => true,
                        'configuracion' => [],
                    ]
                );

                $subcategoriasMap[$subNombre] = $sub->id;

                // Crea los hijos (productos/servicios)
                foreach ($productos as $j => $prodNombre) {
                    CategorySubcategory::updateOrCreate(
                        ['parent_id' => $sub->id, 'category_id' => $categoriasMap[$catNombre], 'business_type_id' => $businessTypeId, 'nombre' => $prodNombre],
                        [
                            'orden' => $j + 1,
                            'activa' => true,
                            'configuracion' => [],
                        ]
                    );
                }
            }
        }

        // ============================================
        // 6. PRODUCTOS DE EJEMPLO POR SUBCATEGORÍA
        // ============================================

        $productosDefinicion = [
            'Alimentos/Bebidas' => [
                'Snacks' => [
                    ['nombre' => 'Papas Lays Clásicas', 'precio' => 55, 'stock' => 100, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Cacahuates Salados', 'precio' => 45, 'stock' => 80, 'itbis_porcentaje' => 18, 'unidad_medida' => 'paquete'],
                    ['nombre' => 'Palomitas de Maíz', 'precio' => 60, 'stock' => 50, 'itbis_porcentaje' => 18, 'unidad_medida' => 'bolsa'],
                    ['nombre' => 'Chicharrón Crujiente', 'precio' => 50, 'stock' => 60, 'itbis_porcentaje' => 18, 'unidad_medida' => 'paquete'],
                ],
                'Comida Rápida' => [
                    ['nombre' => 'Hot Dog Clásico', 'precio' => 99, 'stock' => 30, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Empanada de Pollo', 'precio' => 80, 'stock' => 25, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Pastelito de Guayaba', 'precio' => 70, 'stock' => 20, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Tostón Relleno', 'precio' => 120, 'stock' => 20, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                ],
                'Postres' => [
                    ['nombre' => 'Helado Vainilla', 'precio' => 75, 'stock' => 40, 'itbis_porcentaje' => 18, 'unidad_medida' => 'porción'],
                    ['nombre' => 'Flan de Caramelo', 'precio' => 85, 'stock' => 15, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Pastel de Chocolate', 'precio' => 120, 'stock' => 10, 'itbis_porcentaje' => 18, 'unidad_medida' => 'porción'],
                ],
                'Bebidas Calientes' => [
                    ['nombre' => 'Café Americano', 'precio' => 65, 'stock' => 999, 'itbis_porcentaje' => 18, 'unidad_medida' => 'taza'],
                    ['nombre' => 'Capuchino', 'precio' => 95, 'stock' => 999, 'itbis_porcentaje' => 18, 'unidad_medida' => 'taza'],
                    ['nombre' => 'Latte', 'precio' => 100, 'stock' => 999, 'itbis_porcentaje' => 18, 'unidad_medida' => 'taza'],
                ],
                'Bebidas Frías' => [
                    ['nombre' => 'Coca-Cola 355ml', 'precio' => 65, 'stock' => 200, 'itbis_porcentaje' => 18, 'unidad_medida' => 'lata'],
                    ['nombre' => 'Sprite 355ml', 'precio' => 65, 'stock' => 200, 'itbis_porcentaje' => 18, 'unidad_medida' => 'lata'],
                    ['nombre' => 'Jugo Natural Naranja', 'precio' => 85, 'stock' => 50, 'itbis_porcentaje' => 18, 'unidad_medida' => 'vaso'],
                    ['nombre' => 'Agua Mineral 500ml', 'precio' => 40, 'stock' => 300, 'itbis_porcentaje' => 0, 'unidad_medida' => 'botella'],
                ],
            ],
            'Accesorios Vehiculares' => [
                'Cuidado Exterior' => [
                    ['nombre' => 'Shampoo Automotriz 500ml', 'precio' => 250, 'stock' => 30, 'itbis_porcentaje' => 18, 'unidad_medida' => 'botella'],
                    ['nombre' => 'Cera en Pasta Premium', 'precio' => 350, 'stock' => 25, 'itbis_porcentaje' => 18, 'unidad_medida' => 'lata'],
                    ['nombre' => 'Pulimento Líquido 250ml', 'precio' => 280, 'stock' => 20, 'itbis_porcentaje' => 18, 'unidad_medida' => 'botella'],
                    ['nombre' => 'Protector de Caucho', 'precio' => 190, 'stock' => 35, 'itbis_porcentaje' => 18, 'unidad_medida' => 'spray'],
                ],
                'Cuidado Interior' => [
                    ['nombre' => 'Desodorizante Interior', 'precio' => 180, 'stock' => 40, 'itbis_porcentaje' => 18, 'unidad_medida' => 'botella'],
                    ['nombre' => 'Protector de Tablero', 'precio' => 220, 'stock' => 25, 'itbis_porcentaje' => 18, 'unidad_medida' => 'talla'],
                    ['nombre' => 'Limpia Nylon 500ml', 'precio' => 200, 'stock' => 30, 'itbis_porcentaje' => 18, 'unidad_medida' => 'spray'],
                ],
                'Limpieza' => [
                    ['nombre' => 'Toalla Microfibra Grande', 'precio' => 150, 'stock' => 50, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Guante de Lavado Premium', 'precio' => 280, 'stock' => 20, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Cepillo Interior', 'precio' => 120, 'stock' => 40, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                ],
                'Ambientadores' => [
                    ['nombre' => 'Ambientador Colgante Fresh', 'precio' => 95, 'stock' => 60, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Ventilación Tropical', 'precio' => 110, 'stock' => 45, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Ambientador Wood Classic', 'precio' => 130, 'stock' => 35, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                ],
                'Tecnología' => [
                    ['nombre' => 'Cargador USB Auto Dual', 'precio' => 350, 'stock' => 20, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Soporte Celular Dashboard', 'precio' => 280, 'stock' => 25, 'itbis_porcentaje' => 18, 'unidad_medida' => 'unidad'],
                    ['nombre' => 'Cámara Reversa HD', 'precio' => 850, 'stock' => 10, 'itbis_porcentaje' => 18, 'unidad_medida' => 'kit'],
                ],
            ],
        ];

        foreach ($productosDefinicion as $catNombre => $subs) {
            if (!isset($categoriasMap[$catNombre])) {
                continue;
            }

            foreach ($subs as $subNombre => $productos) {
                if (!isset($subcategoriasMap[$subNombre])) {
                    continue;
                }

                foreach ($productos as $prodData) {
                    $prodData['categoria_id'] = $categoriasMap[$catNombre];
                    $prodData['category_subcategory_id'] = $subcategoriasMap[$subNombre];
                    $prodData['activo'] = true;
                    $prodData['stock_minimo'] = 10;

                    Producto::updateOrCreate(
                        ['nombre' => $prodData['nombre'], 'categoria_id' => $categoriasMap[$catNombre]],
                        $prodData
                    );
                }
            }
        }

        // ============================================
        // 7. ITEMS DE PAQUETES (Asociar servicios a cada paquete)
        // ============================================

        $paqueteItemsDefinicion = [
            'Lavado Básico' => [
                ['servicio' => 'Lavado Exterior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 1],
                ['servicio' => 'Secado', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 2],
            ],
            'Lavado Completo' => [
                ['servicio' => 'Lavado Exterior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 1],
                ['servicio' => 'Lavado Interior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 2],
                ['servicio' => 'Secado', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 3],
            ],
            'Lavado Premium' => [
                ['servicio' => 'Lavado Exterior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 1],
                ['servicio' => 'Lavado Interior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 2],
                ['servicio' => 'Secado', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 3],
                ['servicio' => 'Limpieza de Motor', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 4],
                ['servicio' => 'Encerado', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 5],
            ],
            'Lavado Deluxe' => [
                ['servicio' => 'Lavado Exterior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 1],
                ['servicio' => 'Lavado Interior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 2],
                ['servicio' => 'Secado', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 3],
                ['servicio' => 'Limpieza de Motor', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 4],
                ['servicio' => 'Encerado', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 5],
                ['servicio' => 'Pulido', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 6],
                ['servicio' => 'Desodorización', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 7],
            ],
            'Motor Only' => [
                ['servicio' => 'Limpieza de Motor', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 1],
            ],
            'Interior Only' => [
                ['servicio' => 'Lavado Interior', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 1],
                ['servicio' => 'Aspiradora', 'cantidad' => 1, 'precio_individual' => 0, 'orden' => 2],
            ],
        ];

        foreach ($paquetesCreadas as $paquete) {
            if (!isset($paqueteItemsDefinicion[$paquete->nombre])) {
                continue;
            }

            foreach ($paqueteItemsDefinicion[$paquete->nombre] as $i => $itemData) {
                // Buscar el servicio por nombre
                $servicio = LavaderoServicio::where('nombre', $itemData['servicio'])->first();

                if ($servicio) {
                    LavaderoPaqueteItem::updateOrCreate(
                        [
                            'paquete_id' => $paquete->id,
                            'servicio_id' => $servicio->id,
                            'tipo' => 'servicio',
                        ],
                        [
                            'cantidad' => $itemData['cantidad'],
                            'precio_individual' => $itemData['precio_individual'],
                            'incluir_automatico' => true,
                            'orden' => $i + 1,
                        ]
                    );
                }
            }
        }

        $this->command->info('¡Semilla de lavadero completada exitosamente!');
        $this->command->info('- Paquetes: ' . count($paquetesCreadas));
        $this->command->info('- Servicios adicionales: ' . count($serviciosAdicionales));
        $this->command->info('- Tipos de vehículo: ' . count($vehiculosTipos));
    }
}
