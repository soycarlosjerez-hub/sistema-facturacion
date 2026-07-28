<?php

namespace Database\Factories;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'business_instance_id' => BusinessInstance::factory(),
            'role' => 'admin',
            'sucursal_id' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function gerente(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'gerente',
        ]);
    }

    public function empleado(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'empleado',
        ]);
    }
}
