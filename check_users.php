<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check all users
$users = App\Models\User::all();
echo "=== USERS ===\n";
foreach ($users as $u) {
    echo "ID: {$u->id}, Email: {$u->email}, Name: {$u->name}\n";
    echo "  Roles: " . ($u->roles->count() ? implode(', ', $u->roles->pluck('name')->toArray()) : 'NONE') . "\n";
    
    if ($u->roles->count() === 0) {
        echo "  ** WARNING: No roles assigned! **\n";
    }
}

echo "\n=== ALL PERMISONS WITH 'tecn' ===\n";
$techPerms = App\Models\Permission::where('name', 'like', '%tecn%')->get();
foreach ($techPerms as $perm) {
    echo "  [{$perm->id}] {$perm->name}\n";
}

echo "\n=== Checking tecnicas.create assignment ===\n";
$perm = App\Models\Permission::where('name', 'tecnicas.create')->first();
if ($perm) {
    $roles = \DB::table('role_has_permissions')
        ->where('permission_id', $perm->id)
        ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
        ->select('roles.name', 'roles.id')
        ->get();
    echo "Assigned to roles:\n";
    foreach ($roles as $r) {
        echo "  - {$r->name} (ID: {$r->id})\n";
    }
} else {
    echo "Permission 'tecnicas.create' NOT FOUND\n";
}
