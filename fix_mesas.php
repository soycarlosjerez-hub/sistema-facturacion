<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Mesas before fix ===" . PHP_EOL;
$mesas = DB::table('mesas')->get(['id', 'nombre', 'categoria_id']);
foreach ($mesas as $m) {
    if ($m->categoria_id) {
        $catName = DB::table('categories')->where('id', $m->categoria_id)->value('nombre');
        echo "Mesa {$m->id} ({$m->nombre}) -> categoria_id: {$m->categoria_id} ({$catName})" . PHP_EOL;
    } else {
        echo "Mesa {$m->id} ({$m->nombre}) -> categoria_id: NULL" . PHP_EOL;
    }
}

// Fix: map old mesa_categorias IDs (1-4) to new categories IDs (5-8) by nombre
$oldToNew = [
    1 => 5, // Interior
    2 => 6, // Terraza
    3 => 7, // VIP
    4 => 8, // Barra
];

foreach ($oldToNew as $oldId => $newId) {
    DB::table('mesas')->where('categoria_id', $oldId)->update(['categoria_id' => $newId]);
    echo "Updated mesas with old categoria_id {$oldId} -> {$newId}" . PHP_EOL;
}

echo PHP_EOL . "=== Mesas after fix ===" . PHP_EOL;
$mesas = DB::table('mesas')->get(['id', 'nombre', 'categoria_id']);
foreach ($mesas as $m) {
    if ($m->categoria_id) {
        $catName = DB::table('categories')->where('id', $m->categoria_id)->value('nombre');
        echo "Mesa {$m->id} ({$m->nombre}) -> categoria_id: {$m->categoria_id} ({$catName})" . PHP_EOL;
    } else {
        echo "Mesa {$m->id} ({$m->nombre}) -> categoria_id: NULL" . PHP_EOL;
    }
}