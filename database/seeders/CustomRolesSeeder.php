<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles estándar (administrador, gerente, vendedor, almacen, contador)
        $standardRoles = ['admin', 'gerente', 'vendedor', 'almacen', 'contador'];
        
        // Crear roles estándar si no existen
        foreach ($standardRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        
        // Roles personalizados para el sistema de business types
        $customRoles = [
            'root' => 'Administrador Principal (acceso total)',
            'admin-business' => 'Administrador de Negocio (restringido)',
            'owner' => 'Dueño del Sistema (Super Admin Multi-tenant)',
        ];
        
        foreach ($customRoles as $roleName => $description) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }
}
