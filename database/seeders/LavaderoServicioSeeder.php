<?php

namespace Database\Seeders;

use App\Models\LavaderoServicio;
use Illuminate\Database\Seeder;

class LavaderoServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            ['nombre' => 'Lavado Exterior', 'precio' => 300, 'precio_compra' => 50, 'duracion_minutos' => 20, 'categoria' => 'Lavado', 'orden' => 1],
            ['nombre' => 'Lavado Completo', 'precio' => 500, 'precio_compra' => 100, 'duracion_minutos' => 40, 'categoria' => 'Lavado', 'orden' => 2],
            ['nombre' => 'Lavado de Motor', 'precio' => 800, 'precio_compra' => 150, 'duracion_minutos' => 45, 'categoria' => 'Lavado', 'orden' => 3],
            ['nombre' => 'Aspirado Interior', 'precio' => 400, 'precio_compra' => 50, 'duracion_minutos' => 30, 'categoria' => 'Detallado', 'orden' => 4],
            ['nombre' => 'Detail Completo', 'precio' => 2500, 'precio_compra' => 500, 'duracion_minutos' => 120, 'categoria' => 'Detallado', 'orden' => 5],
            ['nombre' => 'Encerado', 'precio' => 1200, 'precio_compra' => 200, 'duracion_minutos' => 60, 'categoria' => 'Detallado', 'orden' => 6],
            ['nombre' => 'Limpieza de Tapicería', 'precio' => 1500, 'precio_compra' => 300, 'duracion_minutos' => 90, 'categoria' => 'Detallado', 'orden' => 7],
            ['nombre' => 'Lavado de Alfombras', 'precio' => 600, 'precio_compra' => 100, 'duracion_minutos' => 40, 'categoria' => 'Detallado', 'orden' => 8],
            ['nombre' => 'Cambio de Aceite', 'precio' => 1000, 'precio_compra' => 600, 'duracion_minutos' => 30, 'categoria' => 'Mecánica', 'orden' => 9],
            ['nombre' => 'Lavado + Aspirado', 'precio' => 600, 'precio_compra' => 100, 'duracion_minutos' => 50, 'categoria' => 'Lavado', 'orden' => 10],
            ['nombre' => 'Paquete Completo (Lav+Asp+Encera)', 'precio' => 1800, 'precio_compra' => 300, 'duracion_minutos' => 110, 'categoria' => 'Paquetes', 'orden' => 11],
            ['nombre' => 'Ozonizado / Desinfección', 'precio' => 700, 'precio_compra' => 100, 'duracion_minutos' => 30, 'categoria' => 'Detallado', 'orden' => 12],
        ];

        foreach ($servicios as $s) {
            LavaderoServicio::create($s);
        }
    }
}
