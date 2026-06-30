<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$table = 'productos';
$columns = DB::select("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = 'sistema_facturacion' AND table_name = '$table'");
echo "Columns for $table: " . count($columns) . "\n";
$hasTenantId = collect($columns)->contains(fn($col) => $col->COLUMN_NAME === 'tenant_id');
echo "Has tenant_id: " . ($hasTenantId ? 'YES' : 'NO') . "\n";