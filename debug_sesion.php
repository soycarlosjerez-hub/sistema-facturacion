<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simular el usuario logueado
$user = \App\Models\User::where('email', 'lavador@gatonegro.do')->first();
\Illuminate\Support\Facades\Auth::login($user);

echo "Usuario: " . auth()->user()->email . PHP_EOL;
echo "Business Instance ID: " . auth()->user()->business_instance_id . PHP_EOL;

// Verificar cajas
$cajas = \App\Models\Caja::where('tenant_id', 11)->activas()->get();
echo "Cajas para tenant 11: " . $cajas->count() . PHP_EOL;
foreach ($cajas as $c) {
    echo "  Caja: " . $c->nombre . " (ID: " . $c->id . ")" . PHP_EOL;
    
    // Verificar sesión abierta
    $sesion = $c->sesionActiva();
    if ($sesion) {
        echo "  Sesión activa: ID " . $sesion->id . " - Usuario: " . $sesion->user_id . " - Estado: " . $sesion->estado . PHP_EOL;
    } else {
        echo "  Sin sesión activa" . PHP_EOL;
    }
}

// Verificar productos
$productos = \App\Models\Producto::where('tenant_id', 11)->where('activo', true)->get();
echo "Productos: " . $productos->count() . PHP_EOL;

// Verificar servicios
$servicios = \App\Models\LavaderoServicio::where('tenant_id', 11)->where('activo', true)->get();
echo "Servicios: " . $servicios->count() . PHP_EOL;
foreach ($servicios as $s) {
    echo "  - " . $s->nombre . " (ID: " . $s->id . ")" . PHP_EOL;
}
?>