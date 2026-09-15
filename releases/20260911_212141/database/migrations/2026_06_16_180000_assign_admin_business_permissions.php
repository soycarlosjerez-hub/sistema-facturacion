<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the role exists
        $role = Role::firstOrCreate(['name' => 'admin-business']);

        // Grant all existing permissions to this role (you can refine later)
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = Role::where('name', 'admin-business')->first();
        if ($role) {
            $role->revokePermissionTo(Permission::all());
        }
    }
};
