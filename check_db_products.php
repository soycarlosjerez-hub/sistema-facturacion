<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Count: " . \App\Models\Producto::count() . PHP_EOL;
foreach (\App\Models\Producto::all() as $p) {
    echo "- {$p->nombre} (ID: {$p->id})" . PHP_EOL;
}
