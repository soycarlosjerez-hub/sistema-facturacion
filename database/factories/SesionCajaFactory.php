<?php

namespace Database\Factories;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SesionCaja>
 */
class SesionCajaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'caja_id' => Caja::factory(),
            'user_id' => User::factory(),
            'fecha_apertura' => fake()->dateTimeThisMonth(),
            'fecha_cierre' => null,
            'monto_inicial' => fake()->randomFloat(2, 0, 50000),
            'ventas_efectivo' => 0,
            'ventas_tarjeta' => 0,
            'ventas_transferencia' => 0,
            'monto_declarado' => null,
            'descuadre' => null,
            'estado' => 'abierta',
            'notas' => null,
        ];
    }

    public function cerrada(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'cerrada',
                'fecha_cierre' => fake()->dateTimeBetween('-1 month', 'now'),
                'monto_declarado' => fake()->randomFloat(2, 10000, 100000),
                'descuadre' => fake()->randomFloat(2, -500, 500),
            ];
        });
    }
}
