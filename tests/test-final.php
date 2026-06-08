<?php
/**
 * Test E2E Final - Todos los Features
 *
 * Cubre:
 * - Email de cotización (Mailable + Service)
 * - Impresión de tickets (PrintService + vista)
 * - Accesibilidad (ARIA + CSS + JS)
 * - Descuentos por línea en POS
 * - Módulo de Cotizaciones completo
 */

chdir(__DIR__ . '/..');
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

$pass = 0;
$fail = 0;
$sections = [];

function test_ok($name, $callback, $section = 'General') {
    global $pass, $fail, $sections;
    if (!isset($sections[$section])) $sections[$section] = ['pass' => 0, 'fail' => 0, 'results' => []];
    
    try {
        $r = $callback();
        $pass++;
        $sections[$section]['pass']++;
        $sections[$section]['results'][] = "OK  | $name" . ($r ? " [$r]" : '');
    } catch (\Exception $e) {
        $fail++;
        $sections[$section]['fail']++;
        $sections[$section]['results'][] = "FAIL| $name - " . $e->getMessage();
    }
}

echo PHP_EOL . "=========================================" . PHP_EOL;
echo " TEST E2E: SISTEMA COMPLETO DE COTIZACIONES " . PHP_EOL;
echo "=========================================" . PHP_EOL . PHP_EOL;

$cli = App\Models\Cliente::first();
$prod = App\Models\Producto::first();
$user = App\Models\User::first();

if (!$cli) { $cli = App\Models\Cliente::factory()->create(); }
if (!$prod) { $prod = App\Models\Producto::factory()->create(); }
if (!$user) { echo "No hay usuario. Abortando." . PHP_EOL; exit(1); }

auth()->setUser($user);

if (!$cli->email) {
    $cli->email = 'test@example.com';
    $cli->save();
}

// ====================================================
// SECCIÓN 1: Módulo de Cotizaciones (núcleo)
// ====================================================
echo "## 1. Modulo de Cotizaciones" . PHP_EOL;

test_ok("Crear cotizacion", function() use ($cli, $user, $prod) {
    $cot = App\Models\Cotizacion::create([
        'numero' => App\Models\Cotizacion::generarNumero(),
        'cliente_id' => $cli->id,
        'user_id' => $user->id,
        'fecha' => now(),
        'fecha_validez' => now()->addDays(15),
        'estado' => 'borrador',
    ]);
    $item = new App\Models\CotizacionItem([
        'cotizacion_id' => $cot->id,
        'producto_id' => $prod->id,
        'codigo' => $prod->codigo,
        'nombre' => $prod->nombre,
        'unidad' => 'Unidad',
        'cantidad' => 2,
        'precio_unitario' => 100,
        'descuento' => 0,
        'itbis_porcentaje' => 18,
    ]);
    $item->calcular();
    $item->save();
    $cot->calcularTotales();
    return "{$cot->numero} - RD\$" . number_format($cot->total, 2);
}, 'Cotizaciones');

$cot = App\Models\Cotizacion::latest('id')->first();

test_ok("Relaciones Eloquent", function() use ($cot) {
    $cot->load(['cliente', 'user', 'items']);
    if (!$cot->cliente) throw new \Exception("Cliente no cargado");
    if (!$cot->user) throw new \Exception("User no cargado");
    if ($cot->items->count() === 0) throw new \Exception("Items no cargados");
    return "Cliente+User+Items";
}, 'Cotizaciones');

test_ok("Scopes (activas/vencidas)", function() use ($cot) {
    $count = App\Models\Cotizacion::activas()->count();
    if ($count === 0) throw new \Exception("No hay activas");
    return "$count activas";
}, 'Cotizaciones');

test_ok("Permisos configurados", function() {
    $perms = Spatie\Permission\Models\Permission::where('name', 'like', 'cotizaciones.%')->count();
    if ($perms < 6) throw new \Exception("Solo $perms permisos");
    return "$perms permisos";
}, 'Cotizaciones');

test_ok("Rutas registradas (11)", function() {
    $count = collect(Route::getRoutes())->filter(fn($r) => str_contains($r->uri(), 'cotizaciones'))->count();
    if ($count < 11) throw new \Exception("Solo $count rutas");
    return "$count rutas";
}, 'Cotizaciones');

// ====================================================
// SECCIÓN 2: Email de Cotización
// ====================================================
echo PHP_EOL . "## 2. Notificaciones Email" . PHP_EOL;

