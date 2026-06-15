<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$types = DB::table('business_types')->get(['id', 'key', 'nombre']);
foreach ($types as $t) {
    echo "{$t->id}: key={$t->key}, nombre={$t->nombre}\n";
}