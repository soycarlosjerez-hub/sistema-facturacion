<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();

if ($user) {
    echo "Usuario encontrado:\n";
    echo "  ID: " . $user->id . "\n";
    echo "  Nombre: " . $user->name . "\n";
    echo "  Email: " . $user->email . "\n";
    echo "  Role actual: " . $user->role . "\n";
    echo "  Sucursal ID: " . $user->sucursal_id . "\n";
    
    // Actualizar role y sucursal si es necesario
    $user->role = 'vendedor';
    $user->sucursal_id = 1;
    $user->save();
    
    echo "\nActualización completada!\n";
    echo "Nuevo role: " . $user->role . "\n";
    echo "Nueva sucursal ID: " . $user->sucursal_id . "\n";
} else {
    echo "Usuario no encontrado!\n";
}