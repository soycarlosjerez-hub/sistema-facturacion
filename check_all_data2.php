<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tablesResult = DB::select('SHOW TABLES');
$keyName = array_keys((array)$tablesResult[0])[0];

echo "=== ALL TABLES WITH COUNTS ===" . PHP_EOL;
foreach ($tablesResult as $t) {
    $name = $t->$keyName;
    $count = DB::table($name)->count();
    echo sprintf("%4d | %s\n", $count, $name);
}

echo PHP_EOL . "=== TABLES WITH 100+ ROWS ===" . PHP_EOL;

// Check tables with many rows for special chars in names
foreach ($tablesResult as $t) {
    $name = $t->$keyName;
    $count = DB::table($name)->count();
    if ($count < 100) continue;
    
    echo PHP_EOL . "Checking: {$name} ({$count} rows)" . PHP_EOL;
    
    // Get column info
    $columns = DB::select("SHOW COLUMNS FROM `{$name}`");
    $nameColumns = [];
    foreach ($columns as $col) {
        if (stripos($col->Field, 'nombre') !== false || stripos($col->Field, 'name') !== false || stripos($col->Field, 'descripcion') !== false || stripos($col->Field, 'titulo') !== false || stripos($col->Field, 'etiqueta') !== false) {
            $nameColumns[] = $col->Field;
        }
    }
    
    if (empty($nameColumns)) continue;
    
    foreach ($nameColumns as $colName) {
        // Check for special chars using SQL LIKE
        try {
            $results = DB::select("SELECT `id`, `{$colName}` as `name_val`, `{$colName}` as raw_name FROM `{$name}` WHERE `{$colName}` LIKE '%\'%' OR `{$colName}` LIKE '%\"%' OR `{$colName}` LIKE '%<script%' OR `{$colName}` LIKE CONCAT('%', CHAR(10)) OR `{$colName}` LIKE CONCAT('%', CHAR(13)) LIMIT 20");
            if (!empty($results)) {
                echo "  Column '{$colName}' has problematic records:" . PHP_EOL;
                foreach ($results as $r) {
                    echo "    ID={$r->id}: " . bin2hex($r->raw_name) . " => '{$r->name_val}'" . PHP_EOL;
                }
            }
        } catch (Exception $e) {
            // skip
        }
    }
}

echo PHP_EOL . "=== DONE ===" . PHP_EOL;
