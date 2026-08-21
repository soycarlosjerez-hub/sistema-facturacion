<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
echo "User: " . $user->name . "\n";
echo "Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
echo "Has tecnicas.view: " . ($user->hasPermissionTo('tecnicas.view') ? 'YES' : 'NO') . "\n";
echo "Has tecnicas.create: " . ($user->hasPermissionTo('tecnicas.create') ? 'YES' : 'NO') . "\n";

// Show all permissions for this user's role(s)
echo "\nAll permissions assigned to user's role(s):\n";
$allPerms = [];
foreach ($user->roles as $role) {
    $perms = $role->permissions->pluck('name')->toArray();
    echo "  Role [{$role->name}]: " . implode(', ', $perms) . "\n";
    $allPerms = array_merge($allPerms, $perms);
}

// Check technology permissions specifically
echo "\nTechnology module permissions:\n";
$techPerms = ['tecnicas.view', 'tecnicas.create', 'tecnicas.edit', 'tecnicas.delete',
              'equipos.view', 'equipos.create', 'equipos.edit', 'equipos.delete',
              'tecnicos.view', 'tecnicos.create', 'tecnicos.edit', 'tecnicos.delete',
              'domotica.view', 'domotica.create', 'domotica.edit', 'domotica.delete',
              'marcas-tecnologicas.view', 'marca-tecnologicas.create',
              'licencias-software.view', 'licencias-software.create',
              'redes-config.view', 'redes-config.create',
              'presupuestos.view', 'presupuestos.create',
              'tecnica-especialidades.view', 'tecnica-especialidades.create',
              'garantias-config.view', 'garantias-config.create'];

foreach ($techPerms as $perm) {
    $has = $user->hasPermissionTo($perm);
    $hasRole = in_array($perm, $allPerms);
    echo "  $perm: " . ($has ? 'YES' : 'NO') . " (in role: " . ($hasRole ? 'YES' : 'NO') . ")\n";
}
