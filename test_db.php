<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
echo "Database name: " . DB::connection()->getDatabaseName() . "\n";

$columns = DB::select("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'productos'");
echo "productos columns found: " . count($columns) . "\n";