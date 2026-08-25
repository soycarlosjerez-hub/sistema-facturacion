<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = \App\Models\Producto::where('tenant_id', 10)->get();
echo "Productos en instancia 10: " . $products->count() . PHP_EOL;
foreach ($products as $p) {
    echo " - " . $p->nombre . " - Imagen: " . $p->imagen . PHP_EOL;
}
?>