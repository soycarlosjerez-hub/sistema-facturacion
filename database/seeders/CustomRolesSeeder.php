<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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
            'mecanico' => 'Mecánico (Repuesto de Mecanica)',
            'lavador' => 'Lavador (Atención de lavadero)',
            'tecnico' => 'Técnico (Soporte técnico de equipos)',
            'soporte-n1' => 'Soporte Nivel 1',
            'soporte-n2' => 'Soporte Nivel 2',
            'vendedor-tecnico' => 'Vendedor Técnico',
            'almacen-tech' => 'Almacén Tecnológico',
        ];

        foreach ($customRoles as $roleName => $description) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }
}
