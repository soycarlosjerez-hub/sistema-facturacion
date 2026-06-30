<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'owner@sistema-facturacion.com')->first();
echo 'User: ' . $user->name . PHP_EOL;
echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;

// Login the user for Auth facade
Auth::login($user);

// Check permissions
$perms = ['owner.dashboard', 'owner.instances.view', 'owner.business-types.view'];
foreach ($perms as $perm) {
    echo "Permission '$perm': " . ($user->can($perm) ? 'YES' : 'NO') . PHP_EOL;
}

// Check what permissions the owner role has
$role = \Spatie\Permission\Models\Role::where('name', 'owner')->first();
if ($role) {
    echo "Owner role permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . PHP_EOL;
}