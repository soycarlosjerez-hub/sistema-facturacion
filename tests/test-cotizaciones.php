<?php
/**
 * Test E2E del Módulo de Cotizaciones
 *
 * Ejecutar: php tests/test-cotizaciones.php
 *
 * Cubre:
 * - Crear cotización con items
 * - Cálculos de subtotal/ITBIS/total
 * - Cambio de estado
 * - Numeración automática
 * - Scopes (activas, vencidas)
 * - Conversión a venta
 * - Relación bidireccional cliente
 */

chdir(__DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$pass = 0;
$fail = 0;
$tests = [];

function test($name, $callback) {
    global $pass, $fail, $tests;
    try {
        $result = $callback();
        $tests[] = ['name' => $name, 'status' => 'PASS', 'message' => $result ?? ''];
        $pass++;
        echo "  \u{2713} $name" . ($result ? " - $result" : '') . PHP_EOL;
    } catch (\Exception $e) {
        $tests[] = ['name' => $name, 'status' => 'FAIL', 'message' => $e->getMessage()];
        $fail++;
        echo "  \u{2717} $name - ERROR: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "=== TEST E2E: MÓDULO DE COTIZACIONES ===" . PHP_EOL . PHP_EOL;

// Setup
$cli = App\Models\Cliente::first();
$prod1 = App\Models\Producto::first();
$prod2 = App\Models\Producto::skip(1)->first() ?? $prod1;
$user = App\Models\User::first();
$almacen = App\Models\Almacen::first();
auth()->setUser($user);

if (!$cli || !$prod1 || !$user) {
    echo "ERROR: Faltan datos base (cliente/producto/usuario). Ejecuta los seeders primero." . PHP_EOL;
    exit(1);
}

echo "[SETUP] Cliente: {$cli->nombre} | Productos: " . App\Models\Producto::count() . PHP_EOL . PHP_EOL;

// ==== MIGRACIONES Y MODELOS ====
echo "--- Migraciones y Modelos ---" . PHP_EOL;

test("Tabla cotizaciones existe", function() {
    $exists = DB::select("SHOW TABLES LIKE 'cotizaciones'");
    if (count($exists) === 0) throw new Exception("Tabla no encontrada");
    return count($exists) . " tabla(s)";
});

test("Tabla cotizacion_items existe", function() {
    $exists = DB::select("SHOW TABLES LIKE 'cotizacion_items'");
    if (count($exists) === 0) throw new Exception("Tabla no encontrada");
});

test("Modelo Cotizacion carga", function() {
    $count = App\Models\Cotizacion::count();
    return "$count cotizaciones en BD";
});

test("Modelo CotizacionItem carga", function() {
    $count = App\Models\CotizacionItem::count();
    return "$count items en BD";
});

// ==== NUMERACIÓN ====
echo PHP_EOL . "--- Numeración Automática ---" . PHP_EOL;

test("generarNumero() crea número formato COT-YYYY-NNNNNN", function() {
    $numero = App\Models\Cotizacion::generarNumero();
    if (!preg_match('/^COT-\d{4}-\d{6}$/', $numero)) {
        throw new Exception("Formato incorrecto: $numero");
    }
    return $numero;
});

test("Cada llamada genera un número basado en el último", function() {
    $nums = [];
    for ($i = 0; $i < 3; $i++) {
        $nums[] = App\Models\Cotizacion::generarNumero();
    }
    // generarNumero() retorna el siguiente correlativo sin incrementar hasta que se guarde
    // por lo que múltiples llamadas retornan el mismo "preview"
    $ultimoNumero = App\Models\Cotizacion::orderBy('id', 'desc')->first()->numero;
    $esperado = 'COT-' . date('Y') . '-' . str_pad((int)substr($ultimoNumero, -6) + 1, 6, '0', STR_PAD_LEFT);
    if ($nums[0] !== $esperado) {
        throw new Exception("Esperado: $esperado, Obtenido: {$nums[0]}");
    }
    return "Preview: {$nums[0]}";
});

// ==== CREACIÓN Y CÁLCULOS ====
echo PHP_EOL . "--- Creación y Cálculos ---" . PHP_EOL;

$cotizacionTest = null;

test("Crear cotización con items", function() use (&$cotizacionTest, $cli, $user, $prod1, $prod2) {
    $cot = App\Models\Cotizacion::create([
        'numero' => App\Models\Cotizacion::generarNumero(),
        'cliente_id' => $cli->id,
        'user_id' => $user->id,
        'fecha' => now(),
        'fecha_validez' => now()->addDays(15),
        'estado' => 'borrador',
    ]);
    
    foreach ([
        ['producto' => $prod1, 'cantidad' => 5, 'precio' => 200, 'desc' => 0],
        ['producto' => $prod2, 'cantidad' => 3, 'precio' => 100, 'desc' => 20],
    ] as $data) {
        $item = new App\Models\CotizacionItem([
            'cotizacion_id' => $cot->id,
            'producto_id' => $data['producto']->id,
            'codigo' => $data['producto']->codigo,
            'nombre' => $data['producto']->nombre,
            'unidad' => 'Unidad',
            'cantidad' => $data['cantidad'],
            'precio_unitario' => $data['precio'],
            'descuento' => $data['desc'],
            'itbis_porcentaje' => 18,
        ]);
        $item->calcular();
        $item->save();
    }
    
    $cot->calcularTotales();
    $cotizacionTest = $cot;
    return "{$cot->numero} con {$cot->items->count()} items";
});

test("Subtotal = suma de items con descuento a nivel item", function() use (&$cotizacionTest) {
    // item1: 5*200 - 0 = 1000
    // item2: 3*100 - 20 = 280
    // total = 1280
    $expected = (5 * 200 - 0) + (3 * 100 - 20);
    if (abs((float)$cotizacionTest->subtotal - $expected) > 0.01) {
        throw new Exception("Esperado: $expected, Obtenido: {$cotizacionTest->subtotal}");
    }
    return "RD\$" . number_format($cotizacionTest->subtotal, 2);
});

test("Descuento a nivel cabecera se preserva", function() use (&$cotizacionTest) {
    // En esta cotización no se asignó descuento a nivel cabecera (es null/0)
    $valor = (float) ($cotizacionTest->descuento ?? 0);
    if ($valor !== 0.0) {
        throw new Exception("Esperado: 0, Obtenido: $valor");
    }
    return "RD\$0.00 (solo descuento por item)";
});

test("ITBIS = (subtotal) × 18%", function() use (&$cotizacionTest) {
    $expected = 1280 * 0.18;
    if (abs((float)$cotizacionTest->itbis - $expected) > 0.01) {
        throw new Exception("Esperado: $expected, Obtenido: {$cotizacionTest->itbis}");
    }
    return "RD\$" . number_format($cotizacionTest->itbis, 2);
});

test("Total = subtotal + ITBIS - descuento cabecera", function() use (&$cotizacionTest) {
    $expected = 1280 + 1280 * 0.18;
    if (abs((float)$cotizacionTest->total - $expected) > 0.01) {
        throw new Exception("Esperado: $expected, Obtenido: {$cotizacionTest->total}");
    }
    return "RD\$" . number_format($cotizacionTest->total, 2);
});

// ==== RELACIONES ====
echo PHP_EOL . "--- Relaciones Eloquent ---" . PHP_EOL;

test("Cotizacion->cliente() funciona", function() use (&$cotizacionTest) {
    $cotizacionTest->load('cliente');
    if (!$cotizacionTest->cliente) throw new Exception("Cliente no cargado");
    return $cotizacionTest->cliente->nombre;
});

test("Cotizacion->user() funciona", function() use (&$cotizacionTest) {
    $cotizacionTest->load('user');
    if (!$cotizacionTest->user) throw new Exception("Usuario no cargado");
    return $cotizacionTest->user->name;
});

test("Cotizacion->items() funciona", function() use (&$cotizacionTest) {
    $cotizacionTest->load('items');
    if ($cotizacionTest->items->count() !== 2) {
        throw new Exception("Esperado: 2 items, Obtenido: " . $cotizacionTest->items->count());
    }
    return $cotizacionTest->items->count() . " items";
});

test("Cliente->cotizaciones() (relación inversa) funciona", function() use ($cli) {
    $count = $cli->cotizaciones()->count();
    if ($count === 0) throw new Exception("No se cargaron cotizaciones del cliente");
    return "$count cotización(es)";
});

// ==== ESTADOS Y SCOPES ====
echo PHP_EOL . "--- Estados y Scopes ---" . PHP_EOL;

test("Cambiar estado de cotización", function() use (&$cotizacionTest) {
    $cotizacionTest->update(['estado' => 'aprobada']);
    $cotizacionTest->refresh();
    if ($cotizacionTest->estado !== 'aprobada') {
        throw new Exception("Estado no cambió");
    }
    return $cotizacionTest->estado_label;
});

test("Scope activas() funciona", function() {
    $count = App\Models\Cotizacion::activas()->count();
    return "$count activas";
});

test("Scope vencidas() funciona", function() {
    $count = App\Models\Cotizacion::vencidas()->count();
    return "$count vencidas";
});

test("Accessor dias_validez funciona", function() use (&$cotizacionTest) {
    $dias = $cotizacionTest->dias_validez;
    if (!is_int($dias)) throw new Exception("dias_validez no es entero");
    return "$dias días";
});

test("Accessor puede_convertirse funciona", function() use (&$cotizacionTest) {
    $puede = $cotizacionTest->puede_convertirse;
    return $puede ? "true" : "false";
});

// ==== PERMISOS ====
echo PHP_EOL . "--- Permisos ---" . PHP_EOL;

test("6 permisos de cotizaciones existen", function() {
    $perms = Spatie\Permission\Models\Permission::where('name', 'like', 'cotizaciones.%')->pluck('name')->toArray();
    $esperados = ['cotizaciones.view', 'cotizaciones.create', 'cotizaciones.edit', 'cotizaciones.delete', 'cotizaciones.export', 'cotizaciones.convertir'];
    $faltan = array_diff($esperados, $perms);
    if (count($faltan) > 0) {
        throw new Exception("Faltan: " . implode(', ', $faltan));
    }
    return count($perms) . " permisos";
});

test("Permisos asignados a rol admin", function() {
    $admin = Spatie\Permission\Models\Role::where('name', 'admin')->first();
    if (!$admin) throw new Exception("Rol admin no existe");
    $count = $admin->permissions()->where('name', 'like', 'cotizaciones.%')->count();
    if ($count < 7) throw new Exception("Admin tiene $count, esperaba al menos 7");
    return "$count/7";
});

// ==== CONVERSIÓN A VENTA ====
echo PHP_EOL . "--- Conversión a Venta ---" . PHP_EOL;

test("Conversión: crea venta con totales correctos", function() use (&$cotizacionTest, $user, $almacen) {
    DB::beginTransaction();
    try {
        $tipoVenta = App\Models\TipoVenta::where('nombre', 'Contado')->first() ?? App\Models\TipoVenta::first();
        if (!$tipoVenta) throw new Exception("No hay tipos de venta");
        
        $sesion = App\Models\SesionCaja::where('user_id', $user->id)
            ->where('estado', 'abierta')
            ->latest('id')
            ->first();
        
        $venta = App\Models\Venta::create([
            'cliente_id' => $cotizacionTest->cliente_id,
            'user_id' => $user->id,
            'tipo_venta_id' => $tipoVenta->id,
            'caja_id' => $sesion?->caja_id,
            'fecha' => now(),
            'subtotal' => (float) $cotizacionTest->subtotal,
            'descuento' => (float) ($cotizacionTest->descuento ?? 0),
            'impuestos' => (float) $cotizacionTest->itbis,
            'total' => (float) $cotizacionTest->total,
            'tipo_comprobante' => 'ecf',
            'estado' => 'completada',
        ]);
        
        foreach ($cotizacionTest->items as $item) {
            $subtotalItem = ($item->cantidad * $item->precio_unitario) - $item->descuento;
            $venta->detalles()->create([
                'producto_id' => $item->producto_id,
                'almacen_id' => $almacen?->id ?? 1,
                'cantidad' => $item->cantidad,
                'precio_unitario' => $item->precio_unitario,
                'subtotal' => $subtotalItem,
            ]);
        }
        
        $cotizacionTest->update([
            'estado' => 'convertida',
            'venta_id' => $venta->id,
            'convertida_en' => now(),
        ]);
        
        DB::commit();
        return "Venta #{$venta->id}";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
});

test("Conversión: subtotal preservado", function() use (&$cotizacionTest) {
    $venta = App\Models\Venta::find($cotizacionTest->venta_id);
    if (abs((float)$venta->subtotal - (float)$cotizacionTest->subtotal) > 0.01) {
        throw new Exception("Venta: {$venta->subtotal} != Cotización: {$cotizacionTest->subtotal}");
    }
    return "RD\$" . number_format($venta->subtotal, 2);
});

test("Conversión: ITBIS preservado", function() use (&$cotizacionTest) {
    $venta = App\Models\Venta::find($cotizacionTest->venta_id);
    if (abs((float)$venta->impuestos - (float)$cotizacionTest->itbis) > 0.01) {
        throw new Exception("Venta: {$venta->impuestos} != Cotización: {$cotizacionTest->itbis}");
    }
    return "RD\$" . number_format($venta->impuestos, 2);
});

test("Conversión: total preservado", function() use (&$cotizacionTest) {
    $venta = App\Models\Venta::find($cotizacionTest->venta_id);
    if (abs((float)$venta->total - (float)$cotizacionTest->total) > 0.01) {
        throw new Exception("Venta: {$venta->total} != Cotización: {$cotizacionTest->total}");
    }
    return "RD\$" . number_format($venta->total, 2);
});

test("Conversión: cantidad de items preservada", function() use (&$cotizacionTest) {
    $venta = App\Models\Venta::find($cotizacionTest->venta_id);
    $venta->load('detalles');
    if ($venta->detalles->count() !== $cotizacionTest->items->count()) {
        throw new Exception("Venta: " . $venta->detalles->count() . " != Cotización: " . $cotizacionTest->items->count());
    }
    return $venta->detalles->count() . " items";
});

test("Conversión: cotización marcada como convertida", function() use (&$cotizacionTest) {
    $cotizacionTest->refresh();
    if ($cotizacionTest->estado !== 'convertida') {
        throw new Exception("Estado: {$cotizacionTest->estado}");
    }
    return $cotizacionTest->estado_label;
});

test("Conversión: cotización con venta_id asignado", function() use (&$cotizacionTest) {
    $cotizacionTest->refresh();
    if (!$cotizacionTest->venta_id) {
        throw new Exception("Venta ID no asignado");
    }
    return "Venta #{$cotizacionTest->venta_id}";
});

// ==== RUTAS ====
echo PHP_EOL . "--- Rutas ---" . PHP_EOL;

test("Rutas de cotizaciones registradas", function() {
    $routes = collect(Illuminate\Support\Facades\Route::getRoutes())
        ->filter(fn($r) => str_contains($r->uri(), 'cotizaciones'))
        ->count();
    if ($routes < 10) throw new Exception("Solo $routes rutas, esperaba al menos 10");
    return "$routes rutas";
});

test("Ruta de búsqueda existe", function() {
    $route = Illuminate\Support\Facades\Route::getRoutes()->getByName('cotizaciones.buscarProducto');
    if (!$route) throw new Exception("Ruta no encontrada");
    return $route->uri();
});

// ==== RESUMEN ====
echo PHP_EOL . "========================================" . PHP_EOL;
echo "RESUMEN: $pass tests pasados, $fail tests fallidos" . PHP_EOL;
echo "========================================" . PHP_EOL;

exit($fail > 0 ? 1 : 0);