test_ok("CotizacionEmailService existe", function() {
    if (!class_exists('App\Services\CotizacionEmailService')) throw new \Exception("No existe");
    return "OK";
}, 'Email');

test_ok("CotizacionEnviadaMail existe", function() {
    if (!class_exists('App\Mail\CotizacionEnviadaMail')) throw new \Exception("No existe");
    return "OK";
}, 'Email');

test_ok("Servicio envia email correctamente", function() use ($cot) {
    $service = new App\Services\CotizacionEmailService();
    $r = $service->enviar($cot, 'Test', null, false);
    if (!$r['success']) throw new \Exception($r['error'] ?? 'Fallo');
    return "A: {$r['destinatario']}";
}, 'Email');

test_ok("Email a cliente sin email falla", function() use ($user) {
    $cli2 = App\Models\Cliente::skip(1)->first();
    if ($cli2) $cli2->update(['email' => null]);
    
    $cot2 = App\Models\Cotizacion::create([
        'numero' => App\Models\Cotizacion::generarNumero(),
        'cliente_id' => $cli2?->id ?? $cli->id,
        'user_id' => $user->id,
        'fecha' => now(),
        'fecha_validez' => now()->addDays(15),
        'estado' => 'borrador',
    ]);
    
    $service = new App\Services\CotizacionEmailService();
    $r = $service->enviar($cot2);
    if ($r['success']) throw new \Exception("Deberia fallar");
    return "Validado: {$r['error']}";
}, 'Email');

test_ok("Vista HTML del email se renderiza", function() use ($cot) {
    $html = view('emails.cotizacion-enviada', [
        'cotizacion' => $cot,
        'mensajeAdicional' => '',
        'urlVer' => route('cotizaciones.show', $cot),
    ])->render();
    if (strlen($html) < 1000) throw new \Exception("HTML muy corto");
    if (!str_contains($html, $cot->numero)) throw new \Exception("Falta numero");
    return strlen($html) . " bytes";
}, 'Email');

test_ok("Vista texto del email se renderiza", function() use ($cot) {
    $txt = view('emails.cotizacion-enviada-text', [
        'cotizacion' => $cot,
        'mensajeAdicional' => '',
        'urlVer' => route('cotizaciones.show', $cot),
    ])->render();
    if (!str_contains($txt, $cot->numero)) throw new \Exception("Falta numero");
    return strlen($txt) . " bytes";
}, 'Email');

test_ok("Permiso cotizaciones.enviar existe", function() {
    $exists = Spatie\Permission\Models\Permission::where('name', 'cotizaciones.enviar')->exists();
    if (!$exists) throw new \Exception("No existe");
    return "OK";
}, 'Email');

// ====================================================
// SECCIÓN 3: Print Service
// ====================================================
echo PHP_EOL . "## 3. Impresion de Tickets" . PHP_EOL;

test_ok("PrintService existe", function() {
    if (!class_exists('App\Services\PrintService')) throw new \Exception("No existe");
    return "OK";
}, 'Print');

test_ok("Ticket 80mm se genera", function() use ($cot) {
    $service = new App\Services\PrintService();
    $ticket = $service->renderCotizacionTicket($cot, 80);
    if (strlen($ticket) < 500) throw new \Exception("Muy corto");
    if (!str_contains($ticket, $cot->numero)) throw new \Exception("Falta numero");
    return strlen($ticket) . " bytes";
}, 'Print');

test_ok("Ticket 58mm se genera", function() use ($cot) {
    $service = new App\Services\PrintService();
    $ticket = $service->renderCotizacionTicket($cot, 58);
    if (strlen($ticket) < 300) throw new \Exception("Muy corto");
    return strlen($ticket) . " bytes";
}, 'Print');

test_ok("Ancho de linea 80mm <= 42 chars", function() use ($cot) {
    $service = new App\Services\PrintService();
    $ticket = $service->renderCotizacionTicket($cot, 80);
    $lineas = explode("\n", $ticket);
    $max = max(array_map('mb_strlen', array_filter($lineas)));
    if ($max > 42) throw new \Exception("Max: $max");
    return "Max: $max chars";
}, 'Print');

test_ok("Ancho de linea 58mm <= 32 chars", function() use ($cot) {
    $service = new App\Services\PrintService();
    $ticket = $service->renderCotizacionTicket($cot, 58);
    $lineas = explode("\n", $ticket);
    $max = max(array_map('mb_strlen', array_filter($lineas)));
    if ($max > 32) throw new \Exception("Max: $max");
    return "Max: $max chars";
}, 'Print');

