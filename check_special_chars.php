<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DATABASE: " . DB::connection()->getDatabaseName() . " ===" . PHP_EOL . PHP_EOL;

// Check CLIENTES
echo "--- CLIENTES CON CARACTERES ESPECIALES ---" . PHP_EOL;
$clientes = DB::table('clientes')->get();
foreach ($clientes as $c) {
    $specialChars = [];
    if (strpos($c->nombre, "'") !== false) $specialChars[] = "single quote (')";
    if (strpos($c->nombre, '"') !== false) $specialChars[] = "double quote (\")";
    if (strpos($c->nombre, "\n") !== false) $specialChars[] = "newline";
    if (strpos($c->nombre, "\r") !== false) $specialChars[] = "carriage return";
    if (strpos($c->nombre, '\\') !== false) $specialChars[] = "backslash (\)";
    if (strpos($c->nombre, '<') !== false) $specialChars[] = "angle bracket (<)";
    if (strpos($c->nombre, '>') !== false) $specialChars[] = "angle bracket (>)";
    
    if (!empty($specialChars)) {
        echo "  Cliente ID {$c->id}: '{$c->nombre}' [CARACTERES: " . implode(', ', $specialChars) . "]" . PHP_EOL;
    }
}

// Check CLIENTES - all (showing RUC and nombre for context)
echo PHP_EOL . "--- TODOS LOS CLIENTES ---" . PHP_EOL;
foreach ($clientes as $c) {
    echo "  ID {$c->id}: RUC='{$c->ruc}' | Nombre='{$c->nombre}'" . PHP_EOL;
}

// Check PRODUCTOS
echo PHP_EOL . "--- PRODUCTOS CON CARACTERES ESPECIALES ---" . PHP_EOL;
$productos = DB::table('productos')->get();
foreach ($productos as $p) {
    $specialChars = [];
    if (strpos($p->nombre, "'") !== false) $specialChars[] = "single quote (')";
    if (strpos($p->nombre, '"') !== false) $specialChars[] = "double quote (\")";
    if (strpos($p->nombre, "\n") !== false) $specialChars[] = "newline";
    if (strpos($p->nombre, "\r") !== false) $specialChars[] = "carriage return";
    if (strpos($p->nombre, '\\') !== false) $specialChars[] = "backslash (\)";
    
    if (!empty($specialChars)) {
        echo "  Producto ID {$p->id}: '{$p->nombre}' [CARACTERES: " . implode(', ', $specialChars) . "]" . PHP_EOL;
    }
}

echo PHP_EOL . "--- TODOS LOS PRODUCTOS ---" . PHP_EOL;
foreach ($productos as $p) {
    echo "  ID {$p->id}: Nombre='{$p->nombre}' | Almacen ID={$p->almacen_id}" . PHP_EOL;
}

// Check ALMACENES
echo PHP_EOL . "--- ALMACENES CON CARACTERES ESPECIALES ---" . PHP_EOL;
$almacenes = DB::table('almacenes')->get();
foreach ($almacenes as $a) {
    $specialChars = [];
    if (strpos($a->nombre, "'") !== false) $specialChars[] = "single quote (')";
    if (strpos($a->nombre, '"') !== false) $specialChars[] = "double quote (\")";
    if (strpos($a->nombre, "\n") !== false) $specialChars[] = "newline";
    if (strpos($a->nombre, "\r") !== false) $specialChars[] = "carriage return";
    if (strpos($a->nombre, '\\') !== false) $specialChars[] = "backslash (\)";
    
    if (!empty($specialChars)) {
        echo "  Almacen ID {$a->id}: '{$a->nombre}' [CARACTERES: " . implode(', ', $specialChars) . "]" . PHP_EOL;
    }
}

echo PHP_EOL . "--- TODOS LOS ALMACENES ---" . PHP_EOL;
foreach ($almacenes as $a) {
    echo "  ID {$a->id}: Nombre='{$a->nombre}'" . PHP_EOL;
}

// Check VENTAS table for any stored issues
echo PHP_EOL . "--- RESUMEN CANTIDADES ---" . PHP_EOL;
echo "Total clientes: " . $clientes->count() . PHP_EOL;
echo "Total productos: " . $productos->count() . PHP_EOL;
echo "Total almacenes: " . $almacenes->count() . PHP_EOL;

echo PHP_EOL . "=== DONE ===" . PHP_EOL;
