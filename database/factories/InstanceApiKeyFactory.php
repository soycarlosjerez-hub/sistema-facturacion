<?php

namespace Database\Factories;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstanceApiKey>
 */
class InstanceApiKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'business_instance_id' => BusinessInstance::query()->first()?->id
                ?? BusinessInstance::factory(),
            'name' => fake()->words(3, true),
            'key' => hash('sha256', 'iak_'.bin2hex(random_bytes(24))),
            'last_used_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'is_active' => fake()->boolean(80),
            'created_by' => User::query()->first()?->id
                ?? User::factory(),
        ];
    }
}
