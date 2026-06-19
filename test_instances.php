<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Instances: ' . PHP_EOL;
foreach (App\Models\BusinessInstance::all() as $i) {
    echo $i->id . ' - ' . $i->nombre . ' - ' . $i->slug . ' - ' . $i->business_type_id . PHP_EOL;
    if ($i->businessType) {
        echo '  Type: ' . $i->businessType->slug . PHP_EOL;
    }
}