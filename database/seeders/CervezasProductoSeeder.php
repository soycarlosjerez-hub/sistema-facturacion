<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class CervezasProductoSeeder extends Seeder
{
    /**
     * Instancia (tenant) destino. Este seeder solo aplica a la instancia 7,
     * que existe en el servidor de producción pero no en el entorno local.
     */
    protected int $tenantId = 7;

    public function run(): void
    {
        $tenantId = $this->tenantId;

        $instancia = BusinessInstance::withTrashed()->find($tenantId);
        if (! $instancia || $instancia->trashed()) {
            $this->command->warn(
                "Instancia {$tenantId} no encontrada (o eliminada). Se omite CervezasProductoSeeder."
            );
            return;
        }

        $this->command->info("Instancia {$tenantId} encontrada: {$instancia->nombre}");

        $categoria = Categoria::where('nombre', 'Cervezas')
            ->where('tenant_id', $tenantId)
            ->first();

        if (! $categoria) {
            $categoria = Categoria::create([
                'nombre' => 'Cervezas',
                'descripcion' => 'Cervezas nacionales e importadas',
                'activa' => true,
                'tenant_id' => $tenantId,
            ]);
            $this->command->info("Categoria 'Cervezas' creada para la instancia {$tenantId} (ID {$categoria->id}).");
        } else {
            $this->command->info("Categoria 'Cervezas' ya existe para la instancia {$tenantId} (ID {$categoria->id}).");
        }

        $itbis = 18.00;

        $productos = [
            // === Nacionales - Cervecería Nacional Dominicana ===
            [
                'nombre' => 'Presidente (330ml)',
                'marca' => 'Presidente',
                'descripcion' => 'Cerveza lager premium dominicana, botella 330ml.',
                'precio' => 160.00,
                'precio_compra' => 118.00,
                'stock' => 50,
            ],
            [
                'nombre' => 'Presidente Light (330ml)',
                'marca' => 'Presidente',
                'descripcion' => 'Cerveza ligera dominicana baja en calorías, botella 330ml.',
                'precio' => 165.00,
                'precio_compra' => 122.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Presidente Black (330ml)',
                'marca' => 'Presidente',
                'descripcion' => 'Cerveza oscura tipo lager negra dominicana, botella 330ml.',
                'precio' => 185.00,
                'precio_compra' => 138.00,
                'stock' => 35,
            ],
            [
                'nombre' => 'Presidente (500ml)',
                'marca' => 'Presidente',
                'descripcion' => 'Cerveza lager premium dominicana, botella grande 500ml.',
                'precio' => 200.00,
                'precio_compra' => 150.00,
                'stock' => 45,
            ],
            [
                'nombre' => 'Bohemia Especial (330ml)',
                'marca' => 'Bohemia',
                'descripcion' => 'Cerveza premium 100% malta, estilo pilsner, botella 330ml.',
                'precio' => 175.00,
                'precio_compra' => 130.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Bohemia Clásica (500ml)',
                'marca' => 'Bohemia',
                'descripcion' => 'Cerveza clásica dominicana, botella grande 500ml.',
                'precio' => 145.00,
                'precio_compra' => 108.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Bohemia Light (330ml)',
                'marca' => 'Bohemia',
                'descripcion' => 'Cerveza ligera rubia, botella 330ml.',
                'precio' => 170.00,
                'precio_compra' => 126.00,
                'stock' => 35,
            ],
            [
                'nombre' => 'Bohemia Dorada (330ml)',
                'marca' => 'Bohemia',
                'descripcion' => 'Cerveza estilo golden lager, botella 330ml.',
                'precio' => 160.00,
                'precio_compra' => 118.00,
                'stock' => 35,
            ],

            // === Nacionales - Cervecería Vegana ===
            [
                'nombre' => 'Dominó (330ml)',
                'marca' => 'Dominó',
                'descripcion' => 'Cerveza rubia económica dominicana, botella 330ml.',
                'precio' => 150.00,
                'precio_compra' => 108.00,
                'stock' => 60,
            ],

            // === Importadas ===
            [
                'nombre' => 'Corona Extra (355ml)',
                'marca' => 'Corona',
                'descripcion' => 'Cerveza mexicana estilo pilsner con limón, botella 355ml.',
                'precio' => 250.00,
                'precio_compra' => 190.00,
                'stock' => 45,
            ],
            [
                'nombre' => 'Heineken (330ml)',
                'marca' => 'Heineken',
                'descripcion' => 'Cerveza holandesa premium, botella 330ml.',
                'precio' => 285.00,
                'precio_compra' => 215.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Stella Artois (330ml)',
                'marca' => 'Stella Artois',
                'descripcion' => 'Cerveza belga premium estilo lager, botella 330ml.',
                'precio' => 300.00,
                'precio_compra' => 230.00,
                'stock' => 30,
            ],
            [
                'nombre' => 'Budweiser (355ml)',
                'marca' => 'Budweiser',
                'descripcion' => 'Cerveza americana clásica, botella 355ml.',
                'precio' => 220.00,
                'precio_compra' => 165.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Bud Light (355ml)',
                'marca' => 'Bud Light',
                'descripcion' => 'Cerveza americana ligera, botella 355ml.',
                'precio' => 220.00,
                'precio_compra' => 165.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Coors Light (330ml)',
                'marca' => 'Coors Light',
                'descripcion' => 'Cerveza americana ligera, botella 330ml.',
                'precio' => 215.00,
                'precio_compra' => 160.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Miller Lite (355ml)',
                'marca' => 'Miller Lite',
                'descripcion' => 'Cerveza americana ligera, botella 355ml.',
                'precio' => 215.00,
                'precio_compra' => 160.00,
                'stock' => 40,
            ],
            [
                'nombre' => 'Modelo Especial (355ml)',
                'marca' => 'Modelo',
                'descripcion' => 'Cerveza mexicana estilo pilsner, botella 355ml.',
                'precio' => 240.00,
                'precio_compra' => 185.00,
                'stock' => 35,
            ],

            // === Artesanales dominicanas ===
            [
                'nombre' => 'Kolonial Coquera (500ml)',
                'marca' => 'Kolonial',
                'descripcion' => 'Cerveza artesanal dominicana lager coco, botella 500ml.',
                'precio' => 260.00,
                'precio_compra' => 195.00,
                'stock' => 25,
            ],
            [
                'nombre' => 'Kolonial IPA (500ml)',
                'marca' => 'Kolonial',
                'descripcion' => 'Cerveza artesanal dominicana India Pale Ale, botella 500ml.',
                'precio' => 310.00,
                'precio_compra' => 235.00,
                'stock' => 20,
            ],
            [
                'nombre' => 'Kolonial Trigo (500ml)',
                'marca' => 'Kolonial',
                'descripcion' => 'Cerveza artesanal dominicana de trigo, botella 500ml.',
                'precio' => 300.00,
                'precio_compra' => 225.00,
                'stock' => 20,
            ],
        ];

        $creados = 0;
        foreach ($productos as $p) {
            $existe = Producto::where('nombre', $p['nombre'])
                ->where('tenant_id', $tenantId)
                ->exists();

            if ($existe) {
                $this->command->info("Producto ya existe: {$p['nombre']}");
                continue;
            }

            Producto::create(array_merge($p, [
                'categoria_id' => $categoria->id,
                'tenant_id' => $tenantId,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => $itbis,
                'stock_minimo' => 6,
                'activo' => true,
            ]));
            $creados++;
            $this->command->info("Producto creado: {$p['nombre']}");
        }

        $this->command->info(
            "CervezasProductoSeeder finalizado. {$creados} productos nuevos para la instancia {$tenantId}."
        );
    }
}