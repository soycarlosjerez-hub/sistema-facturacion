<?php

namespace Database\Factories;

use App\Models\TipoVenta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoVenta>
 */
class TipoVentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement(['Contado', 'Crédito']),
            'descripcion' => fake()->sentence(),
        ];
    }
}
