<?php

namespace Database\Seeders;

use App\Models\Lavador;
use Illuminate\Database\Seeder;

class LavadorSeeder extends Seeder
{
    public function run(): void
    {
        $lavadores = [
            [
                'nombre' => 'Juan Pérez',
                'tipo' => 'fijo',
                'porcentaje' => 30,
                'telefono' => '809-555-0101',
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos Jiménez',
                'tipo' => 'fijo',
                'porcentaje' => 25,
                'telefono' => '809-555-0102',
                'activo' => true,
            ],
            [
                'nombre' => 'Luis Rodríguez',
                'tipo' => 'temporal',
                'porcentaje' => 50,
                'telefono' => '809-555-0103',
                'activo' => true,
            ],
            [
                'nombre' => 'Pedro Martínez',
                'tipo' => 'temporal',
                'porcentaje' => 45,
                'telefono' => '809-555-0104',
                'activo' => true,
            ],
            [
                'nombre' => 'Ana Santana',
                'tipo' => 'temporal',
                'porcentaje' => 50,
                'telefono' => '809-555-0105',
                'activo' => false,
            ],
        ];

        foreach ($lavadores as $l) {
            Lavador::create($l);
        }

        $this->command->info('5 lavadores creados (3 activos, 2 fijos + 3 temporales)');
    }
}
