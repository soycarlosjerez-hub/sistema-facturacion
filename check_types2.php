<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$count = \App\Models\BusinessType::count();
echo 'Types: ' . $count . PHP_EOL;
$types = \App\Models\BusinessType::take(5)->get();
foreach ($types as $t) {
    echo $t->id . ' - ' . $t->nombre . PHP_EOL;
}
?>