<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== All categories ===" . PHP_EOL;
$cats = DB::table('categories')->get();
foreach ($cats as $c) {
    echo "ID: {$c->id}, nombre: {$c->nombre}, color: {$c->color}, icono: {$c->icono}, orden: {$c->orden}" . PHP_EOL;
}

echo PHP_EOL . "=== Categorizables ===" . PHP_EOL;
$cats = DB::table('categorizables')
    ->join('categories', 'categorizables.category_id', '=', 'categories.id')
    ->join('business_types', 'categorizables.categorizable_id', '=', 'business_types.id')
    ->select('categories.id', 'categories.nombre as cat_nombre', 'business_types.key as bt_key', 'business_types.nombre as bt_nombre')
    ->get();
foreach ($cats as $c) {
    echo "Cat ID: {$c->id}, Cat: {$c->cat_nombre}, BT: {$c->bt_key} ({$c->bt_nombre})" . PHP_EOL;
}