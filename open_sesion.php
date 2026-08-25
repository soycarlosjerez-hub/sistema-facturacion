<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();
\Illuminate\Support\Facades\Auth::login($user);

// Abrir sesión de caja
$caja = \App\Models\Caja::where('tenant_id', 11)->first();
$sesion = \App\Models\SesionCaja::create([
    'caja_id' => $caja->id,
    'user_id' => $user->id,
    'fecha_apertura' => now(),
    'estado' => 'abierta',
    'monto_inicial' => 0,
    'tenant_id' => 11,
]);

echo "Sesión creada: ID " . $sesion->id . " para caja " . $caja->nombre . PHP_EOL;
?>