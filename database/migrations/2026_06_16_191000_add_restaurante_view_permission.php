<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    public function up(): void
    {
        // Create permission if it does not exist
        $permission = Permission::firstOrCreate(['name' => 'restaurante.view']);

        // Assign to admin-business role (create role if missing)
        $role = Role::firstOrCreate(['name' => 'admin-business']);
        $role->givePermissionTo($permission);
    }

    public function down(): void
    {
        // Remove permission from role and delete permission
        $permission = Permission::where('name', 'restaurante.view')->first();
        if ($permission) {
            $role = Role::where('name', 'admin-business')->first();
            if ($role) {
                $role->revokePermissionTo($permission);
            }
            $permission->delete();
        }
    }
};
