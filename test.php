<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = App\Models\BusinessType::all();
foreach ($types as $t) {
    echo $t->slug . ' modulos: ' . implode(', ', App\Models\BusinessType::getModulosVisibles($t->slug)) . PHP_EOL;
}
