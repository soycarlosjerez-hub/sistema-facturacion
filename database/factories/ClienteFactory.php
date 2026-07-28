<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
            'rnc_cedula' => fake()->numerify('#########'),
            'rnc' => fake()->numerify('#########'),
            'tipo_documento' => '1',
            'tipo_cliente' => fake()->randomElement(['credito_fiscal', 'consumo', 'gubernamental', 'especial']),
            'limite_credito' => fake()->randomFloat(2, 0, 500000),
            'balance_pendiente' => 0,
            'plazo_pago_dias' => fake()->randomElement([15, 30, 45, 60]),
            'tasa_descuento_pct' => 0,
            'moneda' => 'RD',
            'auto_bloquear_credito' => false,
            'notas_internas' => null,
            'regimen_mensual' => false,
            'nit' => null,
            'persona_contacto' => null,
            'cargo_contacto' => null,
            'whatsapp' => null,
            'ciudad' => null,
            'provincia' => null,
            'codigo_postal' => null,
            'segmento' => 'pequeno',
            'origen_cliente' => 'walkin',
            'sector_actividad' => null,
            'activo' => true,
            'acceso_api' => false,
            'password' => null,
            'email_verified_at' => now(),
        ];
    }

    public function creditoFiscal(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'tipo_cliente' => 'credito_fiscal',
            'limite_credito' => fake()->randomFloat(2, 100000, 500000),
        ]);
    }

    public function conDeuda(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'balance_pendiente' => fake()->randomFloat(2, 5000, 50000),
        ]);
    }

    public function sinLimiteCredito(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'limite_credito' => 0,
        ]);
    }
}
