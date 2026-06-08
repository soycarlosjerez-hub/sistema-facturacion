<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesYUsuariosSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $vendedorRole = Role::firstOrCreate(['name' => 'vendedor', 'guard_name' => 'web']);

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('123456'),
            ]
        );

        // Asignar rol admin
        $admin->assignRole($adminRole);
    }
}
