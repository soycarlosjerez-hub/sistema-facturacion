<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Producto;

echo "Seeding products..." . PHP_EOL;

$products = [
    ['nombre' => 'Producto Test 1', 'precio' => 10.00, 'stock' => 100, 'tenant_id' => 1],
    ['nombre' => 'Producto Test 2', 'precio' => 20.00, 'stock' => 100, 'tenant_id' => 1],
    ['nombre' => 'Producto Test 3', 'precio' => 30.00, 'stock' => 100, 'tenant_id' => 1],
    ['nombre' => 'Producto Test 4', 'precio' => 40.00, 'stock' => 100, 'tenant_id' => 1],
    ['nombre' => 'Producto Test 5', 'precio' => 50.00, 'stock' => 100, 'tenant_id' => 1],
];

foreach ($products as $p) {
    Producto::updateOrCreate(
        ['nombre' => $p['nombre'], 'tenant_id' => $p['tenant_id']],
        ['precio' => $p['precio'], 'stock' => $p['stock']]
    );
}

echo "Done! Created/Updated " . count($products) . " products." . PHP_EOL;

