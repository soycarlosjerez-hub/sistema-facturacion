<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Debug scopeOfType
echo "=== Debug scopeOfType ===" . PHP_EOL;

// Check raw query
$query = App\Models\Category::query();
$query->whereHas('businessTypes', function ($q) {
    $q->where('business_types.key', 'retail');
});
echo "Retail SQL: " . $query->toSql() . PHP_EOL;
echo "Bindings: " . json_encode($query->getBindings()) . PHP_EOL;
$retailCats = $query->get();
echo "Results: " . $retailCats->count() . PHP_EOL;
foreach ($retailCats as $c) {
    echo "  {$c->id}: {$c->nombre}" . PHP_EOL;
}

// Check mesa 16
echo PHP_EOL . "=== Mesa 16 ===" . PHP_EOL;
$mesa = App\Models\Mesa::find(16);
echo "Mesa 16 categoria_id: " . $mesa->categoria_id . PHP_EOL;
if ($mesa->categoria_id) {
    $cat = App\Models\Category::find($mesa->categoria_id);
    echo "Category: " . ($cat ? $cat->nombre : 'NOT FOUND') . PHP_EOL;
}

// Check relationship directly
echo PHP_EOL . "=== Direct relationship ===" . PHP_EOL;
$cat = App\Models\Category::find(5);
if ($cat) {
    echo "Category 5: {$cat->nombre}" . PHP_EOL;
    $types = $cat->businessTypes;
    echo "BusinessTypes count: " . $types->count() . PHP_EOL;
    foreach ($types as $bt) {
        echo "  -> {$bt->key} ({$bt->nombre})" . PHP_EOL;
    }
}