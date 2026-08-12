<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class EmbutidosCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Salami', 'descripcion' => 'Salami dominicano y variedades', 'activa' => true],
            ['nombre' => 'Longaniza', 'descripcion' => 'Longanizas artesanales y parrilleras', 'activa' => true],
            ['nombre' => 'Chorizo', 'descripcion' => 'Chorizos dulces, picantes y españoles', 'activa' => true],
            ['nombre' => 'Jamón', 'descripcion' => 'Jamones de cerdo, pavo y especialidades', 'activa' => true],
            ['nombre' => 'Mortadela / Bologna', 'descripcion' => 'Mortadelas y bolognas', 'activa' => true],
            ['nombre' => 'Tocino', 'descripcion' => 'Tocino y panceta ahumados', 'activa' => true],
            ['nombre' => 'Quesos', 'descripcion' => 'Quesos para charcutería: de freír, blanco y especiales', 'activa' => true],
            ['nombre' => 'Otros Embutidos', 'descripcion' => 'Butifarras, surtidos y más', 'activa' => true],
        ];

        $creadas = 0;
        foreach ($categorias as $catData) {
            $existente = Categoria::where('nombre', $catData['nombre'])->whereNull('tenant_id')->first();
            if ($existente) {
                $this->command->info("Categoria ya existe: {$catData['nombre']} (ID {$existente->id})");
                continue;
            }

            Categoria::create(array_merge($catData, ['tenant_id' => null]));
            $creadas++;
            $this->command->info("Categoria creada: {$catData['nombre']}");
        }

        $this->command->info("EmbutidosCategoriaSeeder finalizado. {$creadas} categorias nuevas.");
    }
}