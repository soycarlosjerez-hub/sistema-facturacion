<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$type = \App\Models\BusinessType::find(1);
$type->config = ['facturacion_modo' => 'productos'];
$type->save();
echo 'Config updated: ' . json_encode($type->config) . PHP_EOL;
?>