<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $roles = [
                'gerente@test.com'   => ['name' => 'Gerente Demo',     'role' => 'gerente'],
                'almacen@test.com'   => ['name' => 'Almacén Demo',     'role' => 'almacen'],
                'contador@test.com'  => ['name' => 'Contador Demo',    'role' => 'contador'],
            ];

            foreach ($roles as $email => $data) {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'     => $data['name'],
                        'password' => bcrypt('123456'),
                        'role'     => $data['role'],
                    ]
                );
                $user->syncRoles([$data['role']]);
            }
        } catch (Exception $e) {
            $exception = $e;
        }
    }
}
