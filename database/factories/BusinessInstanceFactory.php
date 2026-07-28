<?php

namespace Database\Factories;

use App\Models\BusinessInstance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessInstance>
 */
class BusinessInstanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'slug' => fake()->unique()->slug(),
            'rnc' => fake()->numerify('#########'),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
            'business_type_id' => null,
            'owner_user_id' => null,
            'configuracion' => [
                'restaurante_valida_stock' => '1',
            ],
            'activo' => true,
            'bloqueado' => false,
            'setup_completed' => true,
        ];
    }
}
