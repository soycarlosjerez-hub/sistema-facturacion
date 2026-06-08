<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$columns = DB::getSchemaBuilder()->getColumnListing('ventas');
print_r($columns);