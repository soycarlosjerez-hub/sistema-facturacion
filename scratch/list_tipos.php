<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tipos = \App\Models\TipoVenta::all();
foreach($tipos as $t) {
    echo $t->id . " - " . $t->nombre . "\n";
}
