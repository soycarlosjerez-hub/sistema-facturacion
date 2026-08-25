<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Update business type config to lavados mode
$type = \App\Models\BusinessType::find(1);
$type->config = ['facturacion_modo' => 'lavados'];
$type->save();
echo 'Config updated to: ' . json_encode($type->config) . PHP_EOL;

// Also check instance 10 products and mark some as lavado
$products = \App\Models\Producto::where('tenant_id', 10)->take(5)->get();
foreach ($products as $p) {
    echo "Producto: " . $p->nombre . " - categoria_id: " . $p->categoria_id . PHP_EOL;
}
?>