<?php

namespace Database\Factories;

use App\Models\Caja;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sucursal>
 */
class SucursalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => strtoupper(fake()->bothify('SUC-??')),
            'nombre' => fake()->company() . ' - Sucursal ' . fake()->numberBetween(1, 5),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'rnc' => fake()->numerify('#########'),
            'activa' => true,
            'es_matriz' => fake()->boolean(20),
        ];
    }
}
