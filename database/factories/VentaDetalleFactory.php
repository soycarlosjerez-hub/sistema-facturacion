<?php

namespace Database\Factories;

use App\Models\VentaDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VentaDetalle>
 */
class VentaDetalleFactory extends Factory
{
    public function definition(): array
    {
        $cantidad = fake()->numberBetween(1, 20);
        $precio = fake()->randomFloat(2, 50, 5000);
        $subtotal = $cantidad * $precio;

        return [
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $subtotal,
            'descuento' => 0,
            'descuento_tipo' => 'monto',
        ];
    }

    public function conDescuentoMonto(): Factory
    {
        return $this->state(function (array $attributes) {
            $descuento = (float) $attributes['subtotal'] * 0.1;
            return [
                'descuento' => $descuento,
                'descuento_tipo' => 'monto',
            ];
        });
    }

    public function conDescuentoPorcentaje(): Factory
    {
        return $this->state(function (array $attributes) {
            $descuento = (float) $attributes['subtotal'] * 0.15;
            return [
                'descuento' => $descuento,
                'descuento_tipo' => 'porcentaje',
            ];
        });
    }
}
