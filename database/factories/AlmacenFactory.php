<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Almacen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Almacen>
 */
class AlmacenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement(['Principal', 'Secundario', 'Depósito Central', 'Tienda']),
            'ubicacion' => fake()->city(),
        ];
    }
}
