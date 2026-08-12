<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class EmbutidosProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'salami'  => $this->catId('Salami'),
            'longaniza' => $this->catId('Longaniza'),
            'chorizo' => $this->catId('Chorizo'),
            'jamon'   => $this->catId('Jamón'),
            'mortadela' => $this->catId('Mortadela / Bologna'),
            'tocino'  => $this->catId('Tocino'),
            'quesos'  => $this->catId('Quesos'),
            'otros'   => $this->catId('Otros Embutidos'),
        ];

        if (count(array_filter($categorias)) < 4) {
            $this->command->warn("Categorias de embutidos no encontradas. Correr EmbutidosCategoriaSeeder primero.");
            return;
        }

        $itbis = 18.00;

        $productos = [
            // === SALAMI ===
            [
                'nombre' => 'Salami Popular (500g)',
                'descripcion' => 'Salami popular dominicano, corte de 500 gramos',
                'categoria_id' => $categorias['salami'],
                'precio' => 210.00,
                'precio_compra' => 150.00,
                'stock' => 60,
                'stock_minimo' => 10,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Salami Especial (500g)',
                'descripcion' => 'Salami especial ahumado, corte de 500 gramos',
                'categoria_id' => $categorias['salami'],
                'precio' => 260.00,
                'precio_compra' => 190.00,
                'stock' => 45,
                'stock_minimo' => 8,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Salami Induveca (450g)',
                'descripcion' => 'Salami de la marca Induveca, presentación 450g',
                'categoria_id' => $categorias['salami'],
                'precio' => 185.00,
                'precio_compra' => 135.00,
                'stock' => 70,
                'stock_minimo' => 12,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => $itbis,
            ],
            // === LONGANIZA ===
            [
                'nombre' => 'Longaniza Artesanal (kg)',
                'descripcion' => 'Longaniza artesanal dominicana por kilo',
                'categoria_id' => $categorias['longaniza'],
                'precio' => 320.00,
                'precio_compra' => 240.00,
                'stock' => 30,
                'stock_minimo' => 5,
                'unidad_medida' => 'Kilogramo',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Longaniza Parrillera (lb)',
                'descripcion' => 'Longaniza para parrilla, venta por libra',
                'categoria_id' => $categorias['longaniza'],
                'precio' => 170.00,
                'precio_compra' => 120.00,
                'stock' => 40,
                'stock_minimo' => 8,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            // === CHORIZO ===
            [
                'nombre' => 'Chorizo Español (kg)',
                'descripcion' => 'Chorizo estilo español curado, por kilo',
                'categoria_id' => $categorias['chorizo'],
                'precio' => 450.00,
                'precio_compra' => 340.00,
                'stock' => 25,
                'stock_minimo' => 5,
                'unidad_medida' => 'Kilogramo',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Chorizo Picante (kg)',
                'descripcion' => 'Chorizo picante para cocina y parrilla, por kilo',
                'categoria_id' => $categorias['chorizo'],
                'precio' => 420.00,
                'precio_compra' => 315.00,
                'stock' => 28,
                'stock_minimo' => 5,
                'unidad_medida' => 'Kilogramo',
                'itbis_porcentaje' => $itbis,
            ],
            // === JAMÓN ===
            [
                'nombre' => 'Jamón de Pavo (lb)',
                'descripcion' => 'Jamón de pavo reducido en grasa, venta por libra',
                'categoria_id' => $categorias['jamon'],
                'precio' => 310.00,
                'precio_compra' => 230.00,
                'stock' => 35,
                'stock_minimo' => 6,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Jamón de Cerdo (lb)',
                'descripcion' => 'Jamón de cerdo cocido, venta por libra',
                'categoria_id' => $categorias['jamon'],
                'precio' => 290.00,
                'precio_compra' => 215.00,
                'stock' => 32,
                'stock_minimo' => 6,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            // === MORTADELA / BOLOGNA ===
            [
                'nombre' => 'Mortadela Italiana (lb)',
                'descripcion' => 'Mortadela italiana con pistachos, venta por libra',
                'categoria_id' => $categorias['mortadela'],
                'precio' => 260.00,
                'precio_compra' => 190.00,
                'stock' => 30,
                'stock_minimo' => 6,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Bologna (lb)',
                'descripcion' => 'Bologna clásica para sándwiches, venta por libra',
                'categoria_id' => $categorias['mortadela'],
                'precio' => 180.00,
                'precio_compra' => 125.00,
                'stock' => 50,
                'stock_minimo' => 10,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            // === TOCINO ===
            [
                'nombre' => 'Tocino Ahumado (lb)',
                'descripcion' => 'Tocino ahumado en corte, venta por libra',
                'categoria_id' => $categorias['tocino'],
                'precio' => 340.00,
                'precio_compra' => 255.00,
                'stock' => 24,
                'stock_minimo' => 5,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            // === QUESOS ===
            [
                'nombre' => 'Queso de Freír (400g)',
                'descripcion' => 'Queso de freír dominicano, presentación 400g',
                'categoria_id' => $categorias['quesos'],
                'precio' => 240.00,
                'precio_compra' => 175.00,
                'stock' => 40,
                'stock_minimo' => 8,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => $itbis,
            ],
            [
                'nombre' => 'Queso Blanco (lb)',
                'descripcion' => 'Queso blanco suave para charcutería, venta por libra',
                'categoria_id' => $categorias['quesos'],
                'precio' => 190.00,
                'precio_compra' => 140.00,
                'stock' => 30,
                'stock_minimo' => 6,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => $itbis,
            ],
            // === OTROS ===
            [
                'nombre' => 'Surtido de Embutidos (kg)',
                'descripcion' => 'Surtido variado de embutidos para fiambre, por kilo',
                'categoria_id' => $categorias['otros'],
                'precio' => 380.00,
                'precio_compra' => 280.00,
                'stock' => 20,
                'stock_minimo' => 4,
                'unidad_medida' => 'Kilogramo',
                'itbis_porcentaje' => $itbis,
            ],
        ];

        $creados = 0;
        foreach ($productos as $p) {
            $existente = Producto::where('nombre', $p['nombre'])->whereNull('tenant_id')->first();
            if ($existente) {
                $this->command->info("Producto ya existe: {$p['nombre']}");
                continue;
            }

            Producto::create(array_merge($p, [
                'tenant_id' => null,
                'activo' => true,
            ]));
            $creados++;
            $this->command->info("Producto creado: {$p['nombre']}");
        }

        $this->command->info("EmbutidosProductoSeeder finalizado. {$creados} productos nuevos.");
    }

    private function catId(string $nombre): ?int
    {
        return Categoria::where('nombre', $nombre)->whereNull('tenant_id')->value('id');
    }
}