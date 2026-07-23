<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        $role = Role::where('name', 'admin-business')->first();
        if (!$role) {
            return;
        }

        $deletePermissions = [
            'productos.delete',
            'compras.delete',
            'proveedores.delete',
            'clientes.delete',
            'almacenes.delete',
        ];

        foreach ($deletePermissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $role->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'admin-business')->first();
        if (!$role) {
            return;
        }

        $role->revokePermissionTo([
            'productos.delete',
            'compras.delete',
            'proveedores.delete',
            'clientes.delete',
            'almacenes.delete',
        ]);
    }
};
