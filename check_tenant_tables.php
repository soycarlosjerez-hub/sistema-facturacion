<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SHOW TABLES');
foreach ($tables as $t) {
    $tbl = array_values((array)$t)[0];
    $cols = array_column(DB::select("SHOW COLUMNS FROM `{$tbl}`"), 'Field');
    if (in_array('tenant_id', $cols)) {
        echo $tbl . PHP_EOL;
    }
}
