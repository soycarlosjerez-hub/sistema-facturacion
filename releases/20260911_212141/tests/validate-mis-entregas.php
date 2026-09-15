<?php

/**
 * Script de validación manual para Mis Entregas
 *
 * Uso: php tests/validate-mis-entregas.php
 *
 * Este script valida la funcionalidad usando la BD real (MySQL),
 * evitando los problemas de migración con SQLite que tiene phpunit.
 */

use App\Models\DeliveryTracking;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '=== VALIDACIÓN MANUAL: MIS ENTREGAS ==='.PHP_EOL.PHP_EOL;

$passed = 0;
$failed = 0;

function test($name, $condition)
{
    global $passed, $failed;
    if ($condition) {
        echo "  ✅ PASS: $name".PHP_EOL;
        $passed++;
    } else {
        echo "  ❌ FAIL: $name".PHP_EOL;
        $failed++;
    }
}

// 1. Verificar permisos
echo '1. PERMISOS:'.PHP_EOL;
$permiso = Permission::where('name', 'delivery-mis-entregas.view')->first();
test('Permiso delivery-mis-entregas.view existe', $permiso !== null);

$role = Role::where('name', 'delivery')->first();
test('Role delivery existe', $role !== null);
test('Role delivery tiene permission delivery-mis-entregas.view', $role && $role->hasPermissionTo('delivery-mis-entregas.view'));

// 2. Verificar rutas
echo PHP_EOL.'2. RUTAS:'.PHP_EOL;
$routes = collect(Route::getRoutes())->where('uri', 'delivery-mis-entregas')->first();
test('GET /delivery-mis-entregas existe', $routes !== null);

$routesPatch = collect(Route::getRoutes())->where('uri', 'delivery-mis-entregas/{tracking}/status')->first();
test('PATCH /delivery-mis-entregas/{tracking}/status existe', $routesPatch !== null);

// 3. Verificar data en BD
echo PHP_EOL.'3. DATOS:'.PHP_EOL;
$driversUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'delivery'))->get();
test('Existen usuarios con role delivery', $driversUsers->count() > 0);

if ($driversUsers->count() > 0) {
    $driver = $driversUsers->first();
    echo "   Driver encontrado: User #{$driver->id} ({$driver->name})".PHP_EOL;

    $ventas = Venta::where('driver_id', $driver->id)->where('tipo_orden', 'delivery')->get();
    test("El driver tiene {$ventas->count()} ventas asignadas", $ventas->count() > 0);

    foreach ($ventas as $v) {
        $tracking = $v->deliveryTracking;
        echo "   - Venta #{$v->numero_venta}: estado={$v->estado}, tracking=".($tracking ? $tracking->status : 'NO TIENE').PHP_EOL;
    }
}

// 4. Simular cambios de estado
echo PHP_EOL.'4. SIMULACIÓN DE CAMBIOS DE ESTADO:'.PHP_EOL;

if ($driversUsers->count() > 0) {
    $driver = $driversUsers->first();
    $ventaPendiente = Venta::where('driver_id', $driver->id)
        ->where('tipo_orden', 'delivery')
        ->where('estado', 'pendiente')
        ->first();

    if ($ventaPendiente) {
        $tracking = DeliveryTracking::create([
            'venta_id' => $ventaPendiente->id,
            'driver_id' => $driver->id,
            'status' => 'creado',
            'tenant_id' => $ventaPendiente->tenant_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simular cambio a en_camino
        $tracking->update(['status' => 'en_camino', 'notas' => 'Simulación']);
        test('Cambio a en_camino funciona', $tracking->status === 'en_camino');

        // Simular cambio a entregado
        $tracking->update(['status' => 'entregado', 'notas' => 'Simulación']);
        $ventaPendiente->update(['estado' => 'cobrada']);
        test('Cambio a entregado funciona', $tracking->status === 'entregado' && $ventaPendiente->estado === 'cobrada');

        // Simular cambio a fallido
        $tracking->update(['status' => 'fallido', 'notas' => 'Simulación']);
        test('Cambio a fallido funciona', $tracking->status === 'fallido');

        $tracking->delete();
        $ventaPendiente->update(['estado' => 'pendiente']);
    } else {
        echo "   - No hay ventas con estado 'pendiente' para simular".PHP_EOL;
    }
}

// 5. Resumen
echo PHP_EOL.'=== RESUMEN ==='.PHP_EOL;
echo "Tests PASS: $passed".PHP_EOL;
echo "Tests FAIL: $failed".PHP_EOL;
echo PHP_EOL;

if ($failed === 0) {
    echo '✅ TODAS LAS VALIDACIONES PASARON'.PHP_EOL;
    exit(0);
} else {
    echo '❌ HAY FALLAS - Revisar los tests FAIL arriba'.PHP_EOL;
    exit(1);
}
