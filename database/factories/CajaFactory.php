<?php

namespace Database\Factories;

use App\Models\Caja;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caja>
 */
class CajaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->words(2, true),
            'codigo' => strtoupper(fake()->bothify('CAJA-??')),
            'sucursal_id' => Sucursal::factory(),
            'ubicacion' => fake()->randomElement(['Principal', 'Segundo piso', 'Exterior']),
            'estado' => 'operativa',
            'activo' => true,
        ];
    }

    public function inactiva(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
