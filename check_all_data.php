<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::getDatabaseName();
echo "=== DB: {$db} ===" . PHP_EOL . PHP_EOL;

// Show all tables and counts
$tablesResult = DB::select('SHOW TABLES');
$tableName = array_keys((array)$tablesResult[0])[0];

echo "--- TABLE COUNTS ---" . PHP_EOL;
foreach ($tablesResult as $t) {
    $key = array_keys((array)$t)[0];
    $count = DB::table($t->$key)->count();
    if ($count > 0 || in_array($key, ['clientes', 'productos', 'almacenes', 'business_instances'])) {
        echo "  {$key}: {$count}" . PHP_EOL;
    }
}

// Check business_instances
echo PHP_EOL . "--- BUSINESS INSTANCES ---" . PHP_EOL;
$instances = DB::table('business_instances')->get();
foreach ($instances as $inst) {
    $special = [];
    if (strpos($inst->nombre, "'") !== false) $special[] = "single quote";
    if (strpos($inst->nombre, '"') !== false) $special[] = "double quote";
    if (!empty($special)) {
        echo "  ID {$inst->id}: '{$inst->nombre}' [PROBLEMA: " . implode(', ', $special) . "]" . PHP_EOL;
    } else {
        echo "  ID {$inst->id}: '{$inst->nombre}'" . PHP_EOL;
    }
}

// Check clientes (with tenant_id)
echo PHP_EOL . "--- CLIENTES ---" . PHP_EOL;
$clientes = DB::table('clientes')->get();
echo "  Total: " . $clientes->count() . PHP_EOL;
foreach ($clientes as $c) {
    $special = [];
    if (strpos($c->nombre, "'") !== false) $special[] = "single quote (')";
    if (strpos($c->nombre, '"') !== false) $special[] = "double quote (\")";
    if (strpos($c->nombre, "\n") !== false) $special[] = "newline";
    if (strpos($c->nombre, "\r") !== false) $special[] = "carriage return";
    if (strpos($c->nombre, '\\') !== false) $special[] = "backslash";
    if (strpos($c->nombre, '<script') !== false) $special[] = "script tag";
    
    $msg = "  ID {$c->id} tenant={$c->tenant_id}: '{$c->nombre}' RUC='{$c->ruc}'";
    if (!empty($special)) {
        echo $msg . " [PROBLEMA: " . implode(', ', $special) . "]" . PHP_EOL;
    }
}

// Check productos (with tenant_id)
echo PHP_EOL . "--- PRODUCTOS ---" . PHP_EOL;
$productos = DB::table('productos')->get();
echo "  Total: " . $productos->count() . PHP_EOL;
foreach ($productos as $p) {
    $special = [];
    if (strpos($p->nombre, "'") !== false) $special[] = "single quote (')";
    if (strpos($p->nombre, '"') !== false) $special[] = "double quote (\")";
    if (strpos($p->nombre, "\n") !== false) $special[] = "newline";
    if (strpos($p->nombre, "\r") !== false) $special[] = "carriage return";
    if (strpos($p->nombre, '\\') !== false) $special[] = "backslash";
    
    $msg = "  ID {$p->id} tenant={$p->tenant_id}: '{$p->nombre}' almacen={$p->almacen_id}";
    if (!empty($special)) {
        echo $msg . " [PROBLEMA: " . implode(', ', $special) . "]" . PHP_EOL;
    }
}

// Check almacenes (with tenant_id)
echo PHP_EOL . "--- ALMACENES ---" . PHP_EOL;
$almacenes = DB::table('almacenes')->get();
echo "  Total: " . $almacenes->count() . PHP_EOL;
foreach ($almacenes as $a) {
    $special = [];
    if (strpos($a->nombre, "'") !== false) $special[] = "single quote (')";
    if (strpos($a->nombre, '"') !== false) $special[] = "double quote (\")";
    if (strpos($a->nombre, "\n") !== false) $special[] = "newline";
    if (strpos($a->nombre, "\r") !== false) $special[] = "carriage return";
    if (strpos($a->nombre, '\\') !== false) $special[] = "backslash";
    
    $msg = "  ID {$a->id} tenant={$a->tenant_id}: '{$a->nombre}'";
    if (!empty($special)) {
        echo $msg . " [PROBLEMA: " . implode(', ', $special) . "]" . PHP_EOL;
    }
}

// Also check views/materialized views that might be used
echo PHP_EOL . "--- VENTAS COUNT ---" . PHP_EOL;
echo "  ventas: " . DB::table('ventas')->count() . PHP_EOL;
echo "  ventas_detail: " . DB::table('ventas_detail')->count() . PHP_EOL;

echo PHP_EOL . "=== DONE ===" . PHP_EOL;
