<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

foreach (['admin', 'gerente', 'vendedor', 'admin-business', 'owner'] as $roleName) {
    $role = Role::findByName($roleName);
    $perms = DB::table('model_has_permissions')
        ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
        ->where('model_has_permissions.role_id', $role->id)
        ->where('permissions.name', 'like', 'arte%')
        ->pluck('permissions.name')
        ->toArray();
    echo $roleName . ' => ' . implode(', ', $perms) . PHP_EOL;
}
