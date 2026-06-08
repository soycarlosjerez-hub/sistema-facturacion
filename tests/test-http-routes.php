<?php
/**
 * Test: Verificar que las rutas HTTP funcionan correctamente
 * después de arreglar el Controller base.
 */

chdir('C:\wamp64\www\sistema-facturacion');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo PHP_EOL . "=== TEST: RUTAS HTTP COTIZACIONES ===" . PHP_EOL . PHP_EOL;

$pass = 0;
$fail = 0;

function check($name, $callback) {
    global $pass, $fail;
    try {
        $r = $callback();
        echo "  OK  | $name" . ($r ? " [$r]" : '') . PHP_EOL;
        $pass++;
    } catch (\Exception $e) {
        echo "  FAIL| $name - " . $e->getMessage() . PHP_EOL;
        $fail++;
    }
}

// Verificar que las rutas existen
echo "--- Rutas registradas ---" . PHP_EOL;

check("Ruta cotizaciones.index existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.index');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.show existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.show');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.ticket existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.ticket');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.ticketText existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.ticketText');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.enviar existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.enviar');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.pdf existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.pdf');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.buscarProducto existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.buscarProducto');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

check("Ruta cotizaciones.convertir existe", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.convertir');
    if (!$r) throw new \Exception("No existe");
    return $r->uri();
});

// Verificar que el controller se puede instanciar via container (como hace Laravel)
echo PHP_EOL . "--- Container Resolution ---" . PHP_EOL;

check("Container puede resolver CotizacionController", function() {
    try {
        $controller = app()->make(\App\Http\Controllers\CotizacionController::class);
        if (!$controller instanceof \App\Http\Controllers\CotizacionController) {
            throw new \Exception("Tipo incorrecto");
        }
        return get_class($controller);
    } catch (\Exception $e) {
        throw new \Exception("Error: " . $e->getMessage());
    }
});

check("Container puede resolver VentaController", function() {
    $controller = app()->make(\App\Http\Controllers\VentaController::class);
    return get_class($controller);
});

check("Container puede resolver ClienteController", function() {
    $controller = app()->make(\App\Http\Controllers\ClienteController::class);
    return get_class($controller);
});

// Verificar que los middleware se aplican correctamente
echo PHP_EOL . "--- Middleware en Rutas ---" . PHP_EOL;

check("Ruta index tiene middleware auth", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.index');
    $middleware = $r->gatherMiddleware();
    if (!in_array('auth', $middleware)) {
        throw new \Exception("No tiene 'auth'. Middleware: " . implode(', ', $middleware));
    }
    return implode(', ', array_slice($middleware, 0, 3));
});

check("Ruta index tiene middleware permission", function() {
    $r = Route::getRoutes()->getByName('cotizaciones.index');
    $middleware = $r->gatherMiddleware();
    $hasPerm = false;
    foreach ($middleware as $m) {
        if (str_contains($m, 'permission:')) {
            $hasPerm = true;
            break;
        }
    }
    if (!$hasPerm) throw new \Exception("No tiene permission middleware");
    return "OK";
});

// Verificar que el servicio se inyecta correctamente
echo PHP_EOL . "--- Inyeccion de Dependencias ---" . PHP_EOL;

check("CotizacionEmailService se inyecta", function() {
    $controller = app()->make(\App\Http\Controllers\CotizacionController::class);
    $reflection = new \ReflectionClass($controller);
    $prop = $reflection->getProperty('emailService');
    $prop->setAccessible(true);
    $service = $prop->getValue($controller);
    if (!$service instanceof \App\Services\CotizacionEmailService) {
        throw new \Exception("Tipo incorrecto");
    }
    return get_class($service);
});

check("PrintService se inyecta", function() {
    $controller = app()->make(\App\Http\Controllers\CotizacionController::class);
    $reflection = new \ReflectionClass($controller);
    $prop = $reflection->getProperty('printService');
    $prop->setAccessible(true);
    $service = $prop->getValue($controller);
    if (!$service instanceof \App\Services\PrintService) {
        throw new \Exception("Tipo incorrecto");
    }
    return get_class($service);
});

echo PHP_EOL . "========================================" . PHP_EOL;
echo " RESUMEN: $pass tests pasados, $fail tests fallidos" . PHP_EOL;
echo "========================================" . PHP_EOL;

exit($fail > 0 ? 1 : 0);
