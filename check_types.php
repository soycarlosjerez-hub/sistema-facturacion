<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = \App\Models\BusinessType::all();
foreach ($types as $t) {
    echo $t->id . ' - ' . $t->nombre . PHP_EOL;
}
?>