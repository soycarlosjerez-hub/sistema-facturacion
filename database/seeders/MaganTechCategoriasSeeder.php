<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaganTechCategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $instanceId = DB::table('business_instances')->where('slug', 'magan-tech')->value('id');

        if (!$instanceId) {
            $this->command->error('No se encontr' . 'o la instancia magan-tech.');
            return;
        }

        $categorias = [
            ['nombre' => 'Cables', 'descripcion' => 'Cables de red, HDMI, USB y accesorios de cableado'],
            ['nombre' => 'Computadoras', 'descripcion' => 'Laptops, desktops, componentes y accesorios para PC'],
            ['nombre' => 'Monitores', 'descripcion' => 'Monitores gaming, profesionales y portatiles'],
            ['nombre' => 'Impresoras', 'descripcion' => 'Impresoras laser, inkjet, multifuncionales y toners'],
            ['nombre' => 'Redes', 'descripcion' => 'Routers, switches, access points, antenas y cables de red'],
            ['nombre' => 'Seguridad', 'descripcion' => 'Camaras de vigilancia, DVRs, NVRs y sensores'],
            ['nombre' => 'Almacenamiento', 'descripcion' => 'Discos duros SSD/HDD, memorias USB, tarjetas de memoria'],
            ['nombre' => 'Audio', 'descripcion' => 'Bocinas, audifonos, microfonos y accesorios de audio'],
            ['nombre' => 'Perifericos', 'descripcion' => 'Teclados, mice, webcams, lectores y adaptadores'],
            ['nombre' => 'Componentes', 'descripcion' => 'Placas madre, CPUs, memorias RAM, GPUs, power supplies y cases'],
            ['nombre' => 'Carga y Energia', 'descripcion' => 'Cargadores, UPS, baterias, adaptadores y extensiones'],
            ['nombre' => 'Tablets y Moviles', 'descripcion' => 'Tablets, smartphones y accesorios mobile'],
            ['nombre' => 'Corte y Diseño', 'descripcion' => 'Maquinas de corte Cricut, herramientas y materiales'],
            ['nombre' => 'Mobiliario', 'descripcion' => 'Mesas, sillas, soportes y organizadores'],
            ['nombre' => 'Streaming', 'descripcion' => 'Dispositivos de streaming como Fire TV Stick y Roku'],
            ['nombre' => 'Climatizacion', 'descripcion' => 'Aires acondicionados y accesorios de climatizacion'],
            ['nombre' => 'Registros', 'descripcion' => 'Cajas registradoras, cintas y suministros de registro'],
            ['nombre' => 'Herramientas', 'descripcion' => 'Herramientas de diagnostico, limpieza y mantenimiento'],
            ['nombre' => 'Licencias', 'descripcion' => 'Licencias de software y servicios digitales'],
        ];

        $categoriaMap = [];
        $now = now();

        foreach ($categorias as $cat) {
            $catId = DB::table('categorias')->insertGetId([
                'tenant_id' => $instanceId,
                'nombre' => $cat['nombre'],
                'descripcion' => $cat['descripcion'],
                'activa' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $categoriaMap[$cat['nombre']] = $catId;
        }

        $this->command->info('Categorias creadas para maganTech: ' . count($categorias));
        $this->command->table(
            ['ID', 'Nombre', 'ID BD'],
            array_map(fn($c, $id) => ['id' => $id, 'nombre' => $c, 'db_id' => $categoriaMap[$c]], array_keys($categorias), array_values($categoriaMap))
        );
    }
}
