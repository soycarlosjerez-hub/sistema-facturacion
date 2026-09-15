<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NcfSequence>
 */
class NcfSequenceFactory extends Factory
{
    public function definition(): array
    {
        $prefijos = ['B01', 'B02', 'B03', 'E31', 'E34', 'E41'];
        $prefijo = fake()->randomElement($prefijos);

        return [
            'nombre' => "NCF {$prefijo}",
            'prefijo' => $prefijo,
            'desde' => 1,
            'hasta' => 99999999,
            'actual' => fake()->numberBetween(1, 500),
            'fecha_vencimiento' => fake()->dateTimeBetween('+1 month', '+1 year'),
            'activo' => true,
        ];
    }
}
