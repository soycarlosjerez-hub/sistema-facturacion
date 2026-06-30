<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating stock for all products...\n";

$updated = DB::table('productos')
    ->where('tenant_id', 1)
    ->update(['stock' => 100]);

echo "Updated {$updated} products with stock = 100\n";

echo "\nVerifying...\n";
$products = DB::table('productos')->select('id', 'tenant_id', 'nombre', 'stock')->get();
foreach ($products as $p) {
    echo "ID: {$p->id}, tenant_id: {$p->tenant_id}, stock: {$p->stock}, nombre: {$p->nombre}\n";
}