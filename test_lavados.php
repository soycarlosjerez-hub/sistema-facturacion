<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();
echo 'Usuario: ' . $user->email . PHP_EOL;
echo 'Role: ' . $user->role . PHP_EOL;

// Verificar business instance
\$instance = \$user->businessInstance;
if (\$instance) {
    echo 'Instancia: ' . \$instance->nombre . PHP_EOL;
    \$btype = \$instance->businessType;
    echo 'Modo facturación: ' . (\$btype->config['facturacion_modo'] ?? 'productos') . PHP_EOL;
} else {
    echo 'Sin business instance asignada' . PHP_EOL;
}