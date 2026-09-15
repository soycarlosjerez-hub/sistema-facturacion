<?php

namespace Database\Factories;

use App\Models\BusinessType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessTypeFactory extends Factory
{
    protected $model = BusinessType::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->word,
            'nombre' => $this->faker->randomElement(['Restaurante', 'Retail', 'Mayorista', 'Servicios', 'Mixto']),
            'descripcion' => $this->faker->sentence(),
            'color' => $this->faker->randomElement(['info', 'success', 'warning', 'primary', 'secondary']),
            'icon' => $this->faker->randomElement(['bi-grid', 'bi-cup-straw', 'bi-cart-plus']),
            'activo' => true,
            'orden' => 0,
        ];
    }
}
