<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Business Instance Config ===\n";
$instances = DB::table('business_instances')->get();
foreach ($instances as $i) {
    echo "Instance ID: {$i->id}, config: {$i->configuracion}\n";
    
    $config = json_decode($i->configuracion ?? '{}', true);
    echo "  restaurante_valida_stock: " . ($config['restaurante_valida_stock'] ?? 'NOT SET') . "\n";
}