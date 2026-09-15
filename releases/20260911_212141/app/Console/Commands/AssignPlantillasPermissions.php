<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPlantillasPermissions extends Command
{
    protected $signature = 'plantillas:assign-permissions';

    protected $description = 'Asigna permisos de plantillas a todos los usuarios y roles de instancia';

    public function handle()
    {
        $this->info('=== Asignando permisos de plantillas ===');

        // 1. Asegurar que los permisos existen
        $plantillaPerms = ['plantillas.view', 'plantillas.create', 'plantillas.edit', 'plantillas.delete'];
        foreach ($plantillaPerms as $permKey) {
            if (! Permission::where('name', $permKey)->exists()) {
                Permission::create(['name' => $permKey]);
                $this->info("  ✓ Creado permiso: $permKey");
            }
        }

        // 2. Asignar permisos a todos los InstanceRoles que tengan el módulo "plantillas"
        $roles = \DB::table('instance_role_modules')
            ->where('modulo_key', 'plantillas')
            ->where('is_visible', 1)
            ->distinct()
            ->pluck('instance_role_id');

        $assigned = 0;
        $total = 0;
        foreach ($roles as $roleId) {
            $role = Role::find($roleId);
            if ($role) {
                foreach ($plantillaPerms as $permKey) {
                    $perm = Permission::where('name', $permKey)->first();
                    if ($perm && ! $role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($perm);
                        $assigned++;
                        $total++;
                    }
                }
            }
        }

        // 3. Asignar permisos directamente a los usuarios que tienen instance_role_id
        $users = \DB::table('users')
            ->whereNotNull('instance_role_id')
            ->distinct()
            ->pluck('id');

        foreach ($users as $userId) {
            $user = \App\Models\User::with('roles')->find($userId);
            if ($user) {
                foreach ($plantillaPerms as $permKey) {
                    $perm = Permission::where('name', $permKey)->first();
                    if ($perm && ! $user->hasPermissionTo($perm)) {
                        $user->givePermissionTo($perm);
                        $assigned++;
                    }
                }
            }
        }

        // 4. Asignar permisos a los roles de spatie (admin, gerente, cajero, etc.)
        $spatieRoles = ['admin', 'admin-business', 'gerente', 'cajero', 'mesero', 'cocinero', 'bartender', 'delivery', 'contador', 'vendedor'];
        foreach ($spatieRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                foreach ($plantillaPerms as $permKey) {
                    $perm = Permission::where('name', $permKey)->first();
                    if ($perm && ! $role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($perm);
                        $assigned++;
                    }
                }
            }
        }

        $this->info('=== Completado ===');
        $this->info("Permisos asignados: $assigned");
        $this->info('Roles de instancia procesados: '.$roles->count());
        $this->info('Usuarios procesados: '.$users->count());
    }
}
