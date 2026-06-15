<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Old mesa_categorias ===" . PHP_EOL;
$old = DB::table('mesa_categorias')->get();
foreach ($old as $c) {
    echo "{$c->id}: {$c->nombre} - color: {$c->color} - icono: {$c->icono} - orden: {$c->orden}" . PHP_EOL;
}

echo PHP_EOL . "=== New categories (restaurant type) ===" . PHP_EOL;
$new = DB::table('categories')
    ->join('categorizables', 'categories.id', '=', 'categorizables.category_id')
    ->where('categorizables.categorizable_type', 'App\Models\BusinessType')
    ->join('business_types', 'categorizables.categorizable_id', '=', 'business_types.id')
    ->where('business_types.key', 'restaurante')
    ->get();
foreach ($new as $c) {
    echo "{$c->id}: {$c->nombre} - color: {$c->color} - icono: {$c->icono} - orden: {$c->orden}" . PHP_EOL;
}

echo PHP_EOL . "=== Mesas with old categoria_id ===" . PHP_EOL;
$mesas = DB::table('mesas')->get(['id', 'nombre', 'categoria_id']);
foreach ($mesas as $m) {
    if ($m->categoria_id) {
        $oldName = DB::table('mesa_categorias')->where('id', $m->categoria_id)->value('nombre');
        echo "Mesa {$m->id} ({$m->nombre}) -> old categoria_id: {$m->categoria_id} ({$oldName})" . PHP_EOL;
    } else {
        echo "Mesa {$m->id} ({$m->nombre}) -> old categoria_id: NULL" . PHP_EOL;
    }
}