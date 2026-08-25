<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$count = \App\Models\BusinessInstance::count();
echo 'Total instances: ' . $count . PHP_EOL;
$instances = \App\Models\BusinessInstance::take(5)->get();
foreach ($instances as $i) {
    echo $i->id . ' - ' . $i->name . PHP_EOL;
}
?>