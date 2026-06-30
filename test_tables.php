<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("SHOW TABLES");
echo "Actual table names in database:\n";
foreach ($tables as $t) {
    $values = array_values((array)$t);
    echo "  - " . $values[0] . "\n";
}