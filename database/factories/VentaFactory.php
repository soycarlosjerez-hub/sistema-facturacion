<?php

namespace Database\Factories;

use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    public function definition(): array
    {
        $states = ['completada', 'pendiente', 'cuenta_abierta', 'anulada'];
        $estado = fake()->randomElement($states);

        $methods = ['efectivo', 'tarjeta', 'transferencia', 'fiado', 'cuenta_abierta'];
        $metodo = fake()->randomElement($methods);

        if ($estado === 'pendiente') {
            $metodo = 'fiado';
        } elseif ($estado === 'cuenta_abierta') {
            $metodo = 'cuenta_abierta';
        }

        $types = ['sin', 'ncf', 'ecf'];
        $tipoComprobante = fake()->randomElement($types);

        return [
            'ncf' => $tipoComprobante === 'ncf' ? fake()->numerify('B010000####') : null,
            'ncf_tipo' => $tipoComprobante === 'ncf' ? 'B01' : null,
            'ncf_vencimiento' => $tipoComprobante === 'ncf' ? fake()->dateTimeBetween('+1 month', '+1 year') : null,
            'tipo_comprobante' => $tipoComprobante,
            'encf' => $tipoComprobante === 'ecf' ? fake()->numerify('E31000000##') : null,
            'estado' => $estado,
            'fecha' => fake()->dateTimeThisMonth(),
            'subtotal' => fake()->randomFloat(2, 100, 50000),
            'impuestos' => fake()->randomFloat(2, 0, 10000),
            'descuento' => fake()->randomFloat(2, 0, 2000),
            'general_descuento' => 0,
            'total' => fake()->randomFloat(2, 100, 50000),
            'descuento_tipo' => 'monto',
            'propina' => 0,
            'delivery_fee' => 0,
            'cargo_servicio' => 0,
        ];
    }

    public function completada(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'completada',
        ]);
    }

    public function pendiente(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
        ]);
    }

    public function anulada(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'anulada',
        ]);
    }

    public function conNcf(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'tipo_comprobante' => 'ncf',
            'ncf' => fake()->numerify('B010000####'),
            'ncf_tipo' => 'B01',
        ]);
    }

    public function conEcf(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'tipo_comprobante' => 'ecf',
            'encf' => fake()->numerify('E31000000##'),
        ]);
    }

    public function conDescuento(): Factory
    {
        return $this->state(function (array $attributes) {
            $subtotal = (float) $attributes['subtotal'];
            $descuento = $subtotal * 0.2;
            return [
                'descuento' => $descuento,
                'total' => $subtotal - $descuento + (float) $attributes['impuestos'],
            ];
        });
    }
}
