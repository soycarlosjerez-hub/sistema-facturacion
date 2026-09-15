<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessTypeCategoriasSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Borrar TODAS las relaciones de categorizables
        DB::table('categorizables')->truncate();

        // Borrar TODAS las categorías
        DB::table('categorias')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Categorías y relaciones eliminadas. Iniciando recreación por business type...');

        // Definicion de categorias por business type (slug)
        $categoriasPorType = [
            'restaurante' => [
                ['nombre' => 'Entradas', 'descripcion' => 'Aperitivos y entrantes', 'orden' => 1, 'color' => '#f59e0b', 'icono' => 'bi-egg-fried'],
                ['nombre' => 'Platos Fuertes', 'descripcion' => 'Platos principales', 'orden' => 2, 'color' => '#ef4444', 'icono' => 'bi-bookmark-heart-fill'],
                ['nombre' => 'Postres', 'descripcion' => 'Postres y dulces', 'orden' => 3, 'color' => '#ec4899', 'icono' => 'bi-cake2-fill'],
                ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'orden' => 4, 'color' => '#3b82f6', 'icono' => 'bi-cup-straw'],
                ['nombre' => 'Ensaladas', 'descripcion' => 'Ensaladas frescas y ensaladas del dia', 'orden' => 5, 'color' => '#22c55e', 'icono' => 'bi-flower1'],
                ['nombre' => 'Sopas y Caldos', 'descripcion' => 'Sopas, caldos y crepinas', 'orden' => 6, 'color' => '#f97316', 'icono' => 'bi-cup-hot'],
                ['nombre' => 'Mariscos', 'descripcion' => 'Platos a base de mariscos y pescados', 'orden' => 7, 'color' => '#06b6d4', 'icono' => 'bi-tsunami'],
                ['nombre' => 'Parrilla', 'descripcion' => 'Carnes a la parrilla y asados', 'orden' => 8, 'color' => '#dc2626', 'icono' => 'bi-fire'],
                ['nombre' => 'Pastas', 'descripcion' => 'Platos de pasta italiana', 'orden' => 9, 'color' => '#d97706', 'icono' => 'bi-emoji-smile-fill'],
                ['nombre' => 'Snacks', 'descripcion' => 'Snacks, papas fritas y aperitivos', 'orden' => 10, 'color' => '#b45309', 'icono' => 'bi-dice-3-fill'],
                ['nombre' => 'Cocktails', 'descripcion' => 'Cócteles y bebidas alcoholicas', 'orden' => 11, 'color' => '#8b5cf6', 'icono' => 'bi-cup-straw-fill'],
                ['nombre' => 'Café', 'descripcion' => 'Café espresso y sus variedades', 'orden' => 12, 'color' => '#78350f', 'icono' => 'bi-cup-hot-fill'],
            ],
            'retail' => [
                ['nombre' => 'Aseo Personal', 'descripcion' => 'Productos de aseo personal e higiene', 'orden' => 1, 'color' => '#3b82f6', 'icono' => 'bi-shield-check'],
                ['nombre' => 'Alimentos', 'descripcion' => 'Productos alimentarios generales', 'orden' => 2, 'color' => '#22c55e', 'icono' => 'bi-bag-check'],
                ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas embotelladas y refrescos', 'orden' => 3, 'color' => '#06b6d4', 'icono' => 'bi-cup-straw'],
                ['nombre' => 'Limpieza', 'descripcion' => 'Productos de limpieza para el hogar', 'orden' => 4, 'color' => '#8b5cf6', 'icono' => 'bi-droplet-half'],
                ['nombre' => 'Mascotas', 'descripcion' => 'Alimento y accesorios para mascotas', 'orden' => 5, 'color' => '#d97706', 'icono' => 'bi-heart-pulse'],
                ['nombre' => 'Bebé y Maternidad', 'descripcion' => 'Productos para bebés y maternidad', 'orden' => 6, 'color' => '#ec4899', 'icono' => 'bi-heart-fill'],
                ['nombre' => 'Snacks y Dulces', 'descripcion' => 'Papas, dulces y galletas', 'orden' => 7, 'color' => '#f43f5e', 'icono' => 'bi-emoji-smile-fill'],
                ['nombre' => 'Repostería', 'descripcion' => 'Repostería, panaderia y productos frescos', 'orden' => 8, 'color' => '#f59e0b', 'icono' => 'bi-cake2-fill'],
                ['nombre' => 'Electronica', 'descripcion' => 'Electronica y accesorios generales', 'orden' => 9, 'color' => '#6366f1', 'icono' => 'bi-cpu'],
                ['nombre' => 'Juguetes', 'descripcion' => 'Juguetes y juegos para niños', 'orden' => 10, 'color' => '#14b8a6', 'icono' => 'bi-dice-5-fill'],
                ['nombre' => 'Papelería', 'descripcion' => 'Material de papeleria y oficina', 'orden' => 11, 'color' => '#a855f7', 'icono' => 'bi-pen'],
                ['nombre' => 'Hogar', 'descripcion' => 'Artículos para el hogar y decoracion', 'orden' => 12, 'color' => '#78716c', 'icono' => 'bi-house-heart-fill'],
            ],
            'mayorista' => [
                ['nombre' => 'Alimentos y Bebidas', 'descripcion' => 'Productos alimentarios para distribucion', 'orden' => 1, 'color' => '#22c55e', 'icono' => 'bi-bag-check'],
                ['nombre' => 'Limpieza y Aseo', 'descripcion' => 'Productos de limpieza al por mayor', 'orden' => 2, 'color' => '#8b5cf6', 'icono' => 'bi-droplet-half'],
                ['nombre' => 'Electronica', 'descripcion' => 'Electronica y electrodomesticos mayoristas', 'orden' => 3, 'color' => '#6366f1', 'icono' => 'bi-cpu'],
                ['nombre' => 'Herramientas', 'descripcion' => 'Herramientas y equipo industrial', 'orden' => 4, 'color' => '#f97316', 'icono' => 'bi-tools'],
                ['nombre' => 'Empaques', 'descripcion' => 'Materiales de empaque y embalaje', 'orden' => 5, 'color' => '#a855f7', 'icono' => 'bi-box-seam-fill'],
                ['nombre' => 'Textiles', 'descripcion' => 'Ropa y textiles al por mayor', 'orden' => 6, 'color' => '#ec4899', 'icono' => 'bi-bag-fill'],
                ['nombre' => 'Construccion', 'descripcion' => 'Materiales de construccion', 'orden' => 7, 'color' => '#78716c', 'icono' => 'bi-hammer'],
                ['nombre' => 'Papeleria', 'descripcion' => 'Material de papeleria y oficina al por mayor', 'orden' => 8, 'color' => '#06b6d4', 'icono' => 'bi-pen'],
            ],
            'servicios' => [
                ['nombre' => 'Consultoria', 'descripcion' => 'Servicios de consultoria profesional', 'orden' => 1, 'color' => '#6366f1', 'icono' => 'bi-briefcase'],
                ['nombre' => 'Diseño', 'descripcion' => 'Servicios de diseno grafico y web', 'orden' => 2, 'color' => '#ec4899', 'icono' => 'bi-palette'],
                ['nombre' => 'Marketing', 'descripcion' => 'Marketing digital y traditional', 'orden' => 3, 'color' => '#f97316', 'icono' => 'bi-megaphone'],
                ['nombre' => 'Legal', 'descripcion' => 'Servicios legales y contables', 'orden' => 4, 'color' => '#78716c', 'icono' => 'bi-bank2'],
                ['nombre' => 'Tecnologia', 'descripcion' => 'Servicios de TI y soporte tecnico', 'orden' => 5, 'color' => '#3b82f6', 'icono' => 'bi-cpu'],
                ['nombre' => 'Capacitacion', 'descripcion' => 'Servicios de capacitacion y formacion', 'orden' => 6, 'color' => '#22c55e', 'icono' => 'bi-mortarboard-fill'],
                ['nombre' => 'Eventos', 'descripcion' => 'Organizacion y gestion de eventos', 'orden' => 7, 'color' => '#f43f5e', 'icono' => 'bi-calendar-event'],
            ],
            'lavadero' => [
                ['nombre' => 'Lavado Exterior', 'descripcion' => 'Lavado de carro exterior a mano o automatico', 'orden' => 1, 'color' => '#3b82f6', 'icono' => 'bi-droplet'],
                ['nombre' => 'Lavado Interior', 'descripcion' => 'Limpieza completa del interior del vehiculo', 'orden' => 2, 'color' => '#06b6d4', 'icono' => 'bi-house-door'],
                ['nombre' => 'Detallado', 'descripcion' => 'Servicio detallado: pulido, encerado, correccion de pintura', 'orden' => 3, 'color' => '#8b5cf6', 'icono' => 'bi-stars'],
                ['nombre' => 'Tratamiento de Pintura', 'descripcion' => 'Tratamientos ceramicos y selladores', 'orden' => 4, 'color' => '#a855f7', 'icono' => 'bi-shield-fill-check'],
                ['nombre' => 'Tapicería', 'descripcion' => 'Limpieza y restauracion de tapicería', 'orden' => 5, 'color' => '#d97706', 'icono' => 'bi-cupboard'],
                ['nombre' => 'Mecánica Ligera', 'descripcion' => 'Servicios mecanicos basicos', 'orden' => 6, 'color' => '#dc2626', 'icono' => 'bi-tools'],
                ['nombre' => 'Accesorios', 'descripcion' => 'Accesorios para vehiculos', 'orden' => 7, 'color' => '#22c55e', 'icono' => 'bi-wrench-adjustable'],
            ],
            'mixto' => [
                ['nombre' => 'Entradas', 'descripcion' => 'Aperitivos y entrantes', 'orden' => 1, 'color' => '#f59e0b', 'icono' => 'bi-egg-fried'],
                ['nombre' => 'Platos Fuertes', 'descripcion' => 'Platos principales', 'orden' => 2, 'color' => '#ef4444', 'icono' => 'bi-bookmark-heart-fill'],
                ['nombre' => 'Postres', 'descripcion' => 'Postres y dulces', 'orden' => 3, 'color' => '#ec4899', 'icono' => 'bi-cake2-fill'],
                ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'orden' => 4, 'color' => '#3b82f6', 'icono' => 'bi-cup-straw'],
                ['nombre' => 'Snacks', 'descripcion' => 'Snacks, papas, dulces y galletas', 'orden' => 5, 'color' => '#f43f5e', 'icono' => 'bi-dice-3-fill'],
                ['nombre' => 'Alimentos', 'descripcion' => 'Productos alimentarios generales', 'orden' => 6, 'color' => '#22c55e', 'icono' => 'bi-bag-check'],
                ['nombre' => 'Limpieza', 'descripcion' => 'Productos de limpieza para el hogar', 'orden' => 7, 'color' => '#8b5cf6', 'icono' => 'bi-droplet-half'],
                ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas embotelladas y refrescos', 'orden' => 8, 'color' => '#06b6d4', 'icono' => 'bi-cup-straw'],
            ],
            'climatizacion' => [
                ['nombre' => 'Aires Acondicionados', 'descripcion' => 'Equipos de aire acondicionado splits, centrales, ventana', 'orden' => 1, 'color' => '#8b5cf6', 'icono' => 'bi-wind'],
                ['nombre' => 'Calefacción', 'descripcion' => 'Sistemas de calefaccion y calderas', 'orden' => 2, 'color' => '#ef4444', 'icono' => 'bi-fire'],
                ['nombre' => 'Ventilacion', 'descripcion' => 'Sistemas de ventilacion y extractores', 'orden' => 3, 'color' => '#06b6d4', 'icono' => 'bi-fan'],
                ['nombre' => 'Partes y Piezas', 'descripcion' => 'Repuestos y componentes HVAC', 'orden' => 4, 'color' => '#78716c', 'icono' => 'bi-gear-fill'],
                ['nombre' => 'Control Termico', 'descripcion' => 'Termostatos, sensores y controladores', 'orden' => 5, 'color' => '#3b82f6', 'icono' => 'bi-thermometer-half'],
                ['nombre' => 'Servicios de Instalacion', 'descripcion' => 'Servicios profesionales de instalacion', 'orden' => 6, 'color' => '#22c55e', 'icono' => 'bi-tools'],
                ['nombre' => 'Mantenimiento', 'descripcion' => 'Servicios de mantenimiento preventivo y correctivo', 'orden' => 7, 'color' => '#d97706', 'icono' => 'bi-wrench-adjustable'],
            ],
            'tecnologia' => [
                ['nombre' => 'Cables', 'descripcion' => 'Cables de red, HDMI, USB y accesorios de cableado', 'orden' => 1, 'color' => '#6b7280', 'icono' => 'bi-cable'],
                ['nombre' => 'Computadoras', 'descripcion' => 'Laptops, desktops, componentes y accesorios para PC', 'orden' => 2, 'color' => '#3b82f6', 'icono' => 'bi-laptop'],
                ['nombre' => 'Monitores', 'descripcion' => 'Monitores gaming, profesionales y portatiles', 'orden' => 3, 'color' => '#06b6d4', 'icono' => 'bi-display'],
                ['nombre' => 'Impresoras', 'descripcion' => 'Impresoras laser, inkjet, multifuncionales y toners', 'orden' => 4, 'color' => '#78716c', 'icono' => 'bi-printer'],
                ['nombre' => 'Redes', 'descripcion' => 'Routers, switches, access points y cables de red', 'orden' => 5, 'color' => '#22c55e', 'icono' => 'bi-diagram-3'],
                ['nombre' => 'Seguridad', 'descripcion' => 'Camara de vigilancia, DVRs, NVRs y sensores', 'orden' => 6, 'color' => '#dc2626', 'icono' => 'bi-shield-fill'],
                ['nombre' => 'Almacenamiento', 'descripcion' => 'Discos duros SSD/HDD, memorias USB, tarjetas de memoria', 'orden' => 7, 'color' => '#d97706', 'icono' => 'bi-hdd-fill'],
                ['nombre' => 'Audio', 'descripcion' => 'Bocinas, audifonos, microfonos y accesorios de audio', 'orden' => 8, 'color' => '#a855f7', 'icono' => 'bi-soundwave'],
                ['nombre' => 'Perifericos', 'descripcion' => 'Teclados, mice, webcams, lectores y adaptadores', 'orden' => 9, 'color' => '#ec4899', 'icono' => 'bi-keyboard'],
                ['nombre' => 'Componentes', 'descripcion' => 'Placas madre, CPUs, memorias RAM, GPUs, power supplies y cases', 'orden' => 10, 'color' => '#6366f1', 'icono' => 'bi-cpu'],
                ['nombre' => 'Carga y Energia', 'descripcion' => 'Cargadores, UPS, baterias, adaptadores y extensiones', 'orden' => 11, 'color' => '#f59e0b', 'icono' => 'bi-battery-charging'],
                ['nombre' => 'Tablets y Moviles', 'descripcion' => 'Tablets, smartphones y accesorios mobile', 'orden' => 12, 'color' => '#0ea5e9', 'icono' => 'bi-phone'],
                ['nombre' => 'Corte y Diseño', 'descripcion' => 'Maquinas de corte Cricut, herramientas y materiales', 'orden' => 13, 'color' => '#f43f5e', 'icono' => 'bi-scissors'],
                ['nombre' => 'Mobiliario', 'descripcion' => 'Mesas, sillas, soportes y organizadores', 'orden' => 14, 'color' => '#78716c', 'icono' => 'bi-chair'],
                ['nombre' => 'Streaming', 'descripcion' => 'Dispositivos de streaming como Fire TV Stick y Roku', 'orden' => 15, 'color' => '#8b5cf6', 'icono' => 'bi-play-circle'],
                ['nombre' => 'Climatizacion', 'descripcion' => 'Aires acondicionados y accesorios de climatizacion', 'orden' => 16, 'color' => '#06b6d4', 'icono' => 'bi-wind'],
                ['nombre' => 'Registros', 'descripcion' => 'Cajas registradoras, cintas y suministros de registro', 'orden' => 17, 'color' => '#d97706', 'icono' => 'bi-cash-stack'],
                ['nombre' => 'Herramientas', 'descripcion' => 'Herramientas de diagnostico, limpieza y mantenimiento', 'orden' => 18, 'color' => '#22c55e', 'icono' => 'bi-tools'],
                ['nombre' => 'Licencias', 'descripcion' => 'Licencias de software y servicios digitales', 'orden' => 19, 'color' => '#a855f7', 'icono' => 'bi-shield-check'],
            ],
            'mecanica' => [
                ['nombre' => 'Aceites y Lubricantes', 'descripcion' => 'Aceites de motor por viscosidad y lubricantes automotrices', 'orden' => 1, 'color' => '#f59e0b', 'icono' => 'bi-droplet-half'],
                ['nombre' => 'Filtros', 'descripcion' => 'Filtros de aceite, aire y gasolina', 'orden' => 2, 'color' => '#6b7280', 'icono' => 'bi-funnel'],
                ['nombre' => 'Servicios de Mecanica', 'descripcion' => 'Servicios de mantenimiento: cambio de aceite y cambio de filtro', 'orden' => 3, 'color' => '#3b82f6', 'icono' => 'bi-tools'],
                ['nombre' => 'Otros Repuestos', 'descripcion' => 'Otros repuestos automotrices generales', 'orden' => 4, 'color' => '#22c55e', 'icono' => 'bi-grid'],
                ['nombre' => 'Baterias', 'descripcion' => 'Baterias automotrices y accesorios electricos', 'orden' => 5, 'color' => '#dc2626', 'icono' => 'bi-battery-charging'],
                ['nombre' => 'Neumaticos', 'descripcion' => 'Neumaticos, camaras y llantas', 'orden' => 6, 'color' => '#78716c', 'icono' => 'bi-disc-fill'],
                ['nombre' => 'Frenos', 'descripcion' => 'Pastillas, discos y sistemas de frenos', 'orden' => 7, 'color' => '#ef4444', 'icono' => 'bi-pause-circle'],
                ['nombre' => 'Suspension', 'descripcion' => 'Amortiguadores, resortes y alineacion', 'orden' => 8, 'color' => '#d97706', 'icono' => 'bi-arrow-down-circle'],
                ['nombre' => 'Sistema Electrico', 'descripcion' => 'Alternadores, starteres y sensores', 'orden' => 9, 'color' => '#6366f1', 'icono' => 'bi-lightning-charge-fill'],
                ['nombre' => 'Escape', 'descripcion' => 'Tubos de escape, silenciadores y catalizadores', 'orden' => 10, 'color' => '#a855f7', 'icono' => 'bi-cloud-fog'],
            ],
            'arte_escultura' => [
                ['nombre' => 'Pintura', 'descripcion' => 'Cuadros al oleo, acrilico, tecnica mixta y digital', 'orden' => 1, 'color' => '#a855f7', 'icono' => 'bi-palette'],
                ['nombre' => 'Escultura', 'descripcion' => 'Esculturas en piedra, madera, metal y resina', 'orden' => 2, 'color' => '#78716c', 'icono' => 'bi-brush'],
                ['nombre' => 'Fotografia', 'descripcion' => 'Fotografia artistica en marco y sin marco', 'orden' => 3, 'color' => '#6366f1', 'icono' => 'bi-image'],
                ['nombre' => 'Grabados', 'descripcion' => 'Grabados, litografias y serigrafias', 'orden' => 4, 'color' => '#06b6d4', 'icono' => 'bi-file-earmark-image'],
                ['nombre' => 'Ceramica', 'descripcion' => 'Obra de ceramica y porcelana artistica', 'orden' => 5, 'color' => '#d97706', 'icono' => 'bi-cup-and-saucer'],
                ['nombre' => 'Textiles', 'descripcion' => 'Obra textil, bordados y tapezaria', 'orden' => 6, 'color' => '#ec4899', 'icono' => 'bi-layers'],
                ['nombre' => 'Vidrio Soplado', 'descripcion' => 'Obra en vidrio soplado y artesanal', 'orden' => 7, 'color' => '#3b82f6', 'icono' => 'bi-droplet'],
                ['nombre' => 'Encargos', 'descripcion' => 'Obras por encargo personalizado', 'orden' => 8, 'color' => '#22c55e', 'icono' => 'bi-pencil-fill'],
                ['nombre' => 'Coleccionables', 'descripcion' => 'Ediciones limitadas, posters y reproducciones', 'orden' => 9, 'color' => '#f43f5e', 'icono' => 'bi-gift'],
            ],
            'embutidos' => [
                ['nombre' => 'Salami', 'descripcion' => 'Salami dominicano y variedades', 'orden' => 1, 'color' => '#dc2626', 'icono' => 'bi-egg-fried'],
                ['nombre' => 'Longaniza', 'descripcion' => 'Longanizas artesanales y parrilleras', 'orden' => 2, 'color' => '#ef4444', 'icono' => 'bi-flower1'],
                ['nombre' => 'Chorizo', 'descripcion' => 'Chorizos dulces, picantes y espanoles', 'orden' => 3, 'color' => '#f97316', 'icono' => 'bi-fire'],
                ['nombre' => 'Jamón', 'descripcion' => 'Jamones de cerdo, pavo y especialidades', 'orden' => 4, 'color' => '#b45309', 'icono' => 'bi-cup-hot'],
                ['nombre' => 'Mortadela / Bologna', 'descripcion' => 'Mortadelas y bolognas', 'orden' => 5, 'color' => '#d97706', 'icono' => 'bi-box-seam'],
                ['nombre' => 'Tocino', 'descripcion' => 'Tocino y panceta ahumados', 'orden' => 6, 'color' => '#b91c1c', 'icono' => 'bi-droplet-half'],
                ['nombre' => 'Quesos', 'descripcion' => 'Quesos para charcuteria: de freir, blanco y especiales', 'orden' => 7, 'color' => '#f59e0b', 'icono' => 'bi-cake2'],
                ['nombre' => 'Otros Embutidos', 'descripcion' => 'Butifarras, surtidos y mas', 'orden' => 8, 'color' => '#78716c', 'icono' => 'bi-grid'],
                ['nombre' => 'Ahumados', 'descripcion' => 'Productos ahumados artesanales', 'orden' => 9, 'color' => '#a855f7', 'icono' => 'bi-cloud-fog'],
            ],
        ];

        // Obtener todos los business types
        $businessTypes = BusinessType::where('activo', true)->get();

        $totalCreadas = 0;
        $totalVinculadas = 0;

        foreach ($businessTypes as $businessType) {
            // Buscar definicion de categorias para este tipo
            $categorias = $categoriasPorType[$businessType->slug] ?? null;

            if (! $categorias) {
                $this->command->info("Sin categorias predefinidas para '{$businessType->slug}'. Creando categoria genérica.");
                // Crear categoria genérica por defecto
                $cat = Category::create([
                    'nombre' => 'General',
                    'descripcion' => "Categoria general para {$businessType->nombre}",
                    'activa' => true,
                    'orden' => 1,
                    'color' => $businessType->color ?? '#6366f1',
                    'icono' => $businessType->icon ?? 'bi-grid',
                    'tenant_id' => null,
                ]);

                // Vincular al business type
                DB::table('categorizables')->insert([
                    'category_id' => $cat->id,
                    'categorizable_type' => BusinessType::class,
                    'categorizable_id' => $businessType->id,
                    'configuracion' => null,
                    'soft_delete_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalCreadas++;
                $totalVinculadas++;
                $this->command->info("  -> '{$businessType->slug}': 1 categoria genérica creada y vinculada.");

                continue;
            }

            foreach ($categorias as $catData) {
                // Verificar si ya existe para este business type
                $exists = DB::table('categorizables')
                    ->join('categorias', 'categorizables.category_id', '=', 'categorias.id')
                    ->where('categories.nombre', $catData['nombre'])
                    ->where('categorizables.categorizable_type', BusinessType::class)
                    ->where('categorizables.categorizable_id', $businessType->id)
                    ->exists();

                if ($exists) {
                    $this->command->info("  -> '{$businessType->slug}': {$catData['nombre']} ya existe.");

                    continue;
                }

                // Crear la categoria
                $cat = Category::create([
                    'nombre' => $catData['nombre'],
                    'descripcion' => $catData['descripcion'] ?? '',
                    'activa' => true,
                    'orden' => $catData['orden'] ?? 0,
                    'color' => $catData['color'] ?? null,
                    'icono' => $catData['icono'] ?? null,
                    'configuracion' => null,
                    'tenant_id' => null,
                ]);

                // Vincular al business type
                DB::table('categorizables')->insert([
                    'category_id' => $cat->id,
                    'categorizable_type' => BusinessType::class,
                    'categorizable_id' => $businessType->id,
                    'configuracion' => null,
                    'soft_delete_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalCreadas++;
                $totalVinculadas++;
                $this->command->info("  -> '{$businessType->slug}': '{$catData['nombre']}' creada y vinculada.");
            }
        }

        $this->command->info('');
        $this->command->info('✅ BusinessTypeCategoriasSeeder completado.');
        $this->command->info("   Categorias creadas: {$totalCreadas}");
        $this->command->info("   Vinculaciones: {$totalVinculadas}");
    }
}
