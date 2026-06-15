<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CategoryPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // General category permissions
            'categorias.view' => 'Ver categorías',
            'categorias.create' => 'Crear categorías',
            'categorias.edit' => 'Editar categorías',
            'categorias.delete' => 'Eliminar categorías',
            
            // Business type permissions
            'categorias.business-types.manage' => 'Gestionar tipos de negocio',
            
            // Per-type permissions (generated dynamically, but we seed base ones)
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Assign all permissions to admin role
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
        }
    }
}