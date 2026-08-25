<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$instances = \App\Models\BusinessInstance::all();
foreach ($instances as $i) {
    echo 'Instancia ' . $i->id . ': ' . $i->nombre . ' - business_type_id: ' . $i->business_type_id . PHP_EOL;
}
?>