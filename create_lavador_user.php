<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar si el usuario ya existe
$existing = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();

if ($existing) {
    echo "Usuario ya existe: " . $existing->email . "\n";
} else {
    // Crear usuario lavador
    $user = new \App\Models\User();
    $user->name = 'Lavador Gato Negro';
    $user->email = 'lavador@gatonegro.do';
    $user->password = bcrypt('12345678');
    $user->role = 'vendedor'; // Rol que tendrá acceso a terminal de venta
    $user->sucursal_id = 1; // Asumimos sucursal 1
    $user->save();
    
    echo "Usuario creado exitosamente!\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Sucursal ID: " . $user->sucursal_id . "\n";
}
?>