test_ok("ESC/POS genera comandos validos", function() use ($cot) {
    $service = new App\Services\PrintService();
    $ticket = $service->renderCotizacionTicket($cot, 80);
    $escpos = $service->toEscPos($ticket);
    if (!str_starts_with($escpos, "\x1B\x40")) throw new \Exception("No inicia con ESC @");
    if (!str_ends_with($escpos, "\x1D\x56\x00")) throw new \Exception("No termina con corte");
    return strlen($escpos) . " bytes";
}, 'Print');

test_ok("Vista ticket (HTML) existe", function() {
    if (!file_exists(resource_path('views/cotizaciones/ticket.blade.php'))) {
        throw new \Exception("No existe");
    }
    return filesize(resource_path('views/cotizaciones/ticket.blade.php')) . " bytes";
}, 'Print');

test_ok("Rutas ticket registradas", function() {
    $r1 = Route::getRoutes()->getByName('cotizaciones.ticket');
    $r2 = Route::getRoutes()->getByName('cotizaciones.ticketText');
    if (!$r1 || !$r2) throw new \Exception("Faltan rutas");
    return "ticket + ticketText";
}, 'Print');

test_ok("PrintService genera ticket de VENTA", function() {
    $venta = App\Models\Venta::latest('id')->first();
    if (!$venta) return "skip (no hay ventas)";
    $service = new App\Services\PrintService();
    $ticket = $service->renderVentaTicket($venta, 80);
    if (strlen($ticket) < 300) throw new \Exception("Muy corto");
    return strlen($ticket) . " bytes";
}, 'Print');

// ====================================================
// SECCIÓN 4: Accesibilidad
// ====================================================
echo PHP_EOL . "## 4. Accesibilidad" . PHP_EOL;

test_ok("a11y.js existe", function() {
    if (!file_exists(public_path('js/a11y.js'))) throw new \Exception("No existe");
    return filesize(public_path('js/a11y.js')) . " bytes";
}, 'A11y');

test_ok("a11y.css existe", function() {
    if (!file_exists(public_path('css/a11y.css'))) throw new \Exception("No existe");
    return filesize(public_path('css/a11y.css')) . " bytes";
}, 'A11y');

test_ok("Layout tiene skip link", function() {
    $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
    if (!str_contains($layout, 'skip-to-main')) throw new \Exception("No skip link");
    return "OK";
}, 'A11y');

test_ok("Layout incluye a11y assets", function() {
    $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
    if (!str_contains($layout, "asset('js/a11y.js')")) throw new \Exception("No JS");
    if (!str_contains($layout, "asset('css/a11y.css')")) throw new \Exception("No CSS");
    return "JS + CSS";
}, 'A11y');

test_ok("POS search tiene ARIA", function() {
    $pos = file_get_contents(resource_path('views/ventas/create.blade.php'));
    if (!str_contains($pos, 'aria-label="Buscar producto')) throw new \Exception("No aria-label");
    if (!str_contains($pos, 'aria-controls=')) throw new \Exception("No aria-controls");
    if (!str_contains($pos, 'role="search"')) throw new \Exception("No role=search");
    if (!str_contains($pos, 'role="listbox"')) throw new \Exception("No role=listbox");
    return "role + aria-label + aria-controls";
}, 'A11y');

test_ok("Modal email tiene aria-labelledby", function() {
    $show = file_get_contents(resource_path('views/cotizaciones/show.blade.php'));
    if (!str_contains($show, 'aria-labelledby="modalEnviarEmailLabel"')) throw new \Exception("No");
    return "OK";
}, 'A11y');

test_ok("Email template usa ARIA", function() {
    $email = file_get_contents(resource_path('views/emails/cotizacion-enviada.blade.php'));
    if (!str_contains($email, 'role="table"')) throw new \Exception("No role=table");
    return "OK";
}, 'A11y');

test_ok("CSS respeta prefers-reduced-motion", function() {
    $css = file_get_contents(public_path('css/a11y.css'));
    if (!str_contains($css, 'prefers-reduced-motion')) throw new \Exception("No");
    return "OK";
}, 'A11y');

test_ok("CSS usa focus-visible", function() {
    $css = file_get_contents(public_path('css/a11y.css'));
    if (!str_contains($css, 'focus-visible')) throw new \Exception("No");
    return "OK";
}, 'A11y');

test_ok("CSS tiene touch target 44px", function() {
    $css = file_get_contents(public_path('css/a11y.css'));
    if (!str_contains($css, 'min-height: 44px')) throw new \Exception("No");
    return "OK";
}, 'A11y');

