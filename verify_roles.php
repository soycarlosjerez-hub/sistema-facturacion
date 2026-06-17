<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$roles = Spatie\Permission\Models\Role::all();
echo "Total roles: " . $roles->count() . "\n\n";
foreach ($roles as $r) {
    echo $r->name . ': ' . $r->permissions->count() . " permisos\n";
}