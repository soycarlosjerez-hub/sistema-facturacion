<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'nombre' => fake()->words(2, true),
            'descripcion' => fake()->sentence(),
            'activa' => true,
        ];
    }
}
