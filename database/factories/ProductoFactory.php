<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::factory(),
            'nombre' => fake()->words(3, true),
            'codigo_barras' => fake()->ean13(),
            'descripcion' => fake()->sentence(),
            'marca' => fake()->word(),
            'precio' => fake()->randomFloat(2, 50, 10000),
            'precio_compra' => fake()->randomFloat(2, 20, 5000),
            'unidad_medida' => 'Unidad',
            'itbis_porcentaje' => fake()->randomElement([0, 18]),
            'stock' => fake()->numberBetween(0, 500),
            'stock_minimo' => 5,
            'activo' => true,
            'imagen' => null,
        ];
    }

    public function sinStock(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function conStockMinimo(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 5),
        ]);
    }

    public function exentoItbis(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'itbis_porcentaje' => 0,
        ]);
    }

    public function conCategoria(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'categoria_id' => Categoria::factory(),
        ]);
    }
}
