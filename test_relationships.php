<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test Category -> businessTypes relationship
echo "=== Category -> businessTypes ===" . PHP_EOL;
$cat = App\Models\Category::with('businessTypes')->find(1);
if ($cat) {
    echo "Category 1: {$cat->nombre}" . PHP_EOL;
    foreach ($cat->businessTypes as $bt) {
        echo "  -> {$bt->key} ({$bt->nombre})" . PHP_EOL;
    }
}

// Test scopeOfType
echo PHP_EOL . "=== scopeOfType('retail') ===" . PHP_EOL;
$retailCats = App\Models\Category::ofType('retail')->get();
foreach ($retailCats as $c) {
    echo "  {$c->id}: {$c->nombre}" . PHP_EOL;
}

echo PHP_EOL . "=== scopeOfType('restaurante') ===" . PHP_EOL;
$restCats = App\Models\Category::ofType('restaurante')->get();
foreach ($restCats as $c) {
    echo "  {$c->id}: {$c->nombre}" . PHP_EOL;
}

// Test BusinessType -> categories
echo PHP_EOL . "=== BusinessType -> categories ===" . PHP_EOL;
$bt = App\Models\BusinessType::where('key', 'restaurante')->first();
if ($bt) {
    echo "BusinessType: {$bt->key} ({$bt->nombre})" . PHP_EOL;
    foreach ($bt->categories as $c) {
        echo "  -> {$c->id}: {$c->nombre}" . PHP_EOL;
    }
}

// Test getConfigForType
echo PHP_EOL . "=== getConfigForType ===" . PHP_EOL;
$cat = App\Models\Category::find(5); // Interior
if ($cat) {
    echo "Category: {$cat->nombre}" . PHP_EOL;
    echo "Color for restaurante: " . $cat->getColorForType('restaurante') . PHP_EOL;
    echo "Icon for restaurante: " . $cat->getIconForType('restaurante') . PHP_EOL;
    echo "Orden for restaurante: " . $cat->getOrdenForType('restaurante') . PHP_EOL;
}

// Test Producto relationship
echo PHP_EOL . "=== Producto -> Category ===" . PHP_EOL;
$prod = App\Models\Producto::with('categoria')->find(1);
if ($prod) {
    echo "Producto: {$prod->nombre}" . PHP_EOL;
    echo "Categoria: {$prod->categoria->nombre}" . PHP_EOL;
}

// Test Mesa relationship
echo PHP_EOL . "=== Mesa -> Category ===" . PHP_EOL;
$mesa = App\Models\Mesa::with('categoria')->find(16);
if ($mesa) {
    echo "Mesa: {$mesa->nombre}" . PHP_EOL;
    echo "Categoria: " . ($mesa->categoria ? $mesa->categoria->nombre : 'NULL') . PHP_EOL;
}