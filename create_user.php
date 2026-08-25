<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Crear usuario lavador
$user = new \App\Models\User();
$user->name = 'Lavador';
$user->email = 'lavador@gatonegro.do';
$user->password = bcrypt('12345678'); // password por defecto
$user->save();

// Asignar terminal de venta (codigo 2, id 1)
$terminal = \App\Models\Terminal::where('codigo', '2')->first();
$user->terminal_id = $terminal->id;
$user->save();

echo "Usuario creado y terminal asignada!\n";
echo "Usuario: " . $user->email . "\n";
echo "Terminal: " . $user->terminal->nombre . " (Codigo: " . $user->terminal->codigo . ")\n";
?>