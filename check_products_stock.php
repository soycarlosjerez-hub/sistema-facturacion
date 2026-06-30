<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Products stock check ===\n";
$products = DB::table('productos')->select('id', 'tenant_id', 'nombre', 'stock')->get();
foreach ($products as $p) {
    echo "ID: {$p->id}, tenant_id: {$p->tenant_id}, stock: {$p->stock}, nombre: {$p->nombre}\n";
}