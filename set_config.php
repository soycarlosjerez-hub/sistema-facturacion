<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$instance = DB::table('business_instances')->find(1);
if ($instance) {
    $config = json_decode($instance->configuracion ?? '{}', true);
    $config['restaurante_valida_stock'] = '1';  // Explicitly enable stock validation
    DB::table('business_instances')
        ->where('id', 1)
        ->update(['configuracion' => json_encode($config)]);
    echo "Config updated: restaurante_valida_stock = '1'\n";
} else {
    echo "Instance not found\n";
}