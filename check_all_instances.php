<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$instances = \App\Models\BusinessInstance::all();
foreach ($instances as $i) {
    echo 'ID: ' . $i->id . ' - Nombre: ' . $i->nombre . PHP_EOL;
}
?>