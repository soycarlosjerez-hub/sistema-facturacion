<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoClima;

class TiposClimaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            // Split Systems
            ['slug' => 'split-solo-frio', 'nombre' => 'Split Solo Frío', 'categoria' => 'residencial', 'icono' => 'snowflake', 'orden' => 1],
            ['slug' => 'split-calor-frio', 'nombre' => 'Split Calor Frío', 'categoria' => 'residencial', 'icono' => 'thermometer-half', 'orden' => 2],
            ['slug' => 'split-inverter', 'nombre' => 'Split Inverter', 'categoria' => 'residencial', 'icono' => 'zap', 'orden' => 3],
            
            // Multi Split
            ['slug' => 'multi-split', 'nombre' => 'Multi Split', 'categoria' => 'residencial', 'icono' => 'layers', 'orden' => 4],
            ['slug' => 'multi-hybrid', 'nombre' => 'Multi Hybrid', 'categoria' => 'residencial', 'icono' => 'recycle', 'orden' => 5],
            
            // Cassetes
            ['slug' => 'cassette-4-vias', 'nombre' => 'Cassette 4 Vías', 'categoria' => 'comercial', 'icono' => 'grid-3x3', 'orden' => 6],
            ['slug' => 'cassette-1-via', 'nombre' => 'Cassette 1 Vía', 'categoria' => 'comercial', 'icono' => 'columns', 'orden' => 7],
            
            // Conductos
            ['slug' => 'conductos', 'nombre' => 'Conductos', 'categoria' => 'comercial', 'icono' => 'arrow-down-circle', 'orden' => 8],
            
            // Piso Techo
            ['slug' => 'piso-techo', 'nombre' => 'Piso Techo', 'categoria' => 'comercial', 'icono' => 'arrows-expand-vertical', 'orden' => 9],
            
            // Centrales
            ['slug' => 'central-chiller', 'nombre' => 'Central Chiller', 'categoria' => 'industrial', 'icono' => 'building', 'orden' => 10],
            ['slug' => 'vrv-vrf', 'nombre' => 'Sistema VRV/VRV', 'categoria' => 'industrial', 'icono' => 'cpu', 'orden' => 11],
            ['slug' => 'torre-refrigeracion', 'nombre' => 'Torre de Refrigeración', 'categoria' => 'industrial', 'icono' => 'cloud', 'orden' => 12],
            
            // Paquetarios
            ['slug' => 'paquetario', 'nombre' => 'Paquetario', 'categoria' => 'comercial', 'icono' => 'box', 'orden' => 13],
            
            // Mini Split
            ['slug' => 'mini-split', 'nombre' => 'Mini Split', 'categoria' => 'residencial', 'icono' => 'wind', 'orden' => 14],
        ];

        foreach ($tipos as $tipo) {
            TipoClima::firstOrCreate(
                ['slug' => $tipo['slug']],
                [
                    'nombre' => $tipo['nombre'],
                    'categoria' => $tipo['categoria'],
                    'icono' => $tipo['icono'],
                    'orden' => $tipo['orden'],
                    'activo' => true,
                ]
            );
        }
    }
}