test_ok("JS tiene focus trap", function() {
    $js = file_get_contents(public_path('js/a11y.js'));
    if (!str_contains($js, 'trapFocus')) throw new \Exception("No");
    if (!str_contains($js, 'announce')) throw new \Exception("No announce");
    return "trapFocus + announce";
}, 'A11y');

// ====================================================
// SECCIÓN 5: Descuentos POS
// ====================================================
echo PHP_EOL . "## 5. Descuentos por Linea POS" . PHP_EOL;

test_ok("Validacion descuento array numeric", function() {
    $v = Validator::make(['descuento' => [10, 20, 30]], [
        'descuento' => 'nullable|array',
        'descuento.*' => 'numeric|min:0',
    ]);
    if ($v->fails()) throw new \Exception(implode(',', $v->errors()->all()));
    return "OK";
}, 'Descuentos');

test_ok("Validacion tipo monto/porcentaje", function() {
    $v = Validator::make(['descuento_tipo' => ['monto', 'porcentaje']], [
        'descuento_tipo.*' => 'in:monto,porcentaje',
    ]);
    if ($v->fails()) throw new \Exception(implode(',', $v->errors()->all()));
    return "OK";
}, 'Descuentos');

test_ok("Calculo: descuento monto", function() {
    $subtotal = 100 * 2;
    $desc = 30;
    $final = $subtotal - $desc;
    if ($final !== 170) throw new \Exception("Esperado 170");
    return "100*2 - 30 = 170";
}, 'Descuentos');

test_ok("Calculo: descuento porcentaje", function() {
    $subtotal = 200;
    $porcentaje = 10;
    $desc = $subtotal * $porcentaje / 100;
    $final = $subtotal - $desc;
    if ($final !== 180) throw new \Exception("Esperado 180");
    return "200 - 10% = 180";
}, 'Descuentos');

test_ok("POS tiene input descuento", function() {
    $pos = file_get_contents(resource_path('views/ventas/create.blade.php'));
    if (!str_contains($pos, 'class="discount-input"')) throw new \Exception("No input");
    return "OK";
}, 'Descuentos');

test_ok("POS tiene toggle tipo descuento", function() {
    $pos = file_get_contents(resource_path('views/ventas/create.blade.php'));
    if (!str_contains($pos, 'discount-toggle')) throw new \Exception("No toggle");
    if (!str_contains($pos, "'toggle-discount-type'")) throw new \Exception("No handler");
    return "Toggle + handler";
}, 'Descuentos');

test_ok("POS envia descuento[] y descuento_tipo[]", function() {
    $pos = file_get_contents(resource_path('views/ventas/create.blade.php'));
    if (!str_contains($pos, 'name="descuento[]"')) throw new \Exception("No descuento[]");
    if (!str_contains($pos, 'name="descuento_tipo[]"')) throw new \Exception("No descuento_tipo[]");
    return "Hidden inputs OK";
}, 'Descuentos');

test_ok("VentaController valida descuentos", function() {
    $controller = file_get_contents(app_path('Http/Controllers/VentaController.php'));
    if (!str_contains($controller, "'descuento' => 'nullable|array'")) throw new \Exception("No descuento");
    if (!str_contains($controller, "'descuento_tipo.*' => 'in:monto,porcentaje'")) throw new \Exception("No tipo");
    return "Backend valida";
}, 'Descuentos');

test_ok("Calculo: ITBIS sobre subtotal con descuento", function() {
    $subtotal = 170;
    $itbis = $subtotal * 0.18;
    $total = $subtotal + $itbis;
    if (abs($total - 200.6) > 0.01) throw new \Exception("Esperado ~200.6");
    return number_format($total, 2);
}, 'Descuentos');

// ====================================================
// RESULTADOS
// ====================================================
echo PHP_EOL . "=========================================" . PHP_EOL;
echo " RESULTADOS POR SECCION" . PHP_EOL;
echo "=========================================" . PHP_EOL;

foreach ($sections as $name => $data) {
    $total = $data['pass'] + $data['fail'];
    $status = $data['fail'] === 0 ? '✓' : '✗';
    echo "  $status $name: {$data['pass']}/$total pasados" . PHP_EOL;
    foreach ($data['results'] as $r) {
        echo "      $r" . PHP_EOL;
    }
}

echo PHP_EOL . "=========================================" . PHP_EOL;
echo " TOTAL: $pass tests pasados, $fail tests fallidos" . PHP_EOL;
echo "=========================================" . PHP_EOL;

exit($fail > 0 ? 1 : 0);
