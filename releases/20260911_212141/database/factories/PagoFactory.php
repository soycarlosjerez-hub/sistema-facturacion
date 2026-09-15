<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pago>
 */
class PagoFactory extends Factory
{
    public function definition(): array
    {
        $metodos = ['efectivo', 'tarjeta', 'transferencia'];

        return [
            'monto' => fake()->randomFloat(2, 100, 50000),
            'metodo_pago' => fake()->randomElement($metodos),
            'nota' => null,
            'fecha_pago' => fake()->dateTimeThisMonth(),
        ];
    }
}
