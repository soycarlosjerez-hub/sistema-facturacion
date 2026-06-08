<?php
/**
 * E2E Test - Módulo Conduces
 * Verifica CRUD, generación de número, cambios de estado, entrega, tickets, PDF
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Conduce;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Support\Sidebar;
use Illuminate\Support\Facades\Auth;

$results = [];
$failures = 0;

function test(string $name, callable $fn) {
    global $results, $failures;
    try {
        $r = $fn();
        $results[] = ['pass' => true, 'name' => $name, 'return' => $r];
        echo "  [OK]   $name\n";
    } catch (\Throwable $e) {
        $results[] = ['pass' => false, 'name' => $name, 'error' => $e->getMessage()];
        $failures++;
        echo "  [FAIL] $name: " . $e->getMessage() . "\n";
    }
}

echo "\n=== E2E TEST: MÓDULO CONDUCES ===\n\n";

// Setup
$admin = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();
if (!$admin) {
    echo "FAIL: no admin user found. Run seeder first.\n";
    exit(1);
}
Auth::login($admin);

$cliente = Cliente::first();
$producto = Producto::first();
if (!$cliente || !$producto) {
    echo "FAIL: need at least 1 cliente and 1 producto.\n";
    exit(1);
}

// Pre-clean: eliminar conduces previos de test
Conduce::where('numero', 'like', 'COND-%')->forceDelete();

$conduceTest = null;

test('Migraciones: tabla conduces existe', function () {
    if (!\Schema::hasTable('conduces')) throw new \Exception('Tabla conduces no existe');
    if (!\Schema::hasTable('conduce_items')) throw new \Exception('Tabla conduce_items no existe');
});

test('Migraciones: columna deleted_at (soft delete) en conduces', function () {
    if (!\Schema::hasColumn('conduces', 'deleted_at')) throw new \Exception('Falta deleted_at en conduces');
});

test('Migraciones: columnas requeridas en conduces', function () {
    $cols = ['numero', 'fecha', 'cliente_id', 'user_id', 'estado', 'direccion_entrega', 'total_items'];
    foreach ($cols as $c) {
        if (!\Schema::hasColumn('conduces', $c)) throw new \Exception("Falta columna $c");
    }
});

test('Migraciones: columnas en conduce_items', function () {
    $cols = ['conduce_id', 'producto_id', 'nombre', 'cantidad', 'unidad', 'cantidad_recibida', 'peso', 'orden'];
    foreach ($cols as $c) {
        if (!\Schema::hasColumn('conduce_items', $c)) throw new \Exception("Falta columna $c");
    }
});

test('Migraciones: producto_id es nullable en conduce_items', function () {
    $rows = DB::select("SHOW COLUMNS FROM conduce_items WHERE Field = 'producto_id'");
    if (empty($rows)) throw new \Exception('Columna no encontrada');
    $col = $rows[0];
    if (strtoupper($col->Null) !== 'YES') {
        throw new \Exception("producto_id debe ser NULL permitido, es: {$col->Null}");
    }
});

test('Modelo: constante ESTADOS tiene los 5 estados', function () {
    $esperados = ['borrador', 'en_transito', 'entregado', 'devuelto', 'cancelado'];
    foreach ($esperados as $e) {
        if (!isset(Conduce::ESTADOS[$e])) throw new \Exception("Falta estado $e");
    }
});

test('Modelo: generarNumero() devuelve formato COND-YYYY-NNNNNN', function () {
    $numero = Conduce::generarNumero();
    if (!preg_match('/^COND-\d{4}-\d{6}$/', $numero)) {
        throw new \Exception("Formato incorrecto: $numero");
    }
});

test('Modelo: scopes accesibles', function () {
    $metodos = ['scopeActivos', 'scopePendientes', 'scopeEntregados', 'scopeVencidos', 'scopePorCliente', 'scopePorFecha', 'scopeBuscar'];
    foreach ($metodos as $m) {
        if (!method_exists(Conduce::class, $m)) throw new \Exception("Falta $m");
    }
});

test('Modelo: accessors de estado', function () {
    $c = new Conduce(['estado' => 'borrador']);
    if ($c->estado_label !== 'Borrador') throw new \Exception('estado_label mal: ' . $c->estado_label);
    if ($c->estado_color !== 'secondary') throw new \Exception('estado_color mal: ' . $c->estado_color);
    if ($c->estado_icon !== 'pencil-square') throw new \Exception('estado_icon mal: ' . $c->estado_icon);
});

test('Modelo: accessors booleanos (puede_entregarse, esta_vencido)', function () {
    $c = new Conduce(['estado' => 'en_transito']);
    if ($c->puede_entregarse !== true) throw new \Exception('puede_entregarse debe ser true en tránsito');

    $c2 = new Conduce(['estado' => 'entregado']);
    if ($c2->puede_entregarse !== false) throw new \Exception('puede_entregarse debe ser false cuando ya entregado');
});

test('CRUD: crear conduce con items', function () use ($admin, $cliente, $producto, &$conduceTest) {
    $conduce = Conduce::create([
        'numero' => Conduce::generarNumero(),
        'fecha' => now(),
        'cliente_id' => $cliente->id,
        'user_id' => $admin->id,
        'direccion_entrega' => 'Calle Test #45',
        'estado' => 'borrador',
    ]);

    $conduce->items()->createMany([
        [
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'codigo' => $producto->codigo ?? 'TEST-001',
            'cantidad' => 5,
            'unidad' => $producto->unidad_medida ?? 'UND',
            'peso' => 1.5,
            'orden' => 0,
        ],
        [
            'producto_id' => null,
            'nombre' => 'Item manual sin producto',
            'codigo' => 'MANUAL-001',
            'cantidad' => 10,
            'unidad' => 'UND',
            'peso' => 0,
            'orden' => 1,
        ],
    ]);

    $conduce->calcularTotales();
    $conduce->refresh();

    if ($conduce->items->count() !== 2) throw new \Exception('Items no guardados');
    if ($conduce->total_items !== 15) throw new \Exception("total_items={$conduce->total_items}, esperado 15");
    if ((float) $conduce->peso_total !== 1.5) throw new \Exception("peso_total={$conduce->peso_total}");

    $conduceTest = $conduce;
});

test('CRUD: leer conduce con relaciones', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $c = Conduce::with(['cliente', 'user', 'items'])->find($conduceTest->id);
    if (!$c->cliente) throw new \Exception('Falta relación cliente');
    if (!$c->user) throw new \Exception('Falta relación user');
    if ($c->items->count() !== 2) throw new \Exception('Items no cargados');
});

test('CRUD: actualizar conduce', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $conduceTest->update(['observaciones' => 'Test update ' . now()]);
    $fresh = Conduce::find($conduceTest->id);
    if (!str_contains($fresh->observaciones, 'Test update')) throw new \Exception('Update no persistió');
});

test('Estados: cambiar borrador → en_transito', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $conduceTest->cambiarEstado('en_transito');
    $fresh = Conduce::find($conduceTest->id);
    if ($fresh->estado !== 'en_transito') throw new \Exception("Estado no cambió: {$fresh->estado}");
});

test('Estados: cambiar en_transito → devuelto → en_transito', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $conduceTest->cambiarEstado('devuelto');
    $fresh = Conduce::find($conduceTest->id);
    if ($fresh->estado !== 'devuelto') throw new \Exception("Estado no cambió: {$fresh->estado}");
    $conduceTest->cambiarEstado('en_transito');
});

test('Entrega: marcar como entregado con cantidades', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $itemsRecibidos = [];
    foreach ($conduceTest->items as $item) {
        $itemsRecibidos[$item->id] = (float) $item->cantidad;
    }
    $ok = $conduceTest->marcarEntregado('Receptor Test', '001-1234567-8', $itemsRecibidos);
    if (!$ok) throw new \Exception('marcarEntregado devolvió false');
    $fresh = Conduce::with('items')->find($conduceTest->id);
    if ($fresh->estado !== 'entregado') throw new \Exception("Estado no es entregado: {$fresh->estado}");
    if ($fresh->recibido_por !== 'Receptor Test') throw new \Exception('Recibido por no guardado');
    if (!$fresh->fecha_recibido) throw new \Exception('fecha_recibido no guardada');
    foreach ($fresh->items as $item) {
        if ((float) $item->cantidad_recibida !== (float) $item->cantidad) {
            throw new \Exception("cantidad_recibida inconsistente en item {$item->id}: rec={$item->cantidad_recibida}, env={$item->cantidad}");
        }
    }
});

test('Items: accessors de ConduceItem', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $item = $conduceTest->items()->first();
    if ($item->entregado_completo !== true) throw new \Exception('entregado_completo debe ser true');
    if ($item->porcentaje_entregado != 100) throw new \Exception("porcentaje_entregado={$item->porcentaje_entregado}");
});

test('Rutas: 13 rutas de conduces registradas', function () {
    $expected = [
        'conduces.index', 'conduces.create', 'conduces.store',
        'conduces.show', 'conduces.edit', 'conduces.update', 'conduces.destroy',
        'conduces.fromVenta', 'conduces.cambiarEstado', 'conduces.entregar',
        'conduces.pdf', 'conduces.ticket', 'conduces.ticketText',
    ];
    $names = [];
    foreach (\Route::getRoutes()->getRoutes() as $r) {
        $names[] = $r->getName();
    }
    foreach ($expected as $e) {
        if (!in_array($e, $names, true)) throw new \Exception("Falta ruta $e");
    }
});

test('Rutas: constraints where([0-9]+) en parámetros numéricos', function () {
    $routes = \Route::getRoutes()->getRoutes();
    $found = false;
    foreach ($routes as $r) {
        if (str_contains($r->uri(), 'conduces/{conduce}/ticket') && $r->getName() === 'conduces.ticket') {
            $found = true;
            if (!$r->wheres) throw new \Exception('Sin constraints where');
            break;
        }
    }
    if (!$found) throw new \Exception('Ruta conduces.ticket no encontrada');
});

test('Vistas: archivos existen', function () {
    $vistas = ['conduces.index', 'conduces.create', 'conduces.edit', 'conduces.show', 'conduces.ticket', 'conduces.pdf', 'conduces._form'];
    foreach ($vistas as $v) {
        if (!view()->exists($v)) throw new \Exception("Falta vista $v");
    }
});

test('Vistas: ticket usa 80/58mm', function () {
    $c = Conduce::with('cliente', 'items')->where('estado', 'entregado')->first();
    if (!$c) throw new \Exception('No hay conduce entregado');

    $html80 = view('conduces.ticket', ['conduce' => $c, 'paper' => 80, 'empresa' => (object) []])->render();
    if (!str_contains($html80, '80mm')) throw new \Exception('No renderiza para 80mm');
    if (!str_contains($html80, $c->numero)) throw new \Exception('No muestra número');

    $html58 = view('conduces.ticket', ['conduce' => $c, 'paper' => 58, 'empresa' => (object) []])->render();
    if (!str_contains($html58, '58mm')) throw new \Exception('No renderiza para 58mm');
});

test('Vistas: PDF renderiza', function () {
    $c = Conduce::with('cliente', 'items', 'user')->where('estado', 'entregado')->first();
    if (!$c) throw new \Exception('No hay conduce entregado');

    $html = view('conduces.pdf', ['conduce' => $c, 'empresa' => (object) []])->render();
    if (!str_contains($html, 'CONDUCE')) throw new \Exception('PDF no tiene header');
    if (!str_contains($html, $c->numero)) throw new \Exception('PDF no muestra número');
    if (!str_contains($html, 'Despachado por')) throw new \Exception('PDF sin firma de despachado');
    if (!str_contains($html, 'Recibido por')) throw new \Exception('PDF sin firma de receptor');
});

test('Controller: ConduceController tiene 13 métodos públicos', function () {
    $ref = new ReflectionClass('App\\Http\\Controllers\\ConduceController');
    $metodos = array_filter(
        $ref->getMethods(ReflectionMethod::IS_PUBLIC),
        fn($m) => !$m->isConstructor() && $m->class === $ref->getName()
    );
    $esperados = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
                  'cambiarEstado', 'entregar', 'pdf', 'ticket', 'ticketText', 'fromVenta'];
    $nombres = array_map(fn($m) => $m->getName(), $metodos);
    foreach ($esperados as $m) {
        if (!in_array($m, $nombres)) throw new \Exception("Falta método $m. Disponibles: " . implode(',', $nombres));
    }
});

test('Permisos: 6 permisos de conduces creados', function () {
    $perms = ['conduces.view', 'conduces.create', 'conduces.edit', 'conduces.delete', 'conduces.print', 'conduces.deliver'];
    foreach ($perms as $p) {
        if (!\Spatie\Permission\Models\Permission::where('name', $p)->exists()) {
            throw new \Exception("Permiso $p no existe");
        }
    }
});

test('Permisos: admin tiene todos los conduces.*', function () {
    $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
    $conducesPerms = \Spatie\Permission\Models\Permission::where('name', 'like', 'conduces.%')->pluck('name');
    foreach ($conducesPerms as $p) {
        if (!$adminRole->hasPermissionTo($p)) throw new \Exception("Admin no tiene $p");
    }
});

test('Permisos: vendedor tiene view, create, edit, print, deliver', function () {
    $vendedor = \Spatie\Permission\Models\Role::where('name', 'vendedor')->first();
    $esperados = ['conduces.view', 'conduces.create', 'conduces.edit', 'conduces.print', 'conduces.deliver'];
    foreach ($esperados as $p) {
        if (!$vendedor->hasPermissionTo($p)) throw new \Exception("Vendedor no tiene $p");
    }
    if ($vendedor->hasPermissionTo('conduces.delete')) {
        throw new \Exception('Vendedor NO debería tener conduces.delete');
    }
});

test('Sidebar: aparece item Conduces', function () {
    $menu = Sidebar::menu();
    $flat = [];
    foreach ($menu as $item) {
        if (isset($item['route'])) $flat[] = $item['route'];
    }
    if (!in_array('conduces.index', $flat)) {
        throw new \Exception('Sidebar no incluye conduces.index. Encontrados: ' . implode(',', $flat));
    }
});

test('SoftDelete: eliminar y restaurar', function () use (&$conduceTest) {
    if (!$conduceTest) throw new \Exception('No hay conduce');
    $id = $conduceTest->id;
    $conduceTest->delete();
    if (Conduce::find($id)) throw new \Exception('Soft delete no funcionó');
    if (!Conduce::onlyTrashed()->find($id)) throw new \Exception('onlyTrashed no lo encuentra');

    Conduce::onlyTrashed()->find($id)->restore();
    $restored = Conduce::find($id);
    if (!$restored) throw new \Exception('Restore no funcionó');
    if ($restored->estado !== 'entregado') throw new \Exception("Estado perdido: {$restored->estado}");
});

test('Snapshot: items preservan datos del producto al crear', function () {
    $c = Conduce::with('items')->whereHas('items', fn($q) => $q->whereNotNull('producto_id'))->first();
    if (!$c) throw new \Exception('Sin items con producto');
    $item = $c->items->first();
    if (!$item->nombre) throw new \Exception('Falta snapshot nombre');
    if (!$item->codigo) throw new \Exception('Falta snapshot codigo');
});

test('Búsqueda: scope buscar() por número/observaciones', function () {
    $c = Conduce::first();
    if (!$c) throw new \Exception('Sin conduces');
    $busquedaNumero = Conduce::buscar($c->numero)->count();
    if ($busquedaNumero < 1) throw new \Exception("Buscar por número no encontró: {$c->numero}");
});

test('Cliente: relación conduces()', function () use ($cliente) {
    if (!method_exists($cliente, 'conduces')) {
        throw new \Exception('Cliente no tiene método conduces()');
    }
    $count = $cliente->conduces()->count();
    if ($count < 1) throw new \Exception("Cliente no tiene conduces asociados (count={$count})");
});

test('Cleanup: eliminar conduce de prueba', function () use (&$conduceTest) {
    if ($conduceTest && $conduceTest->id) {
        Conduce::withTrashed()->find($conduceTest->id)?->forceDelete();
    }
});

echo "\n=== RESUMEN ===\n";
$total = count($results);
$pass = count(array_filter($results, fn($r) => $r['pass']));
echo "Pasados: $pass / $total\n";
echo "Fallos: $failures\n";

if ($failures > 0) {
    echo "\nDETALLES DE FALLOS:\n";
    foreach ($results as $r) {
        if (!$r['pass']) {
            echo "  - {$r['name']}: {$r['error']}\n";
        }
    }
    exit(1);
}

echo "\n[SUCCESS] Todos los tests pasaron.\n";
exit(0);
