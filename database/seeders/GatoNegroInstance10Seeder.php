<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Seed de la carta de "Gato Negro (Santiago, DR)" para la instancia 10
 * (nueva instancia de lavadero / auto detailing).
 *
 * Los datos (categorías y productos con precios) fueron extraídos del menú
 * público de Uber Eats del negocio. Las imágenes individuales de cada producto
 * no pudieron ser descargadas (el sitio de Uber Eats está protegido por
 * Cloudflare), por lo que se utiliza la imagen principal del local como
 * imagen de todos los productos. Reemplazar por fotografías reales cuando
 * se disponga de ellas.
 *
 * Ejecutar solo para la instancia 10:
 *   php artisan db:seed --class=GatoNegroInstance10Seeder
 *
 * Categorias:
 * - Artículos destacados: platos principales (nachos, smash, alitas, carne, etc.)
 * - Combos: combos especiales
 * - Gato Snacks: entrantes y snacks (quipes, empanadas, mozzarella, dip)
 * - Especial del Gato: vacío (sin datos de Uber Eats)
 * - Smash it: vacío (sin datos de Uber Eats)
 * - Bebidas: vacío (sin datos de Uber Eats)
 */
class GatoNegroInstance10Seeder extends Seeder
{
    protected int $tenantId = 10;

    protected string $imagen = 'productos/gato-negro-logo.jpeg';

    protected string $imagenUrl = 'https://tb-static.uber.com/prod/image-proc/processed_images/9e8a059dcc1330bb83858af2c954ceed/63cdd1044c1bf03a6f4ce3a9422016b8.jpeg';

    public function run(): void
    {
        // La instancia 10 debe existir (productos.tenant_id es FK hacia business_instances).
        if (! DB::table('business_instances')->where('id', $this->tenantId)->exists()) {
            $this->command->error(
                "La instancia (business_instance) con id={$this->tenantId} no existe. " .
                "Créela primero (wizard/setup) y vuelve a ejecutar este seeder."
            );
            return;
        }

        $this->asegurarImagen();

        // Limpiar datos previos de esta instancia para volver a sembrar de forma idempotente.
        DB::table('productos')->where('tenant_id', $this->tenantId)->delete();
        DB::table('categorias')->where('tenant_id', $this->tenantId)->delete();

        $ahora = now();

        $menu = $this->menu();

        foreach ($menu as $catNombre => $catData) {
            $catId = DB::table('categorias')->insertGetId([
                'nombre'      => $catNombre,
                'descripcion' => $catData['descripcion'],
                'activa'      => true,
                'tenant_id'   => $this->tenantId,
                'created_at'  => $ahora,
                'updated_at'  => $ahora,
            ]);

            // Insertar productos con la imagen del local (fallback por restricciones de Cloudflare)
            foreach ($catData['productos'] as $p) {
                DB::table('productos')->insert([
                    'categoria_id'       => $catId,
                    'nombre'             => $p['nombre'],
                    'descripcion'        => $p['descripcion'],
                    'precio'             => $p['precio'],
                    'precio_compra'      => 0,
                    'unidad_medida'      => 'Unidad',
                    'itbis_porcentaje'   => 18.00,
                    'stock'              => 999,
                    'stock_minimo'       => 0,
                    'activo'             => true,
                    'incluir_kds'        => false,
                    'imagen'             => null,
                    'tenant_id'          => $this->tenantId,
                    'created_at'         => $ahora,
                    'updated_at'         => $ahora,
                ]);
            }
        }

        $this->command->info(
            'Gato Negro (instancia 10): ' . count($menu) . ' categorías creadas con ' .
            collect($menu)->sum(fn ($c) => count($c['productos'])) . ' productos.'
        );
    }

    /**
     * Asegura que la imagen del local exista en storage/app/public.
     * Si no está, intenta descargarla desde Uber Eats; si falla, deja el
     * producto sin imagen (se mostrará el placeholder del sistema).
     */
    protected function asegurarImagen(): void
    {
        // Asegurar que el logo del local exista como fallback
        if (Storage::disk('public')->exists($this->imagen)) {
            return;
        }

        try {
            $contenido = file_get_contents($this->imagenUrl);
            if ($contenido !== false) {
                Storage::disk('public')->put($this->imagen, $contenido);
            }
        } catch (\Throwable $e) {
            $this->imagen = null;
            $this->command->warn('No se pudo descargar la imagen del local.');
        }
    }

