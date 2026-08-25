<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar si ya existe la terminal 2 para tenant 10
$terminalExistente = \App\Models\Terminal::where('codigo', '2')->where('tenant_id', 10)->first();

if ($terminalExistente) {
    echo "Terminal 2 ya existe: ID " . $terminalExistente->id . " - " . $terminalExistente->nombre . PHP_EOL;
} else {
    // Crear nueva terminal
    $terminal = new \App\Models\Terminal();
    $terminal->tenant_id = 10;
    $terminal->nombre = 'Terminal Venta Lavado';
    $terminal->codigo = '2';
    $terminal->ubicacion = 'Estación de Lavado - Gato Negro';
    $terminal->activo = true;
    $terminal->save();
    
    echo "Terminal 2 creada exitosamente!" . PHP_EOL;
    echo "ID: " . $terminal->id . PHP_EOL;
    echo "Nombre: " . $terminal->nombre . PHP_EOL;
    echo "Codigo: " . $terminal->codigo . PHP_EOL;
}
?>