<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::statement('UPDATE business_types SET `key` = slug WHERE `key` = "" OR `key` IS NULL');
echo "Updated key column\n";

$results = DB::table('business_types')->get(['id', 'key', 'slug', 'nombre']);
foreach ($results as $r) {
    echo "{$r->id}: key='{$r->key}', slug='{$r->slug}', nombre='{$r->nombre}'\n";
}