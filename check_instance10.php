<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\BusinessInstance::count();
echo 'Instances: ' . $count . PHP_EOL;

$instance = \App\Models\BusinessInstance::find(10);
if ($instance) {
    echo 'Instance 10 found: ' . $instance->nombre . PHP_EOL;
} else {
    echo 'Instance 10 NOT found' . PHP_EOL;
}
?>