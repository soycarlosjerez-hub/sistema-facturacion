<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all tables
$tablesResult = DB::select('SHOW TABLES');
$keyName = array_keys((array)$tablesResult[0])[0];

echo "--- ALL TABLES WITH COUNTS ---" . PHP_EOL;
foreach ($tablesResult as $t) {
    $name = $t->$keyName;
    $count = DB::table($name)->count();
    echo sprintf("  %4d | %s\n", $count, $name);
}

echo PHP_EOL . "--- TABLES WITH 100+ ROWS ---" . PHP_EOL;
