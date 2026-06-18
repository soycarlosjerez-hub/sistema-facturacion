<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void
    {
        // Ensure the permission exists
        $perm = Permission::firstOrCreate(['name' => 'configuracion.edit', 'guard_name' => 'web']);
        // Assign it to the owner role
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->givePermissionTo($perm);
    }

    public function down(): void
    {
        $owner = Role::where('name', 'owner')->first();
        if ($owner) {
            $owner->revokePermissionTo('configuracion.edit');
        }
    }
};
