<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantUsersSeeder extends Seeder
{
    public function run(): void
    {
        $instances = BusinessInstance::whereHas('businessType', fn($q) => $q->where('slug', 'restaurante'))->get();

        if ($instances->isEmpty()) {
            $this->command->warn('No se encontraron instancias de tipo restaurante.');
            return;
        }

        foreach ($instances as $instance) {
            $slug = $instance->slug;

            $users = [
                [
                    'email' => "gerente@{$slug}.com",
                    'name'  => "Gerente {$instance->nombre}",
                    'role'  => 'gerente',
                ],
                [
                    'email' => "cajero@{$slug}.com",
                    'name'  => "Cajero {$instance->nombre}",
                    'role'  => 'cajero',
                ],
                [
                    'email' => "mesero@{$slug}.com",
                    'name'  => "Mesero {$instance->nombre}",
                    'role'  => 'mesero',
                ],
                [
                    'email' => "cocinero@{$slug}.com",
                    'name'  => "Cocinero {$instance->nombre}",
                    'role'  => 'cocinero',
                ],
                [
                    'email' => "bartender@{$slug}.com",
                    'name'  => "Bartender {$instance->nombre}",
                    'role'  => 'bartender',
                ],
                [
                    'email' => "delivery@{$slug}.com",
                    'name'  => "Delivery {$instance->nombre}",
                    'role'  => 'delivery',
                ],
                [
                    'email' => "contador@{$slug}.com",
                    'name'  => "Contador {$instance->nombre}",
                    'role'  => 'contador',
                ],
            ];

            foreach ($users as $data) {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'                => $data['name'],
                        'password'            => Hash::make('Cambiar123'),
                        'role'                => $data['role'],
                        'business_type_id'    => $instance->business_type_id,
                        'business_instance_id'=> $instance->id,
                    ]
                );
                $user->syncRoles([$data['role']]);
            }

            $this->command->info("Usuarios creados para {$instance->nombre}");
        }
    }
}
