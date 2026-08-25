<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$instances = \App\Models\BusinessInstance::all();
foreach ($instances as $i) {
    $count = \App\Models\Producto::where('tenant_id', $i->id)->count();
    echo "Instancia ID " . $i->id . ": " . $i->nombre . " - Productos: " . $count . PHP_EOL;
}
?>