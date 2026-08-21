<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find first user and assign root role
$user = App\Models\User::first();
if (!$user) {
    echo "No users found!\n";
    exit(1);
}

// Check current roles
echo "User: {$user->email} ({$user->name})\n";
echo "Current roles: " . ($user->roles->count() ? implode(', ', $user->roles->pluck('name')->toArray()) : 'NONE') . "\n";

// Assign root role
$user->syncRoles(['root']);
echo "Assigned role: root\n";

// Verify
$user->load('roles');
echo "Final roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
echo "Has tecnicas.view: " . ($user->hasPermissionTo('tecnicas.view') ? 'YES' : 'NO') . "\n";
echo "Has tecnicas.create: " . ($user->hasPermissionTo('tecnicas.create') ? 'YES' : 'NO') . "\n";

echo "\nDone! Logout and login again to see the buttons.\n";
