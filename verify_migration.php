<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Categories count: " . App\Models\Category::count() . PHP_EOL;
echo "Categorizables count: " . DB::table('categorizables')->count() . PHP_EOL;

$categories = App\Models\Category::with('businessTypes')->get();
foreach ($categories as $c) {
    $types = $c->businessTypes->pluck('key')->implode(', ');
    echo "{$c->id}: {$c->nombre} - color: {$c->color} - icono: {$c->icono} - orden: {$c->orden} - types: {$types}" . PHP_EOL;
}

echo PHP_EOL . "Productos with categoria_id:" . PHP_EOL;
$productos = DB::table('productos')->select('id', 'nombre', 'categoria_id')->get();
foreach ($productos as $p) {
    $catName = DB::table('categories')->where('id', $p->categoria_id)->value('nombre');
    echo "  {$p->id}: {$p->nombre} -> categoria_id: {$p->categoria_id} ({$catName})" . PHP_EOL;
}

echo PHP_EOL . "Mesas with categoria_id:" . PHP_EOL;
$mesas = DB::table('mesas')->select('id', 'nombre', 'categoria_id')->get();
foreach ($mesas as $m) {
    $catName = $m->categoria_id ? DB::table('categories')->where('id', $m->categoria_id)->value('nombre') : 'NULL';
    echo "  {$m->id}: {$m->nombre} -> categoria_id: {$m->categoria_id} ({$catName})" . PHP_EOL;
}