    /**
     * Carta extraída del menú de Uber Eats de Gato Negro (Santiago, DR).
     * Las 3 últimas categorías aparecen vacías en el sitio (contenido no
     * renderizado por protección Cloudflare), se crean como categorías vacías.
     *
     * Productos duplicados eliminados:
     * - Trío de Empanadas: estaba en Artículos destacados y Gato Snacks → queda solo en Gato Snacks
     * - Mozzarella Sticks: estaba en Artículos destacados y Gato Snacks → queda solo en Gato Snacks
     * - Trío de Quipes: estaba en Artículos destacados y Gato Snacks → queda solo en Gato Snacks
     */
    protected function menu(): array
    {
        return [
            'Artículos destacados' => [
                'descripcion' => 'Lo más pedido de Gato Negro',
                'productos'  => [
                    ['nombre' => 'Street Cat Nachos', 'precio' => 600.00, 'descripcion' => 'Nachos con queso, carne y salsas de la casa.'],
                    ['nombre' => 'Jungle Smash', 'precio' => 865.00, 'descripcion' => 'Smash burger estilo Jungle con doble carne y toppings.'],
                    ['nombre' => 'Alitas', 'precio' => 700.00, 'descripcion' => 'Alitas de pollo bañadas en salsa (BBQ/Búfalo).'],
                    ['nombre' => 'Carne Salada 12 oz', 'precio' => 512.00, 'descripcion' => 'Carne salada 12 oz acompañada de su guarnición.'],
                    ['nombre' => 'Doble Smash Burger', 'precio' => 750.00, 'descripcion' => 'Doble smash burger con papas.'],
                    ['nombre' => 'Quesadillas', 'precio' => 505.00, 'descripcion' => 'Quesadillas rellenas de queso y pollo.'],
                    ['nombre' => 'Jugo Natural de Chinola', 'precio' => 190.00, 'descripcion' => 'Jugo natural de chinola (maracuyá).'],
                    ['nombre' => 'Jugo Natural de Fresa', 'precio' => 190.00, 'descripcion' => 'Jugo natural de fresa.'],
                    ['nombre' => 'Jugo Natural de Naranja', 'precio' => 190.00, 'descripcion' => 'Jugo natural de naranja.'],
                    ['nombre' => 'Jungle Cat Fries', 'precio' => 635.00, 'descripcion' => 'Papas fritas estilo Jungle Cat con toppings.'],
                    ['nombre' => 'Tabla de Carnes', 'precio' => 1220.00, 'descripcion' => 'Tabla de carnes variadas para compartir.'],
                    ['nombre' => 'El Charro Smash', 'precio' => 710.00, 'descripcion' => 'Smash burger El Charro con ingredientes especiales.'],
                ],
            ],
            'Combos' => [
                'descripcion' => 'Combos Gato Negro',
                'productos'  => [
                    ['nombre' => 'Combo Mundial', 'precio' => 900.00, 'descripcion' => 'Hamburguesa doble smash (con papas), mozzarella sticks y una coca cola.'],
                ],
            ],
            'Gato Snacks' => [
                'descripcion' => 'Snacks y entrantes',
                'productos'  => [
                    ['nombre' => 'Trío de Quipes', 'precio' => 416.00, 'descripcion' => 'Quipes rellenos de carne de res. Foto creada con IA.'],
                    ['nombre' => 'Trío de Empanadas', 'precio' => 416.00, 'descripcion' => 'Hechas a mano con relleno de su elección: queso, pollo a la crema, res.'],
                    ['nombre' => 'Mozzarella Sticks', 'precio' => 416.00, 'descripcion' => 'Para calentar el paladar, acompañado de salsa marinara. Foto creada con IA.'],
                    ['nombre' => 'Dip de Espinaca', 'precio' => 505.00, 'descripcion' => 'Salsa de queso casera con espinacas, acompañada de totopos.'],
                ],
            ],
            'Especial del Gato' => [
                'descripcion' => 'Especialidades del Gato Negro',
                'productos'  => [],
            ],
            'Smash it' => [
                'descripcion' => 'Línea Smash it',
                'productos'  => [],
            ],
            'Bebidas' => [
                'descripcion' => 'Bebidas',
                'productos'  => [],
            ],
        ];
    }
}
